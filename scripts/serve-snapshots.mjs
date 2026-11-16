/**
 * Serves docs/snapshots as /snap/<name> and the theme's public folder as /themes/egberipiribo/, so the audit can
 * run on pages the kit rendered without touching the shared dev site.
 *
 *   node scripts/serve-snapshots.mjs 8097
 *   node scripts/audit.mjs http://127.0.0.1:8097 home=/snap/home article=/snap/article ...
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { createServer } from 'node:http';
import { existsSync, readFileSync, statSync } from 'node:fs';
import { dirname, extname, join, normalize, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const port = Number(process.argv[2] ?? 8097);
const TYPES = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.json': 'application/json', '.png': 'image/png', '.svg': 'image/svg+xml' };

createServer((request, response) => {
    const path = decodeURIComponent(new URL(request.url ?? '/', 'http://x').pathname);
    let file = null;
    let type = 'text/html; charset=utf-8';
    if (path.startsWith('/snap/')) {
        file = join(root, 'docs/snapshots', `${path.slice(6).replace(/[^a-z0-9-]/gi, '')}.html`);
    } else if (path.startsWith('/themes/egberipiribo/')) {
        file = normalize(join(root, 'public', path.slice('/themes/egberipiribo/'.length)));
        type = TYPES[extname(file)] ?? 'application/octet-stream';
        if (!file.startsWith(join(root, 'public'))) file = null;
    }
    if (file && existsSync(file) && statSync(file).isFile()) {
        response.writeHead(200, { 'content-type': type });
        const bytes = readFileSync(file);
        // The kit renders for http://localhost: point every address back at this server.
        response.end(type.startsWith('text/html') ? bytes.toString('utf8').replaceAll('http://localhost', `http://127.0.0.1:${port}`) : bytes);
    } else {
        response.writeHead(404);
        response.end('not found');
    }
}).listen(port, '127.0.0.1', () => console.log(`Snapshots on http://127.0.0.1:${port}`));
