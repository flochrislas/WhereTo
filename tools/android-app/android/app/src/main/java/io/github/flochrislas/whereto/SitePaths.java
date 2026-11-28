package io.github.flochrislas.whereto;

import java.io.ByteArrayOutputStream;
import java.nio.ByteBuffer;
import java.nio.charset.CharacterCodingException;
import java.nio.charset.CodingErrorAction;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

/**
 * The path rule of the app: which exported file answers a request path. It is the Java twin of
 * tools/android-app/lib/resolve.mjs (used by the desktop preview), so that the preview and the phone serve the
 * same file for the same address. Pure Java, no Android class, so that the JVM unit tests can run it.
 *
 * The rule: drop the query string; percent-decode; refuse "..", backslashes and NUL;
 * if the path is a file, serve it; else if path/index.html is a file, serve that as HTML; else 404.
 */
public final class SitePaths {

    /** Tells whether a file exists, given its path relative to the root of the export ("css/style.css") */
    public interface Files {
        boolean isFile(String relativePath);
    }

    private SitePaths() {
    }

    /**
     * The file to serve for a raw request path, as the preview's resolve() takes it: with or without its
     * leading slash, with or without a query string or fragment, still percent-encoded.
     * Returns the path relative to the root of the export, or null for a 404.
     */
    public static String resolve(String requestPath, Files files) {
        if (requestPath == null) {
            return null;
        }
        String path = requestPath;
        int cut = indexOfAny(path, '?', '#');
        if (cut >= 0) {
            path = path.substring(0, cut);
        }
        String decoded = percentDecode(path);
        return decoded == null ? null : resolveDecoded(decoded, files);
    }

    /**
     * The same rule for a path already decoded and without its query: what WebViewAssetLoader hands to a
     * path handler (Uri.getPath() of the request, minus the "/" it is registered on). Decoding it again
     * would turn a "%" in a file name into something else.
     */
    public static String resolveDecoded(String path, Files files) {
        if (path == null) {
            return null;
        }
        // Rebuild the path from its non-empty segments, so that "/a//b/" and "a/b" are the same file.
        // ".." is refused rather than resolved, even when it would stay inside: nothing outside the export
        // may ever be served, and the pages never need it. A backslash (a separator on some systems) or a
        // NUL in a segment is refused too.
        List<String> segments = new ArrayList<>();
        for (String segment : path.split("/")) {
            if (segment.isEmpty() || segment.equals(".")) {
                continue;
            }
            if (segment.equals("..") || segment.indexOf('\\') >= 0 || segment.indexOf('\0') >= 0) {
                return null;
            }
            segments.add(segment);
        }
        String relative = String.join("/", segments);

        if (!relative.isEmpty() && files.isFile(relative)) {
            return relative;
        }
        // Pages live at addresses without an extension (/ja/restaurants): each is a folder with its index.html
        String index = relative.isEmpty() ? "index.html" : relative + "/index.html";
        return files.isFile(index) ? index : null;
    }

    /** The MIME type of a served file, from its extension. Unknown extensions are sent as bytes. */
    public static String mimeType(String relativePath) {
        int dot = relativePath.lastIndexOf('.');
        int slash = relativePath.lastIndexOf('/');
        String extension = dot > slash ? relativePath.substring(dot + 1).toLowerCase(Locale.ROOT) : "";
        // The same table as MIME_TYPES in lib/resolve.mjs (the charset is added by the caller here)
        switch (extension) {
            case "html":
                return "text/html";
            case "css":
                return "text/css";
            case "js":
                return "text/javascript";
            case "json":
                return "application/json";
            case "svg":
                return "image/svg+xml";
            case "png":
                return "image/png";
            case "ico":
                return "image/x-icon";
            case "woff2":
                return "font/woff2";
            case "woff":
                return "font/woff";
            case "ttf":
                return "font/ttf";
            case "txt":
                return "text/plain";
            default:
                return "application/octet-stream";
        }
    }

    /** Text types are sent with a charset, so that Japanese text is never read as Latin-1 */
    public static boolean isText(String mimeType) {
        return mimeType.startsWith("text/") || mimeType.equals("application/json")
                || mimeType.equals("image/svg+xml");
    }

    /**
     * Percent-decoding as JavaScript's decodeURIComponent does it: "+" stays a plus (URLDecoder would make
     * it a space), and a malformed escape or bytes that are not UTF-8 give null, a 404.
     */
    static String percentDecode(String text) {
        if (text.indexOf('%') < 0) {
            return text;
        }
        ByteArrayOutputStream bytes = new ByteArrayOutputStream();
        StringBuilder out = new StringBuilder();
        int i = 0;
        while (i < text.length()) {
            char c = text.charAt(i);
            if (c == '%') {
                if (i + 3 > text.length()) {
                    return null;
                }
                int high = Character.digit(text.charAt(i + 1), 16);
                int low = Character.digit(text.charAt(i + 2), 16);
                if (high < 0 || low < 0) {
                    return null;
                }
                bytes.write(high * 16 + low);
                i += 3;
                continue;
            }
            if (!flushUtf8(bytes, out)) {
                return null;
            }
            out.append(c);
            i++;
        }
        return flushUtf8(bytes, out) ? out.toString() : null;
    }

    /** Appends the pending decoded bytes as UTF-8; false when they are not valid UTF-8 */
    private static boolean flushUtf8(ByteArrayOutputStream bytes, StringBuilder out) {
        if (bytes.size() == 0) {
            return true;
        }
        try {
            out.append(StandardCharsets.UTF_8.newDecoder()
                    .onMalformedInput(CodingErrorAction.REPORT)
                    .onUnmappableCharacter(CodingErrorAction.REPORT)
                    .decode(ByteBuffer.wrap(bytes.toByteArray())));
        } catch (CharacterCodingException e) {
            return false;
        }
        bytes.reset();
        return true;
    }

    private static int indexOfAny(String text, char a, char b) {
        int i = text.indexOf(a);
        int j = text.indexOf(b);
        if (i < 0) {
            return j;
        }
        return j < 0 ? i : Math.min(i, j);
    }
}
