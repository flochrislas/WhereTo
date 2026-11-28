#!/usr/bin/env bash
# Builds the WhereTo APK from a site export, inside a podman container, so that the host needs no
# Android SDK and no JDK. Usage:
#
#   ./build-apk.sh [export-dir]     (default: export/, made by "npm run export")
#
# Ends with build/whereto-<date>.apk, a debug build signed with the keystore of .keystore/, which this
# script creates on its first run and reuses afterwards so that each new APK installs over the last one.
set -euo pipefail

here=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
image=localhost/whereto-android-build:1
# The Gradle cache (the wrapper's Gradle, the plugin, the libraries) survives between builds in this volume
gradle_volume=whereto-android-gradle

die() {
    echo "build-apk: $*" >&2
    exit 1
}

export_dir=${1:-$here/export}
[ -d "$export_dir" ] || die "no export at $export_dir: run 'npm run export' first, or give an export folder"
export_dir=$(cd "$export_dir" && pwd)
[ -f "$export_dir/index.html" ] || die "$export_dir has no index.html: is it a site export?"
command -v podman > /dev/null || die "podman is needed"

# The date of the data is the version of the app. meta.json is written by the export; a hand-made test
# export may have none, and then the time of the build stands in for it.
meta=$export_dir/app-data/meta.json
export_date=
if [ -f "$meta" ]; then
    export_date=$(sed -n 's/.*"exportDate"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$meta" | head -n 1)
fi
if [ -z "$export_date" ]; then
    export_date=$(date -u +%Y-%m-%dT%H:%M:%SZ)
    echo "build-apk: no exportDate in $meta, using the current time: $export_date" >&2
fi
day=${export_date:0:10}
[[ $day =~ ^[0-9]{4}-[0-9]{2}-[0-9]{2}$ ]] || die "unexpected exportDate '$export_date' (ISO 8601 expected)"

# The site goes into the APK's assets, replacing whatever an earlier build left there
assets=$here/android/app/src/main/assets/site
rm -rf "$assets"
mkdir -p "$assets"
cp -R "$export_dir/." "$assets/"

# The image has no COPY, so it is built from an empty context rather than this folder and its export.
# Unchanged, every step comes from podman's cache and this takes a second.
context=$(mktemp -d)
trap 'rm -rf "$context"' EXIT
podman build --quiet --tag "$image" --file "$here/Containerfile" "$context" > /dev/null

mkdir -p "$here/.keystore" "$here/build"

# keep-id runs the build as the host user, so the files it writes in the mounted folder are the user's.
# The commands inside: create the keystore once (never printed), run the JVM unit tests of the path
# rule, then build the debug APK. The Gradle wrapper fetches its own Gradle into the cached volume.
podman run --rm \
    --userns=keep-id \
    --volume "$here:/work" \
    --volume "$gradle_volume:/gradle-cache" \
    --workdir /work/android \
    --env EXPORT_DATE="$export_date" \
    "$image" \
    bash -c '
        set -euo pipefail
        keystore=/work/.keystore/debug.keystore
        if [ ! -f "$keystore" ]; then
            keytool -genkeypair -keystore "$keystore" -storepass android -keypass android \
                -alias androiddebugkey -keyalg RSA -keysize 2048 -validity 10000 \
                -dname "CN=WhereTo Debug,O=WhereTo,C=JP" > /dev/null 2>&1
            chmod 600 "$keystore"
            echo "build-apk: created a new debug keystore in .keystore/"
        fi
        ./gradlew --no-daemon --console=plain --warning-mode=summary \
            -PexportDate="$EXPORT_DATE" -PkeystoreFile="$keystore" \
            testDebugUnitTest assembleDebug
    '

apk=$here/build/whereto-$day.apk
cp "$here/android/app/build/outputs/apk/debug/app-debug.apk" "$apk"

echo
echo "APK: $apk ($(du -h "$apk" | cut -f1), version $export_date)"
echo "Install it on the phone connected by USB with:"
echo "  adb install -r $apk"
