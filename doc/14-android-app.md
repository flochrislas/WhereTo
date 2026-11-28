# Proposal for an Android app

Package the existing WhereTo site for Google Play using a **Trusted Web Activity (TWA)**. A visitor installs WhereTo from Play and taps its launcher icon. A compatible browser opens the verified WhereTo website full screen. Laravel continues to serve the English and Japanese pages, run the searches and calculate lunch suggestions. A correction to a restaurant or a tag reaches users through the next web deployment. [Chrome’s TWA quick start](https://developer.chrome.com/docs/android/trusted-web-activity/quick-start)

## Build a first Play release

1. **Try the web app on a phone first.** Open the production HTTPS site in Chrome, install it from the browser menu and run through a search. WhereTo already has a standalone manifest, launcher icons, shareable place URLs and a service worker. If a Home Screen icon is the whole goal, browser installation is sufficient. A Play listing is the reason to build the Android package. [Web manifest](../source/public/manifest.json) · [Offline behavior](04-how-it-works.md#10-installable-app-and-offline-service-worker)
2. **Generate the Android project.** Choose the permanent application ID and canonical production hostname. Run Bubblewrap’s `init` against the live `/manifest.json`, review the name, icons and launch URL, then run `bubblewrap build`. Keep the generated project in a proposed `mobile/android/` directory and store signing credentials outside Git. Use the signed Android App Bundle for Play’s internal testing track. [Bubblewrap](https://github.com/GoogleChromeLabs/bubblewrap)
3. **Associate the domain with the distributed app.** Serve `/.well-known/assetlinks.json` over HTTPS as JSON with a direct 200 response. Include the application ID, the `delegate_permission/common.handle_all_urls` relation and the SHA-256 fingerprint of the **Play app signing certificate**. The upload certificate can differ. Give this URL its own cache rule because the deployment example caches all JSON for a year and a signing change needs to propagate. A browser toolbar on the canonical domain is a reason to check verification. [Android website associations](https://developer.android.com/training/app-links/configure-assetlinks) · [WhereTo deployment](06-deploy-production.md)
4. **Prepare the listing and privacy information.** Describe location use accurately. WhereTo sends coordinates to Laravel in request URLs to calculate nearby results and suggestions. Check what the production access logs retain before completing Data safety and publishing the privacy policy. Confirm the target Android API and any closed-testing requirements for the actual Play account when preparing the release. [Play Data safety](https://support.google.com/googleplay/android-developer/answer/10787469) · [Account testing requirements](https://support.google.com/googleplay/android-developer/answer/14151465)

## Verify the Play-installed build

Install from the internal track on a real phone so the test exercises Google's signing certificate. Open a shared Japanese place link, switch to English, follow the Maps link and use Android Back. Allow location once, then repeat after denying it and check that the list uses Shibuya Station as its reference point.

Before going offline, open a filtered list and a place. Reopen them in airplane mode, then try a search the phone has never seen. The service worker caches the app shell and a bounded history of responses. The listing should promise access to recently visited content only after verifying that flow. Future changes to signing, launcher artwork or Android SDK support require maintaining the Android package alongside the site.

## A stand-alone APK that needs no server

The Trusted Web Activity above is a window onto the deployed site: without the server it shows nothing new. A second kind of package carries the site **and its data** inside the APK. It is copied to a phone and installed by hand (no Play listing, no domain, no HTTPS), and it works in airplane mode. It is the quick way to carry WhereTo around before anything is deployed. It can also go to Play later, as an ordinary app.

### What has to move into the phone

An APK cannot run Laravel, so everything PHP answers at request time must be either prepared when the app is built, or done by JavaScript in the page. The page asks the server for three things only (`fetch` in `whereto.js`):

| Request | What PHP does | In the stand-alone app |
|---|---|---|
| The results list (`loadAllResults`, `ajaxResults`) | filters the places by tags with one of four operators (AND, OR, AND NOT, OR NOT), orders them by ratings or cost, computes each distance and sorts by it when asked (`App\Services\PlaceSearch`) | done in JavaScript, on rows and orders prepared by the server at build time |
| The details of a place (`ajaxDetails`) | renders one Blade view, the same for every visitor | a plain file, exported at build time |
| The lunch suggestion (`ajaxSuggestion`) | between 11:30 and 14:00 in Tokyo, picks at random among the 35 nearest of the 100 best restaurants (`SuggestionController`) | done in JavaScript |

Everything else is already static: the CSS, the fonts, the tag icons sprite, `whereto.js` itself.

### Build it

Built in November 2026 from the plan in [`16-standalone-apk-plan.md`](16-standalone-apk-plan.md); the tool is [`tools/android-app/`](../tools/android-app/README.md) and the request flow is section 11 of [`04-how-it-works.md`](04-how-it-works.md). In short:

1. **Export the site with Laravel itself.** An Artisan command (`php artisan app:export-static <dir>`) asks the application for every page and fragment, in both languages, and writes the answers as files, with root-relative URLs so that they work at any origin. It works on a database of its own, freshly seeded, and refuses any other. It also writes, per kind, the ids of the places in the two orders the server would return (by ratings, by cost), with their tags and coordinates. The app therefore shows exactly the HTML of the site, and a change to a view reaches both.
2. **Compute only what depends on the visitor.** A script, `public/js/whereto-local.js`, filters the ids by tags, computes the distances with the server's formula, sorts by distance when asked, and assembles the results from the rows the server rendered. It does not re-implement the ordering of the database. It also makes the lunch suggestion and decides, from the phone's clock, whether its button shows: the site decides that when the page is rendered, which in an app would be the time of the build.
3. **Give `whereto.js` one seam.** Its four `fetch` calls go through one function, `loadFragment()`. On the site it requests the server as now; in the app it asks the local script first. The details of a place are fetched as before: they are files of the app.
4. **Show it in a small Android app.** One screen holding a WebView that serves the exported files from inside the package, at a fixed secure origin (`WebViewAssetLoader`), mapping `/ja/restaurants` to its `index.html`. It asks for the location permission, opens Google Maps and Tabelog in their own apps, follows the system's dark or light theme, and needs no network permission of its own. Android's Back closes an open place before it leaves the page. "Copy URL" is hidden: a place in the app has no address to share.
5. **One command.** The tool `tools/android-app/`: `npm run build` exports the site from the seeders, checks the app's answers against the site's own on the same data, builds the APK in a container (so only podman and `adb` are needed on the laptop) and leaves it in `build/`. `adb install -r` puts it on the phone.

### What it costs

- **Data reaches the phone by reinstalling.** A corrected restaurant is in the app only after a new export, a new build and a new install. The version shown in the app should carry the date of the export.
- **Two searches to keep in agreement.** The PHP one serves the site, the JavaScript one the app. The JavaScript one takes its orders from the server, and a browser test compares what it builds with the site's own answers on the same database, for a table of requests.
- **A build toolchain.** The Android SDK and a JDK (a few gigabytes), kept in a container image. Nothing is added to the server or to the site's own dependencies.
- **No service worker needed.** The files are in the package, so the offline cache of the site has no role in this app; the export should leave `sw.js` out.
- **Not for search engines or sharing.** A place opened in the app has no public URL.

### Ruled out

- **PHP inside the app.** Builds of PHP for Android exist, but the app would carry an interpreter, a web server and a database for a job that a few hundred lines of JavaScript do.
- **Static pages only, no search in JavaScript.** Every combination of tags, operator and order would have to be generated ahead of time; there are far too many, so the filter would not work.
- **Saving the deployed site with its service worker.** It needs the deployed site, and only keeps what was visited.

### What the tests cover, and what only a phone can

`npm test` in the tool runs Node tests of the path rule and of the pure functions of `whereto-local.js` (lunch time to the second in Tokyo time from any time zone, the tag edge cases, the positions PHP accepts and refuses, the places without coordinates, the random pick), then a browser spec on the desktop preview of the export: the pages in both languages, a place by its address, `whereToBack()`, the suggestion at 09:00 and 12:00 with a fake clock, location denied, and, for 288 requests (every kind, both languages, the operators, orders and positions of the table), the same rows in the same order with the same walking times as the site's own answer on the same database. Nothing the page requests may leave the preview.

The Android side (the asset loader and its 404, the bridge, Back, the theme, the restoration after the process is killed, the location prompt, the absence of the `INTERNET` permission) can only be checked on a phone: see the acceptance list of `16-standalone-apk-plan.md`, section 7.

### Verify

Install the APK on a real phone, turn on airplane mode before the first launch, and check: each of the four kinds lists its places; a filter with two tags and each operator gives the same places, in the same order, as the preview of the same export on the laptop (the dev site has other ids and may have other data); the three sort orders; a place opens with its details and icons, in English and in Japanese; the distances start from the phone with location allowed and from Shibuya Station with it denied; at lunch time in Tokyo the home page makes a suggestion.
