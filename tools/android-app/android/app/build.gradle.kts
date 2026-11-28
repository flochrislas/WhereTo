import java.time.Duration
import java.time.Instant
import java.time.LocalDate
import java.time.LocalDateTime
import java.time.OffsetDateTime
import java.time.ZoneOffset
import java.time.format.DateTimeParseException

plugins {
    id("com.android.application")
}

// The version of the app is the date of its data. build-apk.sh passes it from app-data/meta.json as
// -PexportDate (ISO 8601, with or without the time and offset); without it, the time of the build.
val exportDate: String = (findProperty("exportDate") as String?)?.takeIf { it.isNotBlank() }
    ?: Instant.now().toString()

fun parseExportDate(text: String): Instant =
    try {
        OffsetDateTime.parse(text).toInstant()
    } catch (e: DateTimeParseException) {
        try {
            LocalDateTime.parse(text).toInstant(ZoneOffset.UTC)
        } catch (e2: DateTimeParseException) {
            LocalDate.parse(text).atStartOfDay().toInstant(ZoneOffset.UTC)
        }
    }

// versionCode must grow with each export so that "adb install -r" accepts a newer build over an older one:
// minutes since 2020, which stays far below Android's limit (2,100,000,000) for thousands of years.
val exportVersionCode: Int =
    Duration.between(Instant.parse("2020-01-01T00:00:00Z"), parseExportDate(exportDate)).toMinutes().toInt()

android {
    namespace = "io.github.flochrislas.whereto"
    // API 36 (Android 16), the latest platform listed as stable on
    // https://developer.android.com/tools/releases/platforms ; AGP 9.5 supports up to 37.
    compileSdk = 36
    buildToolsVersion = "36.0.0"

    defaultConfig {
        // Decided by the user: changing it later installs a second app beside the first
        applicationId = "io.github.flochrislas.whereto"
        // Android 7.0: androidx.webkit 1.16+ requires it, and its WebView is updatable from the Play Store
        minSdk = 24
        // 33+ makes the WebView follow the activity theme for prefers-color-scheme (see themes.xml)
        targetSdk = 36
        versionCode = exportVersionCode
        versionName = exportDate
    }

    signingConfigs {
        getByName("debug") {
            // A keystore kept in tools/android-app/.keystore/ by build-apk.sh, instead of the per-machine
            // ~/.android/debug.keystore, so that every build is signed the same and installs over the last one
            val keystore = findProperty("keystoreFile") as String?
            if (keystore != null) {
                storeFile = file(keystore)
                storePassword = "android"
                keyAlias = "androiddebugkey"
                keyPassword = "android"
            }
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    androidResources {
        // The default pattern also drops folders whose name starts with "_"; the export may have some,
        // and a missing file would be a silent 404 in the app. Hidden files and editor leftovers stay out.
        ignoreAssetsPattern = "!.svn:!.git:!.ds_store:!*.scc:.*:!CVS:!thumbs.db:!picasa.ini:!*~"
    }
}

dependencies {
    // OnBackPressedCallback and ComponentActivity: https://developer.android.com/jetpack/androidx/releases/activity
    implementation("androidx.activity:activity:1.13.0")
    // WebViewAssetLoader: https://developer.android.com/jetpack/androidx/releases/webkit
    implementation("androidx.webkit:webkit:1.18.0")

    testImplementation("junit:junit:4.13.2")
}
