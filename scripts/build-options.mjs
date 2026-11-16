/**
 * Writes OPTIONS.md from docs/options.json, the proposal's single source of truth, and checks every row
 * against what Teme can store: key shape and length, the four types, a select's default being one of its
 * options, the four fieldsets, and a visible_when that names only EARLIER keys. Prints the totals.
 *
 *   node scripts/build-options.mjs
 *
 * Nothing here changes the theme: the proposal is not built (it needs the owner's review first).
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const data = JSON.parse(readFileSync(resolve(root, 'docs/options.json'), 'utf8'));
const rows = data.rows;
const FIELDSETS = ['site', 'latest', 'content', 'archive'];
const TYPES = ['select', 'boolean', 'text', 'color'];

// ---------------------------------------------------------------- validation
const problems = [];
const seen = new Map();
rows.forEach((row, index) => {
    const at = `row ${index + 1} (${row.key})`;
    if (!/^[a-z][a-z0-9_]{1,40}$/.test(row.key) || row.key.startsWith('nav_')) problems.push(`${at}: key shape or length`);
    if (seen.has(row.key)) problems.push(`${at}: duplicate key`);
    if (!TYPES.includes(row.type)) problems.push(`${at}: type ${row.type}`);
    if (!FIELDSETS.includes(row.fieldset)) problems.push(`${at}: fieldset ${row.fieldset}`);
    if (row.type === 'select') {
        if (!Array.isArray(row.options) || row.options.length < 2) problems.push(`${at}: select without options`);
        else if (!row.options.includes(row.default)) problems.push(`${at}: default ${JSON.stringify(row.default)} is not an option`);
    }
    if (row.type === 'boolean' && typeof row.default !== 'boolean') problems.push(`${at}: boolean default`);
    if (row.type === 'color' && !(row.default === '' || /^#[0-9a-fA-F]{6}$/.test(row.default))) problems.push(`${at}: color default`);
    for (const parent of Object.keys(row.visibleWhen ?? {})) {
        if (!seen.has(parent)) problems.push(`${at}: visible_when names ${parent}, which is not an earlier key`);
    }
    if (row.skin !== 'shared' && row.key !== 'skin' && row.visibleWhen?.skin === undefined) problems.push(`${at}: a skin setting without visible_when skin`);
    seen.set(row.key, row);
});

// ---------------------------------------------------------------- helpers
const esc = (value) => String(value ?? '').replace(/\|/g, '\\|').replace(/\n/g, ' ');
const show = (value) => (typeof value === 'boolean' ? (value ? 'on' : 'off') : value === '' ? 'empty' : `\`${esc(value)}\``);
const typeText = (row) => (row.type === 'select' ? `select: ${row.options.map((o) => `\`${esc(o)}\``).join(', ')}` : row.type);
const visible = (row) => {
    const parts = Object.entries(row.visibleWhen ?? {}).map(([k, v]) => `\`${k}\` = ${Array.isArray(v) ? v.map((x) => `\`${x}\``).join(' or ') : typeof v === 'boolean' ? (v ? 'on' : 'off') : `\`${v}\``}`);
    return parts.join('; ');
};
const status = (row) => (row.waits ? `**waits**: ${esc(row.waits.feature)}${row.waits.issue ? ` ${row.waits.issue}` : ''}` : row.readNow ? 'read now' : 'new');
const table = (list) => {
    const head = '| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |\n|---|---|---|---|---|---|---|---|\n';
    return head + list.map((r) => `| \`${r.key}\` | ${typeText(r)} | ${show(r.default)} | \`${esc(r.template)}\` | ${visible(r) || 'always'} | ${r.fieldset} | ${esc(r.why)}${r.waits?.note ? ` *When Teme can: ${esc(r.waits.note)}*` : ''} | ${status(r)} |`).join('\n') + '\n';
};
const groupsOf = (list) => {
    const map = new Map();
    for (const r of list) {
        if (!map.has(r.group)) map.set(r.group, []);
        map.get(r.group).push(r);
    }
    return map;
};
const section = (title, list) => {
    let out = '';
    for (const [group, items] of groupsOf(list)) out += `\n#### ${group}\n\n${table(items)}`;
    return `\n### ${title}\n${out}`;
};
const count = (list) => ({ total: list.length, readNow: list.filter((r) => r.readNow).length, waits: list.filter((r) => r.waits).length });

// ---------------------------------------------------------------- document
const skinRow = rows.find((r) => r.key === 'skin');
const shared = rows.filter((r) => r.skin === 'shared' && r.key !== 'skin');
const bySkin = (name) => rows.filter((r) => r.skin === name);
const fable = bySkin('fable');
const strata = bySkin('strata');
const threads = bySkin('threads');

const perTemplate = new Map();
for (const r of fable) perTemplate.set(r.template, (perTemplate.get(r.template) ?? 0) + 1);
const perFieldset = new Map();
for (const r of rows) perFieldset.set(r.fieldset, (perFieldset.get(r.fieldset) ?? 0) + 1);
const waits = new Map();
for (const r of rows.filter((x) => x.waits)) {
    const k = `${r.waits.feature}${r.waits.issue ? ` (${r.waits.issue})` : ''}`;
    waits.set(k, (waits.get(k) ?? 0) + 1);
}
const all = count(rows);

let md = `# OPTIONS: the theme settings, skin by skin

**A proposal. Nothing here is built.** Fable ships with each default below fixed. For the owner to review.

${data.intro}

## Totals

| | Settings | Read by a template now | New proposals | Waiting on a Teme feature |
|---|---|---|---|---|
| Fable | ${count(fable).total} | ${count(fable).readNow} | ${count(fable).total - count(fable).readNow} | ${count(fable).waits} |
| Shared by every skin | ${count(shared).total + 1} | ${count(shared).readNow} | ${count(shared).total + 1 - count(shared).readNow} | ${count(shared).waits} |
| Later: Strata | ${count(strata).total} | 0 | ${count(strata).total} | ${count(strata).waits} |
| Later: Threads | ${count(threads).total} | 0 | ${count(threads).total} | ${count(threads).waits} |
| **All** | **${all.total}** | ${all.readNow} | ${all.total - all.readNow} | ${all.waits} |

Fable settings per template or partial: ${[...perTemplate].sort((a, b) => b[1] - a[1]).map(([t, n]) => `\`${t}\` ${n}`).join(' · ')}.

Per fieldset (all skins): ${FIELDSETS.map((f) => `${f} ${perFieldset.get(f) ?? 0}`).join(' · ')}.

Waiting on a Teme feature (${all.waits}): ${[...waits].map(([k, n]) => `${k} ${n}`).join(' · ') || 'none'}.

${problems.length ? `**Validation problems (${problems.length})**:\n\n${problems.map((p) => `- ${p}`).join('\n')}\n` : 'Every row passes the checks in `scripts/build-options.mjs` (key shape and length, types, fieldsets, a select\'s default is one of its options, a `visible_when` names only earlier keys).\n'}
${data.howToRead}

## The skin setting, first

${table([skinRow])}
${data.skinNote}

## Shared by every skin
${shared.length ? `\n${table(shared)}` : '\nNone beyond the skin choice.\n'}
## Fable (the skin this release ships)

${data.fableNote}
${section('Site-wide: header, rail, footer, share, patterns, dark mode, dates, cards', fable.filter((r) => r.fieldset === 'site'))}
${section('The front page and the posts page (fieldset latest)', fable.filter((r) => r.fieldset === 'latest'))}
${section('Articles, pages and comment pages (fieldset content)', fable.filter((r) => r.fieldset === 'content'))}
${section('Archives, authors, search, 404 and errors (fieldset archive)', fable.filter((r) => r.fieldset === 'archive'))}

## Later: Strata

${data.strataNote}
${section('Strata settings', strata)}

## Later: Threads

${data.threadsNote}
${section('Threads settings', threads)}

${data.closing}
`;
writeFileSync(resolve(root, 'OPTIONS.md'), md);
console.log(`${all.total} settings: fable ${fable.length}, shared ${shared.length + 0}, strata ${strata.length}, threads ${threads.length}; waits ${all.waits}; problems ${problems.length}`);
problems.slice(0, 25).forEach((p) => console.log('  - ' + p));
process.exit(problems.length ? 1 : 0);
