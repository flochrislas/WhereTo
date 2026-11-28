package io.github.flochrislas.whereto;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.content.res.AssetManager;
import android.content.res.Configuration;
import android.graphics.drawable.ColorDrawable;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.ViewGroup;
import android.webkit.GeolocationPermissions;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.FrameLayout;
import androidx.activity.ComponentActivity;
import androidx.activity.EdgeToEdge;
import androidx.activity.OnBackPressedCallback;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.core.content.ContextCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.webkit.WebViewAssetLoader;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Collections;
import java.util.Map;

/**
 * The whole app: one WebView showing the exported site, served from the APK's assets at
 * https://appassets.androidplatform.net/ (a secure origin, which the page's geolocation needs).
 * No request ever leaves the phone: the app has no INTERNET permission, unknown paths and other hosts
 * get a 404 from here, and external links are handed to other apps.
 */
public class MainActivity extends ComponentActivity {

    private static final String TAG = "WhereTo";
    /** The default domain of WebViewAssetLoader, reserved for apps that serve their own files */
    static final String HOST = WebViewAssetLoader.DEFAULT_DOMAIN;
    static final String HOME = "https://" + HOST + "/";
    /** Where build-apk.sh copies the export, under src/main/assets/ */
    private static final String SITE_ROOT = "site/";
    private static final String PREFS = "whereto";
    private static final String PREF_LAST_URL = "lastUrl";

    private WebView webView;
    private WebViewAssetLoader assetLoader;
    private OnBackPressedCallback backCallback;

    /** True while the page has something that whereToBack() would close (a place's details) */
    private boolean placeOpen;

    /** The page's location request waiting for the answer to the Android permission dialog */
    private String pendingGeoOrigin;
    private GeolocationPermissions.Callback pendingGeoCallback;
    private ActivityResultLauncher<String[]> locationPermission;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Targeting API 35+ draws the app under the system bars whatever we ask, so do it on purpose:
        // bar icons that contrast with the theme, and the page padded out of the bars below.
        EdgeToEdge.enable(this);

        locationPermission = registerForActivityResult(
                new ActivityResultContracts.RequestMultiplePermissions(),
                results -> answerGeolocation(hasLocationPermission()));

        FrameLayout root = new FrameLayout(this);
        webView = new WebView(this);
        root.addView(webView, new FrameLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.MATCH_PARENT));
        ViewCompat.setOnApplyWindowInsetsListener(root, (view, insets) -> {
            Insets bars = insets.getInsets(
                    WindowInsetsCompat.Type.systemBars() | WindowInsetsCompat.Type.displayCutout());
            view.setPadding(bars.left, bars.top, bars.right, bars.bottom);
            return WindowInsetsCompat.CONSUMED;
        });
        setContentView(root);

        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        // The theme choice and the language banner are remembered in localStorage
        settings.setDomStorageEnabled(true);
        settings.setGeolocationEnabled(true);
        // The asset loader serves everything over https; file:// and content:// are never needed
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);

        assetLoader = new WebViewAssetLoader.Builder()
                .addPathHandler("/", new SitePathHandler(getAssets()))
                .build();
        webView.setWebViewClient(new SiteClient());
        webView.setWebChromeClient(new LocationChromeClient());
        // Only the app's own files run in this WebView, so this object is never offered to a foreign page
        webView.addJavascriptInterface(new Bridge(), "WhereToApp");

        backCallback = new OnBackPressedCallback(false) {
            @Override
            public void handleOnBackPressed() {
                goBackInPage();
            }
        };
        getOnBackPressedDispatcher().addCallback(this, backCallback);

        if (savedInstanceState == null) {
            webView.loadUrl(HOME);
        } else if (webView.restoreState(savedInstanceState) == null) {
            // Android killed the process and the WebView could not rebuild its history: reopen the last page.
            // The page rebuilds its filter, order and open place from the query string of its address.
            webView.loadUrl(lastUrl());
        }
        updateBackCallback();
    }

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);
        webView.saveState(outState);
    }

    @Override
    protected void onPause() {
        super.onPause();
        String url = webView.getUrl();
        if (isOwnUrl(url)) {
            getSharedPreferences(PREFS, Context.MODE_PRIVATE).edit().putString(PREF_LAST_URL, url).apply();
        }
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        // The activity is kept on a change of dark mode (configChanges), so its theme is not applied again:
        // the colour behind the bars and the bar icons are set here from the resources of the new mode.
        // (The page itself follows on its own: the WebView updates prefers-color-scheme.)
        getWindow().setBackgroundDrawable(new ColorDrawable(ContextCompat.getColor(this, R.color.bar_background)));
        EdgeToEdge.enable(this);
    }

    @Override
    protected void onDestroy() {
        webView.destroy();
        super.onDestroy();
    }

    private String lastUrl() {
        SharedPreferences prefs = getSharedPreferences(PREFS, Context.MODE_PRIVATE);
        String url = prefs.getString(PREF_LAST_URL, null);
        return isOwnUrl(url) ? url : HOME;
    }

    static boolean isOwnUrl(String url) {
        if (url == null) {
            return false;
        }
        Uri uri = Uri.parse(url);
        return "https".equals(uri.getScheme()) && HOST.equals(uri.getHost());
    }

    // ---- Back -------------------------------------------------------------------------------------------

    /**
     * The back callback must be enabled before the gesture starts, so the app cannot ask the page then.
     * It is enabled when the page said a place is open, or when there is a previous page; disabled, Android
     * does its default and leaves the app.
     */
    private void updateBackCallback() {
        backCallback.setEnabled(placeOpen || webView.canGoBack());
    }

    private void goBackInPage() {
        if (placeOpen) {
            // Opening a place adds no history entry, so closing it is the page's job, not goBack()
            webView.evaluateJavascript("typeof whereToBack === 'function' && whereToBack()", result -> {
                if (!"true".equals(result)) {
                    // The page had nothing to close after all: do what Back would have done without it
                    placeOpen = false;
                    if (webView.canGoBack()) {
                        webView.goBack();
                    }
                    updateBackCallback();
                }
            });
        } else if (webView.canGoBack()) {
            webView.goBack();
        }
    }

    // ---- External links ---------------------------------------------------------------------------------

    /** Hands a link to the app that handles it (Maps, the browser...). No such app: nothing happens. */
    private void openExternally(Uri uri) {
        try {
            startActivity(new Intent(Intent.ACTION_VIEW, uri).addCategory(Intent.CATEGORY_BROWSABLE));
        } catch (ActivityNotFoundException e) {
            Log.w(TAG, "No app to open " + uri);
        }
    }

    static boolean isWebUrl(Uri uri) {
        String scheme = uri.getScheme();
        return ("http".equals(scheme) || "https".equals(scheme)) && uri.getHost() != null
                && !uri.getHost().isEmpty();
    }

    /**
     * The object the page sees as window.WhereToApp. Keep it to these two methods: whatever is added here
     * can be called by any script of the page. They run on a WebView background thread, hence the posts.
     */
    private final class Bridge {
        /** The buttons of a place call window.open(url), which whereto-local.js sends here */
        @JavascriptInterface
        public void openExternal(String url) {
            if (url == null || url.isEmpty()) {
                return;
            }
            Uri uri = Uri.parse(url);
            if (!isWebUrl(uri)) {
                return;
            }
            runOnUiThread(() -> openExternally(uri));
        }

        /** Called by whereToLocal.stateChanged() each time a place opens or closes */
        @JavascriptInterface
        public void setPlaceOpen(boolean open) {
            runOnUiThread(() -> {
                placeOpen = open;
                updateBackCallback();
            });
        }
    }

    // ---- Serving the files ------------------------------------------------------------------------------

    private final class SiteClient extends WebViewClient {
        @Override
        public WebResourceResponse shouldInterceptRequest(WebView view, WebResourceRequest request) {
            WebResourceResponse response = assetLoader.shouldInterceptRequest(request.getUrl());
            // null would let the WebView fetch the address itself: never, whatever the host or scheme
            return response != null ? response : SitePathHandler.notFound();
        }

        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            Uri uri = request.getUrl();
            if (isOwnUrl(uri.toString())) {
                return false;
            }
            // Facebook, Instagram, the Maps links of the rows: open them in their app, never in this WebView
            if (isWebUrl(uri) || "mailto".equals(uri.getScheme()) || "tel".equals(uri.getScheme())
                    || "geo".equals(uri.getScheme())) {
                openExternally(uri);
            }
            return true;
        }

        @Override
        public void onPageStarted(WebView view, String url, android.graphics.Bitmap favicon) {
            // A new document starts with no place open, until its script says otherwise. Not done in
            // doUpdateVisitedHistory: the page's history.replaceState also lands there, right when a place opens.
            placeOpen = false;
            updateBackCallback();
        }

        @Override
        public void doUpdateVisitedHistory(WebView view, String url, boolean isReload) {
            updateBackCallback();
        }
    }

    /**
     * The path rule of SitePaths on the APK's assets, for every path of the app's origin.
     * Never returns null, which would send the request to the network.
     */
    static final class SitePathHandler implements WebViewAssetLoader.PathHandler {
        private final AssetManager assets;

        SitePathHandler(AssetManager assets) {
            this.assets = assets;
        }

        @Override
        public WebResourceResponse handle(String path) {
            // The loader passes Uri.getPath() minus the "/" prefix: decoded, without the query (observed on API 36)
            String file = SitePaths.resolveDecoded(path, this::isFile);
            if (file == null) {
                Log.d(TAG, "404 for path [" + path + "]");
                return notFound();
            }
            try {
                InputStream data = assets.open(SITE_ROOT + file);
                String mime = SitePaths.mimeType(file);
                return new WebResourceResponse(mime, SitePaths.isText(mime) ? "utf-8" : null, data);
            } catch (IOException e) {
                return notFound();
            }
        }

        /** A file, not a folder: AssetManager.open() fails on folders and missing files alike */
        private boolean isFile(String relativePath) {
            try (InputStream ignored = assets.open(SITE_ROOT + relativePath)) {
                return true;
            } catch (IOException e) {
                return false;
            }
        }

        /**
         * A real 404, which the page relies on to close the details of an unknown place. The constructor
         * with a status needs a non-empty reason phrase.
         */
        static WebResourceResponse notFound() {
            Map<String, String> headers = Collections.emptyMap();
            return new WebResourceResponse("text/plain", "utf-8", 404, "Not Found", headers,
                    new ByteArrayInputStream(new byte[0]));
        }
    }

    // ---- Location ---------------------------------------------------------------------------------------

    private boolean hasLocationPermission() {
        return ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED
                || ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION)
                == PackageManager.PERMISSION_GRANTED;
    }

    private void answerGeolocation(boolean allowed) {
        if (pendingGeoCallback != null) {
            // Never retained (third argument): a retained yes is stored by the WebView across restarts, and if
            // the user later takes the permission back in Android's settings, the WebView stops asking us and
            // the page only gets an error. Unretained, each request comes here and checks the permission anew.
            pendingGeoCallback.invoke(pendingGeoOrigin, allowed, false);
            pendingGeoCallback = null;
            pendingGeoOrigin = null;
        }
    }

    private final class LocationChromeClient extends WebChromeClient {
        /** The page asked for the position: ask Android first if the app does not have the permission yet */
        @Override
        public void onGeolocationPermissionsShowPrompt(String origin, GeolocationPermissions.Callback callback) {
            if (!isOwnUrl(origin)) {
                callback.invoke(origin, false, false);
                return;
            }
            if (hasLocationPermission()) {
                callback.invoke(origin, true, false);
                return;
            }
            // A second request while the dialog is up replaces the first: the page only needs one answer
            answerGeolocation(false);
            pendingGeoOrigin = origin;
            pendingGeoCallback = callback;
            locationPermission.launch(new String[] {
                Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION
            });
        }

        @Override
        public void onGeolocationPermissionsHidePrompt() {
            pendingGeoCallback = null;
            pendingGeoOrigin = null;
        }
    }
}
