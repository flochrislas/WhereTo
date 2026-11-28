// Makes export/: the site and its data as files, for the stand-alone Android app and its desktop preview, and
// export-reference.json beside it: the site's own answers to the parity requests, from the same database.
// Run on the host, with the dev containers up (dev/compose.yaml). See README.md.
//
// 1. check that the dev containers are up;
// 2. create the database whereto_export once (the command rebuilds it, it does not create it);
// 3. php artisan app:export-static in the app container, into source/storage/app/ (mounted at /app/storage/app/),
//    then move the result here;
// 4. php artisan app:export-reference on the same database: the answers the parity tests compare with;
// 5. print the summary of app-data/meta.json.
// The previous export/ and its reference answers are replaced together, and only once the new pair is complete
// (lib/publish.mjs): a failure or an interruption at any step leaves the previous pair, or brings it back.
// One export at a time: they share the staging folder and the export database (a lock folder says one is running).
import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { publish, recover } from './lib/publish.mjs';
import { parityRequests } from './lib/requests.mjs';

const here = dirname(fileURLToPath(import.meta.url));
const repository = join(here, '..', '..');
const compose = ['compose', '-f', join(repository, 'dev', 'compose.yaml')];
const storage = join(repository, 'source', 'storage', 'app');
const DATABASE = 'whereto_export';

/** Run podman compose with these arguments; the output, or the error with a message */
function podman(args, options = {}) {
  return execFileSync('podman', [...compose, ...args], { encoding: 'utf8', stdio: ['ignore', 'pipe', options.quiet ? 'pipe' : 'inherit'], ...options });
}

function fail(message) {
  console.error(`\n${message}`);
  process.exit(1);
}

// One export at a time. mkdir either creates the folder or fails, in one step: two runs cannot both get it
const lock = join(here, '.export.lock');
try {
  mkdirSync(lock);
} catch {
  fail(`Another export is running (or one was killed): ${lock} exists. If none is running, remove that folder and try again.`);
}
process.on('exit', () => rmSync(lock, { recursive: true, force: true }));
for (const signal of ['SIGINT', 'SIGTERM', 'SIGHUP']) {
  process.on(signal, () => process.exit(130));
}

// An earlier export may have been killed while it put its result in place: finish or undo that first, so that the
// last complete export is at its public paths whatever happens to this run (lib/publish.mjs)
const recovered = recover(here);
if (recovered === 'restored') {
  console.log('An earlier export was interrupted while replacing the previous one: the previous one is back in place.');
} else if (recovered === 'completed') {
  console.log('An earlier export was interrupted after it was put in place: its leftovers are removed.');
}

// 1. The dev containers
let running = '';
try {
  running = podman(['ps', '--services', '--filter', 'status=running'], { quiet: true });
} catch {
  fail('Cannot run "podman compose": is podman installed? Start the dev containers with: cd dev && podman compose up -d');
}
for (const service of ['app', 'db']) {
  if (!running.split('\n').map(line => line.trim()).includes(service)) {
    fail(`The dev container "${service}" is not running. Start them with: cd dev && podman compose up -d`);
  }
}

// 2. The export database, created once
const psql = sql => podman(['exec', '-T', 'db', 'psql', '-U', 'wheretouser', '-d', 'whereto', '-tAc', sql], { quiet: true }).trim();
if (psql(`SELECT 1 FROM pg_database WHERE datname = '${DATABASE}'`) !== '1') {
  console.log(`Creating the database ${DATABASE} (once)`);
  psql(`CREATE DATABASE ${DATABASE}`);
}

// 3. The export, written by Laravel in the container, then moved here
const work = join(storage, 'static-export');
rmSync(work, { recursive: true, force: true });
console.log('Exporting (rebuilds whereto_export from the seeders)...');
try {
  podman(['exec', '-T', 'app', 'php', 'artisan', 'app:export-static', 'storage/app/static-export'], { stdio: ['ignore', 'inherit', 'inherit'] });
} catch {
  fail('The export failed: see the messages above.');
}

// 4. The site's answers to the parity requests, from the same database, before the export moves
const requests = join(storage, 'export-requests.json');
const reference = join(storage, 'export-reference.json');
writeFileSync(requests, JSON.stringify(parityRequests(work), null, 1));
try {
  podman(['exec', '-T', 'app', 'php', 'artisan', 'app:export-reference', 'storage/app/export-requests.json', 'storage/app/export-reference.json'], { stdio: ['ignore', 'inherit', 'inherit'] });
} catch {
  fail('The reference answers could not be written: see the messages above.');
}

// Publish the pair (lib/publish.mjs): swapped in together, the previous pair put back on a failure, and an earlier
// interrupted publication recovered first
const target = join(here, 'export');
try {
  publish(here, work, reference);
} catch (error) {
  fail(`The new export could not be put in place (the previous one is kept): ${error.message}`);
}
rmSync(work, { recursive: true, force: true });
rmSync(reference, { force: true });
rmSync(requests, { force: true });

// 5. The summary
const meta = JSON.parse(readFileSync(join(target, 'app-data', 'meta.json'), 'utf8'));
console.log(`\nExport of ${meta.exportDate} in ${target}`);
for (const [kind, count] of Object.entries(meta.places)) {
  console.log(`  ${kind.padEnd(12)} ${String(count).padStart(4)} places`);
}
if (!existsSync(join(here, 'export-reference.json'))) {
  fail('export-reference.json is missing.');
}
console.log('Next: npm test (the tests), npm run preview (the app in a browser), npm run apk (the APK).');
