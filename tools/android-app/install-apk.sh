#!/usr/bin/env bash
# Installs the WhereTo APK on the phone plugged in by USB. Usage:
#
#   ./install-apk.sh [apk]     (default: the most recent build/whereto-<date>.apk)
#
# It can be started before the phone is plugged in: it waits for it, and for the "Allow USB debugging?"
# answer on the phone. The app is installed over the one already there ("adb install -r"), which keeps
# its saved theme and choices: nothing is uninstalled.
# With several phones or emulators attached, name one: ANDROID_SERIAL=<serial> ./install-apk.sh
set -euo pipefail

here=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
package=io.github.flochrislas.whereto

die() {
    echo "install-apk: $*" >&2
    exit 1
}

# adb from the PATH, else from the Android SDK (Android Studio and Flutter put it there, outside the PATH)
if ! command -v adb > /dev/null; then
    for sdk in "${ANDROID_HOME:-}" "${ANDROID_SDK_ROOT:-}" "$HOME/Android/Sdk"; do
        if [ -n "$sdk" ] && [ -x "$sdk/platform-tools/adb" ]; then
            PATH=$sdk/platform-tools:$PATH
            break
        fi
    done
fi
command -v adb > /dev/null || die "adb is needed: sudo apt install adb"

apk=${1:-}
if [ -z "$apk" ]; then
    # The names hold the date of the export, so the last one in order is the most recent
    shopt -s nullglob
    apks=("$here"/build/whereto-*.apk)
    shopt -u nullglob
    [ ${#apks[@]} -gt 0 ] || die "no APK in $here/build: run 'npm run build' first"
    apk=${apks[-1]}
fi
[ -f "$apk" ] || die "no such file: $apk"

# The attached devices in a given state ("device": ready; "unauthorized": the phone has not allowed this laptop)
devices() {
    adb devices | awk -v state="$1" 'NR > 1 && $2 == state { print $1 }'
}

adb start-server > /dev/null 2>&1 || die "the adb server does not start"

if [ -z "${ANDROID_SERIAL:-}" ]; then
    said=
    while :; do
        mapfile -t ready < <(devices device)
        if [ ${#ready[@]} -eq 1 ]; then
            break
        elif [ ${#ready[@]} -gt 1 ]; then
            die "several devices are attached (${ready[*]}): choose one with ANDROID_SERIAL=<serial>"
        fi
        if [ -n "$(devices unauthorized)" ]; then
            message='Answer "Allow" to "Allow USB debugging?" on the phone...'
        else
            message='Waiting for the phone: plug it in, with USB debugging on (see README.md)...'
        fi
        if [ "$message" != "$said" ]; then
            echo "$message"
            said=$message
        fi
        sleep 1
    done
    export ANDROID_SERIAL=${ready[0]}
else
    echo "Waiting for the device $ANDROID_SERIAL..."
    adb wait-for-device
fi

echo "Installing $(basename "$apk") on $(adb shell getprop ro.product.model | tr -d '\r') ($ANDROID_SERIAL)..."
if ! output=$(adb install -r "$apk" 2>&1); then
    echo "$output" >&2
    case $output in
        *INSTALL_FAILED_UPDATE_INCOMPATIBLE*)
            die "the installed app was signed with another key. Uninstall it first (its saved theme and choices are lost): adb uninstall $package"
            ;;
        *INSTALL_FAILED_VERSION_DOWNGRADE*)
            die "the installed app is more recent than this APK. To install the older one anyway: adb install -r -d '$apk'"
            ;;
        *) die "the installation failed" ;;
    esac
fi

version=$(adb shell dumpsys package "$package" | sed -n 's/.*versionName=//p' | head -n 1 | tr -d '\r')
echo "Installed: WhereTo ${version:-(version unknown)}"
