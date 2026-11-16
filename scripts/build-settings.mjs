/**
 * Writes the theme's `settings` into teme-theme.json from the rows of docs/options.json that are marked
 * `"build": true`, in the order of that file, and checks them against Teme's rules (key shape, the four types,
 * the four fieldsets, a select's default is one of its options, and a visible_when names only EARLIER built keys).
 *
 * Labels come from the row's own `label` if it has one, then from docs/settings-labels/*.json (key to
 * {label, description}), then from a generated one ("Area: what it does") built from the row. Run after
 * changing either file:
 *
 *   node scripts/build-settings.mjs        (writes teme-theme.json)
 *   node scripts/build-options.mjs         (rewrites OPTIONS.md with the "built" status)
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { existsSync, readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const data = JSON.parse(readFileSync(resolve(root, 'docs/options.json'), 'utf8'));
const manifestPath = resolve(root, 'teme-theme.json');
const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));

const overrides = {};
const labelsDir = resolve(root, 'docs/settings-labels');
if (existsSync(labelsDir)) {
    for (const file of readdirSync(labelsDir).filter((f) => f.endsWith('.json'))) {
        Object.assign(overrides, JSON.parse(readFileSync(resolve(labelsDir, file), 'utf8')));
    }
}

const AREA = [
    [/^Home/, 'Home'], [/^Article/, 'Article'], [/^Comments/, 'Comments'], [/^Page/, 'Page'], [/^Archive/, 'Archive'],
    [/^Author/, 'Author'], [/^Search/, 'Search'], [/^404/, '404 page'], [/^Error/, 'Error pages'], [/^Newsletter/, 'Footer'],
    [/^Ads/, 'Ads'], [/^Share/, 'Share'], [/^Patterns/, 'Pattern'], [/^Header/, 'Header'], [/^Rail/, 'Rail'], [/^Footer/, 'Footer'],
];
const generated = (row) => {
    const area = row.skin === 'shared' ? 'All skins' : (AREA.find(([re]) => re.test(row.group)) ?? [null, row.group])[1];
    let what = row.why.split(/ \(|; |\. /)[0].replace(/^(The|A|An) /, '').replace(/\.$/, '');
    what = what.charAt(0).toLowerCase() + what.slice(1);
    if (what.length > 70) what = what.slice(0, 67).replace(/\s+\S*$/, '') + '…';
    return `${area}: ${what}`;
};

const built = data.rows.filter((r) => r.build === true);
const problems = [];
const seen = new Set();
const settings = built.map((row) => {
    const o = overrides[row.key] ?? {};
    const setting = {
        key: row.key,
        type: row.type,
        label: row.label ?? o.label ?? generated(row),
        description: o.description ?? row.why,
        fieldset: row.fieldset,
    };
    let options = row.options;
    if (row.key === 'skin') options = ['fable']; // only Fable ships in this release; the others are added by the update that builds them
    if (row.type === 'select') {
        setting.options = Object.fromEntries(options.map((v) => [v, (o.optionLabels ?? row.optionLabels ?? {})[v] ?? v.charAt(0).toUpperCase() + v.slice(1).replace(/[-_]/g, ' ')]));
        if (!options.includes(row.default)) problems.push(`${row.key}: default not in options`);
    }
    setting.default = row.default;
    const vw = {};
    for (const [k, v] of Object.entries(row.visibleWhen ?? {})) {
        if (!seen.has(k)) problems.push(`${row.key}: visible_when names ${k}, which is not an earlier built key`);
        vw[k] = v;
    }
    if (Object.keys(vw).length) setting.visible_when = vw;
    if (!/^[a-z][a-z0-9_]{1,40}$/.test(row.key)) problems.push(`${row.key}: key shape`);
    seen.add(row.key);
    return setting;
});

manifest.settings = settings;
writeFileSync(manifestPath, JSON.stringify(manifest, null, 4) + '\n');
console.log(`${settings.length} settings written to teme-theme.json; ${problems.length} problems`);
problems.forEach((p) => console.log('  - ' + p));
process.exit(problems.length ? 1 : 0);
