/**
 * Vite build for the Egberipiribo theme.
 *
 * Builds the shared stylesheet and script into public/build, which Teme serves
 * from /themes/egberipiribo/build/, and copies the self-hosted font files from
 * their Fontsource packages into public/fonts, where the manifest's `fonts`
 * key and the stylesheet's @font-face rules point.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import { copyFileSync, existsSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

const root = dirname(fileURLToPath(import.meta.url));

/**
 * Every font file the theme serves: Fontsource package, file name in it.
 * The theme's own names are the same, so the manifest lists them as they are.
 */
const FONT_FILES = [
    ['@fontsource-variable/bricolage-grotesque', 'bricolage-grotesque-latin-wdth-normal.woff2'],
    ['@fontsource-variable/bricolage-grotesque', 'bricolage-grotesque-latin-ext-wdth-normal.woff2'],
    ['@fontsource-variable/literata', 'literata-latin-wght-normal.woff2'],
    ['@fontsource-variable/literata', 'literata-latin-ext-wght-normal.woff2'],
    ['@fontsource-variable/literata', 'literata-latin-wght-italic.woff2'],
    ['@fontsource-variable/literata', 'literata-latin-ext-wght-italic.woff2'],
];

/**
 * Copies the Fontsource files into public/fonts, so the build is the one place
 * the fonts come from and no font is ever fetched from a font host.
 *
 * @return {import('vite').Plugin}
 */
function selfHostedFonts() {
    return {
        name: 'egberipiribo:self-hosted-fonts',
        buildStart() {
            const out = resolve(root, 'public/fonts');
            mkdirSync(out, { recursive: true });
            for (const [pkg, file] of FONT_FILES) {
                const from = resolve(root, 'node_modules', pkg, 'files', file);
                if (!existsSync(from)) {
                    this.error(`Missing font file ${file} in ${pkg}. Run npm install.`);
                }
                copyFileSync(from, resolve(out, file));
            }
        },
    };
}

export default defineConfig({
    // Where core serves the build from: /themes/{code}/build.
    base: '/themes/egberipiribo/build/',
    plugins: [
        tailwindcss(),
        laravel({
            input: ['assets/css/theme.css', 'assets/js/theme.js'],
            publicDirectory: 'public',
            buildDirectory: 'build',
            hotFile: 'public/hot',
        }),
        selfHostedFonts(),
    ],
});
