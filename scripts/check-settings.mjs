/**
 * Checks the theme's settings against its templates: every setting declared in teme-theme.json is read by at least one
 * view, and every `$themeSettings['key'] ?? <literal>` in the views uses the manifest's default for that key (so a
 * template and the manifest never disagree about what "unchanged" means). Reads of a key the manifest does not
 * declare (a later proposal) are listed, not failed.
 *
 *   node scripts/check-settings.mjs
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const manifest = JSON.parse(readFileSync(resolve(root, 'teme-theme.json'), 'utf8'));
const declared = new Map(manifest.settings.map((s) => [s.key, s]));
const reads = new Map();

const walk = (dir) => {
    for (const name of readdirSync(dir)) {
        const path = join(dir, name);
        if (statSync(path).isDirectory()) walk(path);
        else if (path.endsWith('.blade.php')) scan(path);
    }
};
const scan = (file) => {
    const source = readFileSync(file, 'utf8');
    // A table of ['key', <default>] pairs (the share networks and the pattern areas are read through one): each counts as a read with that default.
    for (const m of source.matchAll(/\[\s*'([a-z][a-z0-9_]*)'\s*,\s*(true|false)\s*[,\]]/g)) {
        if (!/^(fable_share_|fable_pattern_)/.test(m[1])) continue;
        const entry = reads.get(m[1]) ?? [];
        entry.push({ file: file.replace(root + '/', ''), literal: m[2] });
        reads.set(m[1], entry);
    }
    for (const m of source.matchAll(/\$(?:themeSettings|patternCfg)\[\s*'([a-z0-9_]+)'\s*\]\s*\?\?\s*(true|false|null|'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|-?\d+|\[\])?/g)) {
        const entry = reads.get(m[1]) ?? [];
        entry.push({ file: file.replace(root + '/', ''), literal: m[2] });
        reads.set(m[1], entry);
    }
};
walk(join(root, 'views'));

const asValue = (literal) => {
    if (literal === undefined) return undefined;
    if (literal === 'true') return true;
    if (literal === 'false') return false;
    if (literal === 'null') return null;
    if (/^-?\d+$/.test(literal)) return Number(literal);
    return literal.slice(1, -1);
};
const problems = [];
for (const [key, setting] of declared) {
    if (!reads.has(key)) problems.push(`${key}: declared but no view reads it`);
}
for (const [key, list] of reads) {
    const setting = declared.get(key);
    if (!setting) continue;
    for (const { file, literal } of list) {
        const value = asValue(literal);
        if (value === undefined) continue; // a non-literal fallback (e.g. $site->tagline): not comparable
        if (String(value) !== String(setting.default)) problems.push(`${key}: ${file} falls back to ${JSON.stringify(value)}, the manifest says ${JSON.stringify(setting.default)}`);
    }
}
const undeclared = [...reads.keys()].filter((k) => !declared.has(k));
console.log(`${declared.size} settings declared; ${reads.size} keys read by the views; ${undeclared.length} read but not declared${undeclared.length ? ': ' + undeclared.join(', ') : ''}`);
problems.forEach((p) => console.log('  - ' + p));
process.exit(problems.length ? 1 : 0);
