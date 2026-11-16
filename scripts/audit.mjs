/**
 * Audits Fable's pages in a real browser: axe-core accessibility violations, sideways
 * scroll, the number of <h1> and console errors, at 1280 and 375 pixels wide, and
 * saves a full-page screenshot of each into docs/screenshots.
 *
 * Needs a Teme site that draws the theme (the shared dev site while this theme is
 * active), so it is a manual check, not part of the Pest suite:
 *
 *   node scripts/audit.mjs http://127.0.0.1:8099 home=/ article=/some-story/ ...
 *
 * Each page is name=path. Exits 1 when any page has a violation, an overflow, or
 * not exactly one h1.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const axeSource = readFileSync(resolve(root, 'node_modules/axe-core/axe.min.js'), 'utf8');
const [base, ...pairs] = process.argv.slice(2);

if (!base || pairs.length === 0) {
    console.error('Usage: node scripts/audit.mjs <site url> name=/path ...');
    process.exit(2);
}

const WIDTHS = [
    { label: '1280', width: 1280, height: 900 },
    { label: '375', width: 375, height: 812 },
];
const shots = resolve(root, 'docs/screenshots');
mkdirSync(shots, { recursive: true });

const browser = await chromium.launch();
const report = [];
let failed = false;

for (const pair of pairs) {
    const [name, path] = pair.split('=');
    for (const size of WIDTHS) {
        const context = await browser.newContext({ viewport: { width: size.width, height: size.height } });
        const page = await context.newPage();
        const errors = [];
        page.on('console', (message) => {
            if (message.type() === 'error') {
                errors.push(message.text());
            }
        });
        page.on('pageerror', (error) => errors.push(String(error)));

        const response = await page.goto(new URL(path, base).href, { waitUntil: 'networkidle' });
        await page.evaluate(axeSource);
        const result = await page.evaluate(async () => {
            const axe = await window.axe.run(document, { resultTypes: ['violations'] });
            return axe.violations.map((v) => ({ id: v.id, impact: v.impact, nodes: v.nodes.length, help: v.help, target: v.nodes[0]?.target?.join(' '), html: (v.nodes[0]?.html ?? '').slice(0, 200) }));
        });
        const layout = await page.evaluate(() => ({
            h1: document.querySelectorAll('h1').length,
            overflow: document.documentElement.scrollWidth - window.innerWidth,
        }));
        await page.screenshot({ path: resolve(shots, `${name}-${size.label}.png`), fullPage: true });

        const bad = result.length > 0 || layout.h1 !== 1 || layout.overflow > 0;
        failed ||= bad;
        report.push({ page: name, width: size.label, status: response?.status(), h1: layout.h1, overflow: layout.overflow, violations: result, consoleErrors: errors });
        await context.close();
    }
}

await browser.close();
writeFileSync(resolve(root, process.env.AUDIT_JSON ?? 'docs/audit.json'), JSON.stringify(report, null, 2));
for (const row of report) {
    const flag = row.violations.length || row.h1 !== 1 || row.overflow > 0 ? 'FAIL' : 'ok  ';
    console.log(`${flag} ${row.page} @${row.width}: status ${row.status}, h1 ${row.h1}, overflow ${row.overflow}px, axe ${row.violations.length}, console errors ${row.consoleErrors.length}`);
    for (const v of row.violations) {
        console.log(`       - ${v.id} (${v.impact}, ${v.nodes}): ${v.help} [${v.target}] ${v.html}`);
    }
}
process.exit(failed ? 1 : 0);
