// The path rule of the stand-alone app: which file answers a request path, or a 404.
// The one place where it is written in JavaScript: the preview server (preview.mjs) and its tests use it, and the
// Android app's path handler (android/, SitePaths.java) mirrors it. Keep the two the same
// (doc/16-standalone-apk-plan.md, Phase 4):
//   1. drop the query string (the pages ask for /css/style.css?v=1234);
//   2. refuse any ".." segment: nothing outside the export is served;
//   3. the path is a file: serve it;
//   4. else <path>/index.html exists: serve it as HTML (/ja/restaurants is ja/restaurants/index.html);
//   5. else a real 404, which the page relies on to close the details of an unknown place.
import { statSync } from 'node:fs';
import { extname, join } from 'node:path';

/** The content types of the files of the export, by extension */
export const MIME_TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.json': 'application/json',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.ico': 'image/x-icon',
  '.woff2': 'font/woff2',
  '.woff': 'font/woff',
  '.ttf': 'font/ttf',
  '.txt': 'text/plain; charset=utf-8',
};

/** The content type of a file name, application/octet-stream when the extension is unknown */
export function mimeType(file) {
  return MIME_TYPES[extname(file).toLowerCase()] ?? 'application/octet-stream';
}

function isFile(path) {
  try {
    return statSync(path).isFile();
  } catch {
    return false;
  }
}

/**
 * The file that answers a request path (with or without its leading slash, with or without a query string) in the
 * export directory root: { status: 200, file, type } or { status: 404 }.
 */
export function resolve(root, requestPath) {
  let path = String(requestPath ?? '');
  // 1. The query string (and a fragment, which a browser never sends but a hand-typed path may carry)
  path = path.replace(/[?#].*$/s, '');
  try {
    path = decodeURIComponent(path);
  } catch {
    return { status: 404 };
  }
  // 2. No way out of the export: no "..", no backslash (a separator on some systems), no NUL
  const segments = path.split('/').filter(segment => segment !== '' && segment !== '.');
  if (segments.some(segment => segment === '..' || segment.includes('\\') || segment.includes('\0'))) {
    return { status: 404 };
  }
  const relative = segments.join('/');
  // 3. A file
  if (relative !== '' && isFile(join(root, relative))) {
    return { status: 200, file: join(root, relative), type: mimeType(relative) };
  }
  // 4. A folder with its page
  const page = join(root, relative, 'index.html');
  if (isFile(page)) {
    return { status: 200, file: page, type: MIME_TYPES['.html'] };
  }
  // 5. Nothing
  return { status: 404 };
}
