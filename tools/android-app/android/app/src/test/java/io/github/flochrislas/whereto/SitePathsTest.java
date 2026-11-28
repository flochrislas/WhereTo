package io.github.flochrislas.whereto;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;
import static org.junit.Assert.assertNull;
import static org.junit.Assert.assertTrue;

import java.util.Arrays;
import java.util.HashSet;
import java.util.Set;
import org.junit.Test;

/** The path rule, with the same cases as the Node tests of lib/resolve.mjs, on a made-up export */
public class SitePathsTest {

    private static final Set<String> EXPORT = new HashSet<>(Arrays.asList(
            "index.html",
            "ja/index.html",
            "restaurants/index.html",
            "ja/restaurants/index.html",
            "restaurants/details/12/index.html",
            "css/style.css",
            "icons/tags.svg",
            "fonts/font.woff2",
            "app-data/meta.json"));

    private static final SitePaths.Files FILES = EXPORT::contains;

    private static String resolve(String path) {
        return SitePaths.resolve(path, FILES);
    }

    @Test
    public void theRootIsTheHomePage() {
        assertEquals("index.html", resolve("/"));
        // What the asset loader hands to a handler registered on "/": the path without its leading slash
        assertEquals("index.html", resolve(""));
    }

    @Test
    public void aPathWithoutExtensionIsItsIndexFile() {
        assertEquals("ja/restaurants/index.html", resolve("/ja/restaurants"));
        assertEquals("ja/restaurants/index.html", resolve("ja/restaurants"));
        assertEquals("ja/restaurants/index.html", resolve("/ja/restaurants/"));
        assertEquals("ja/index.html", resolve("/ja"));
        assertEquals("restaurants/details/12/index.html", resolve("/restaurants/details/12"));
    }

    @Test
    public void theQueryStringAndFragmentAreIgnored() {
        assertEquals("css/style.css", resolve("/css/style.css?v=1"));
        assertEquals("icons/tags.svg", resolve("/icons/tags.svg?v=1700000000#ramen"));
        assertEquals("restaurants/index.html", resolve("/restaurants?place=12&tags=a,b"));
    }

    @Test
    public void theWayOutOfTheExportIsRefused() {
        // The cases of test/resolve.test.mjs
        for (String path : new String[] {
            "/../x", "/../package.json", "/css/../../export.mjs", "/%2e%2e/package.json",
            "/css/..%2F..%2Fexport.mjs", "..", "/a\\..\\b", "/%E0%A4%A", "../index.html",
        }) {
            assertNull(path, resolve(path));
        }
        // A ".." that would stay inside is refused too: the rule is simple, the pages never need it
        assertNull(resolve("/ja/../index.html"));
        assertNull(resolve("/css/%00style.css"));
    }

    @Test
    public void thePathIsPercentDecodedLikeDecodeUriComponent() {
        assertEquals("css/style.css", resolve("/css/style%2Ecss?v=1"));
        assertEquals("ja/restaurants/index.html", resolve("/ja%2Frestaurants"));
        assertEquals("a+b é", SitePaths.percentDecode("a+b%20%C3%A9"));
        assertNull("malformed escape", SitePaths.percentDecode("/%zz"));
        assertNull("truncated escape", SitePaths.percentDecode("/%4"));
        assertNull("not UTF-8", SitePaths.percentDecode("/%C3"));
    }

    @Test
    public void theAssetLoaderPathIsAlreadyDecoded() {
        // What the handler receives: no leading slash, no query, already decoded, so never decoded twice
        assertEquals("index.html", SitePaths.resolveDecoded("", FILES));
        assertEquals("ja/restaurants/index.html", SitePaths.resolveDecoded("ja/restaurants", FILES));
        assertEquals("css/style.css", SitePaths.resolveDecoded("css/style.css", FILES));
        assertNull(SitePaths.resolveDecoded("css/style%2Ecss", FILES));
        assertNull(SitePaths.resolveDecoded("ja/../index.html", FILES));
    }

    @Test
    public void aMissingPathIsA404() {
        assertNull(resolve("/missing"));
        assertNull(resolve("/restaurants/results?orderBy=ratings"));
        assertNull(resolve("/sw.js"));
        assertNull(resolve("/restaurants/details/999"));
        assertNull(resolve("/css/missing.css?v=1"));
        assertNull(resolve(null));
    }

    @Test
    public void aFolderWithoutIndexIsA404() {
        assertNull(resolve("/css"));
        assertNull(resolve("/app-data"));
    }

    @Test
    public void mimeTypesComeFromTheExtension() {
        assertEquals("text/html", SitePaths.mimeType("ja/restaurants/index.html"));
        assertEquals("text/css", SitePaths.mimeType("css/style.css"));
        assertEquals("text/javascript", SitePaths.mimeType("js/whereto.js"));
        assertEquals("application/json", SitePaths.mimeType("app-data/meta.json"));
        assertEquals("image/svg+xml", SitePaths.mimeType("icons/tags.svg"));
        assertEquals("font/woff2", SitePaths.mimeType("fonts/font.woff2"));
        assertEquals("font/ttf", SitePaths.mimeType("fonts/Font.TTF"));
        assertEquals("image/x-icon", SitePaths.mimeType("favicon.ico"));
        assertEquals("image/png", SitePaths.mimeType("icons/icon-192.png"));
        assertEquals("image/svg+xml", SitePaths.mimeType("A.SVG"));
        assertEquals("application/octet-stream", SitePaths.mimeType("a.unknown"));
        assertEquals("application/octet-stream", SitePaths.mimeType("a.folder/noextension"));
    }

    @Test
    public void textTypesGetACharset() {
        assertTrue(SitePaths.isText("text/html"));
        assertTrue(SitePaths.isText("image/svg+xml"));
        assertTrue(SitePaths.isText("application/json"));
        assertFalse(SitePaths.isText("image/png"));
        assertFalse(SitePaths.isText("font/woff2"));
    }
}
