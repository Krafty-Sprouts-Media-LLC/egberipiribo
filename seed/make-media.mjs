/**
 * Draws the demo pictures the seed uploads: plain colour fields with a soft shape, made here in a browser so
 * no picture is fetched from anywhere and none is a real person's or a brand's.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const out = resolve(dirname(fileURLToPath(import.meta.url)), 'media');
mkdirSync(out, { recursive: true });
const HUES = [215, 160, 28, 340, 95, 265, 190, 50];
const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1600, height: 900 } });
for (const [i, hue] of HUES.entries()) {
    await page.setContent(`<body style="margin:0;width:1600px;height:900px;overflow:hidden;background:linear-gradient(135deg,hsl(${hue} 70% 62%),hsl(${(hue + 40) % 360} 65% 38%))">
      <div style="position:absolute;left:${10 + i * 6}%;top:${20 + (i % 3) * 12}%;width:520px;height:520px;border-radius:50%;background:hsl(${hue} 80% 85% / .35)"></div>
      <div style="position:absolute;right:${8 + (i % 4) * 5}%;bottom:-8%;width:760px;height:380px;border-radius:380px 380px 0 0;background:hsl(${(hue + 200) % 360} 55% 30% / .45)"></div></body>`);
    await page.screenshot({ path: resolve(out, `fable-${i + 1}.png`) });
}
await browser.close();
console.log(`Drew ${HUES.length} pictures.`);
