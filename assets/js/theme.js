/**
 * The theme's one script.
 *
 * Loads Alpine's CSP build, which reads no string as code and so runs under a
 * page policy without `unsafe-eval`, registers the skin's components, and
 * starts Alpine. The script is bundled into public/build; no view carries an
 * inline script.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */
import Alpine from '@alpinejs/csp';

import { registerFable } from './skins/fable.js';

registerFable(Alpine);

window.Alpine = Alpine;
Alpine.start();
