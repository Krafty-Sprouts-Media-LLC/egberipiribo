/**
 * Writes one Blade view per icon the theme uses, from the Phosphor icon set.
 *
 * Icons are inlined as SVG, so no icon font, no icon stylesheet and no outside
 * host is needed, and the CSP stays as it is. Run `npm run icons` after
 * changing ICONS; the generated views are committed.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');

/** Every icon a view draws, by its Phosphor name. Bold weight throughout. */
const ICONS = [
    'magnifying-glass', 'caret-down', 'arrow-right', 'arrow-left', 'arrow-up', 'x', 'list',
    'check', 'check-circle', 'warning-circle', 'link', 'envelope-simple', 'rows', 'squares-four',
    'x-logo', 'instagram-logo', 'pinterest-logo', 'facebook-logo', 'rss',
    'linkedin-logo', 'whatsapp-logo', 'telegram-logo', 'reddit-logo', 'share-network',
];

mkdirSync(resolve(root, 'views/icons'), { recursive: true });

for (const name of ICONS) {
    const svg = readFileSync(resolve(root, 'node_modules/@phosphor-icons/core/assets/bold', `${name}-bold.svg`), 'utf8');
    const inner = svg.replace(/^[\s\S]*?<svg[^>]*>/, '').replace(/<\/svg>\s*$/, '').trim();
    const view = `{{--
    Icon: ${name} (Phosphor, bold, MIT). Takes an optional $class. Decorative: hidden from assistive technology,
    so give the control around it a name of its own.

    @package egberipiribo
    @since   0.1.0
--}}
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" width="1em" height="1em" class="icon {{ $class ?? '' }}" aria-hidden="true" focusable="false">${inner}</svg>
`;
    writeFileSync(resolve(root, 'views/icons', `${name}.blade.php`), view);
}

console.log(`Wrote ${ICONS.length} icon views.`);
