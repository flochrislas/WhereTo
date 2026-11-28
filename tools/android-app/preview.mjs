// The stand-alone app in a desktop browser: a static server on 127.0.0.1 that serves the export with the path rule
// of the Android app (lib/resolve.mjs). The preview and its tests run on the exact files that go into the APK.
//
//   node preview.mjs [port] [dir]      port 0 (the default): any free port; dir: ./export by default
//
// Open it as http://localhost:<port>/, not http://127.0.0.1:<port>/: whereto.js accepts geolocation without HTTPS
// on the host "localhost" only. The address is printed on start (e2e/tests/standalone.spec.js reads it).
import { createReadStream, existsSync } from 'node:fs';
import { createServer } from 'node:http';
import { dirname, join, resolve as absolute } from 'node:path';
import { fileURLToPath } from 'node:url';
import { resolve } from './lib/resolve.mjs';

const here = dirname(fileURLToPath(import.meta.url));
const port = Number(process.argv[2] ?? 0);
const root = absolute(process.argv[3] ?? join(here, 'export'));

if (!Number.isInteger(port) || port < 0 || port > 65535) {
  console.error(`Not a port: ${process.argv[2]}`);
  process.exit(2);
}
if (!existsSync(join(root, 'index.html'))) {
  console.error(`No export in ${root}: run "npm run export" first.`);
  process.exit(1);
}

const server = createServer((request, response) => {
  // The app has no server: only GET means anything (HEAD for the curious)
  if (request.method !== 'GET' && request.method !== 'HEAD') {
    response.writeHead(405, { Allow: 'GET, HEAD' }).end();
    return;
  }
  const answer = resolve(root, request.url);
  if (answer.status !== 200) {
    // A real 404 with an empty body, as the Android handler answers
    response.writeHead(404, { 'Content-Type': 'text/plain', 'Cache-Control': 'no-store' }).end();
    return;
  }
  // no-store: a new export is seen on the next load, never an old copy
  response.writeHead(200, { 'Content-Type': answer.type, 'Cache-Control': 'no-store' });
  if (request.method === 'HEAD') {
    response.end();
    return;
  }
  createReadStream(answer.file).pipe(response);
});

server.listen(port, '127.0.0.1', () => {
  console.log(`WhereTo preview of ${root}: http://localhost:${server.address().port}/`);
});

// Stop cleanly when the tests or Ctrl+C end it
for (const signal of ['SIGINT', 'SIGTERM']) {
  process.on(signal, () => {
    server.close(() => process.exit(0));
    // The browser keeps its connections open: close them, or close() would wait for them
    server.closeAllConnections();
  });
}
