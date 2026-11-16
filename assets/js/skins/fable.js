/**
 * Fable skin: the components its views use.
 *
 * Written for Alpine's CSP build, which evaluates no string as code. A view's
 * x-* attributes therefore name a property or a method and nothing more: every
 * choice (is the menu open, which class to wear, which icon) is made here, in
 * a getter or a method. Every page works without this script: the menu,
 * the sections and the search are links and a plain form first.
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */

/**
 * Reads a stored choice, and writes one, without ever throwing: storage may be
 * switched off, full or blocked.
 */
const memory = {
    get(key) {
        try {
            return window.localStorage.getItem(key);
        } catch {
            return null;
        }
    },
    set(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch {
            /* A choice that cannot be kept is simply not kept. */
        }
    },
};

/**
 * The page shell: the small-screen menu, the search panel, the back-to-top
 * button, the reading progress bar and the contents list in the rail.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
function shell(Alpine) {
    Alpine.data('fableShell', () => ({
        menuOpen: false,
        searchOpen: false,
        scrolled: false,

        /** Locks the page behind the search panel. */
        get shellClass() {
            return this.searchOpen ? 'overflow-hidden' : '';
        },

        /** The menu button's name: it says what it will do. */
        get menuIcon() {
            return this.menuOpen ? 'close' : 'open';
        },

        get menuClosed() {
            return !this.menuOpen;
        },

        init() {
            const top = this.$refs.top;
            if (top && 'IntersectionObserver' in window) {
                new IntersectionObserver(([entry]) => {
                    this.scrolled = !entry.isIntersecting;
                }).observe(top);
            }
            this.$watch('searchOpen', (open) => {
                if (open) {
                    this.$nextTick(() => this.$refs.q && this.$refs.q.focus());
                }
            });
            this.watchContents();
            this.watchProgress();
        },

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
        },

        closeMenu() {
            this.menuOpen = false;
        },

        openSearch() {
            this.menuOpen = false;
            this.searchOpen = true;
        },

        closeSearch() {
            this.searchOpen = false;
        },

        /** Escape closes whatever is open. */
        closeAll() {
            this.searchOpen = false;
            this.menuOpen = false;
        },

        /** "/" opens the search, unless the reader is typing somewhere. */
        onSlash(event) {
            const typing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement && document.activeElement.tagName);
            if (event.key === '/' && !typing && !event.metaKey && !event.ctrlKey && !event.altKey) {
                event.preventDefault();
                this.openSearch();
            }
        },

        backToTop() {
            const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: calm ? 'auto' : 'smooth' });
        },

        /**
         * The rail's "In this story" list marks the heading the reader has
         * scrolled past: a heading counts once it is above the top 30% of the view.
         */
        watchContents() {
            const heads = [...document.querySelectorAll('.prose h2[id]')];
            const links = [...document.querySelectorAll('[data-toc-link]')];
            if (!heads.length || !links.length || !('IntersectionObserver' in window)) {
                return;
            }
            const passed = new Set();
            const mark = () => {
                const last = heads.filter((h) => passed.has(h.id)).pop();
                const id = last ? last.id : heads[0].id;
                links.forEach((link) => {
                    link.setAttribute('aria-current', link.getAttribute('href') === `#${id}` ? 'true' : 'false');
                });
            };
            const spy = new IntersectionObserver(
                (entries) => {
                    entries.forEach((e) => (e.isIntersecting ? passed.add(e.target.id) : passed.delete(e.target.id)));
                    mark();
                },
                { rootMargin: '100000px 0px -70% 0px' },
            );
            heads.forEach((h) => spy.observe(h));
            mark();
        },

        /** The reading bar follows a CSS scroll timeline where there is one, and this listener elsewhere. */
        watchProgress() {
            const bar = document.querySelector('[data-progress]');
            if (!bar || CSS.supports('animation-timeline: scroll()')) {
                return;
            }
            let queued = false;
            window.addEventListener(
                'scroll',
                () => {
                    if (queued) {
                        return;
                    }
                    queued = true;
                    requestAnimationFrame(() => {
                        const max = document.documentElement.scrollHeight - window.innerHeight;
                        bar.style.setProperty('--p', max > 0 ? window.scrollY / max : 0);
                        queued = false;
                    });
                },
                { passive: true },
            );
        },
    }));
}

/**
 * One section of the rail: its topics open and close. The section's own link
 * always goes to its page; the caret beside it only shows the topics.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
function railSection(Alpine) {
    Alpine.data('railSection', () => ({
        open: false,

        init() {
            this.open = this.$el.dataset.open === 'true';
        },

        toggle() {
            this.open = !this.open;
        },

        get caretClass() {
            return this.open ? 'rotate-180' : '';
        },
    }));
}

/**
 * The grid or list choice on a listing. It is the reader's, kept on this
 * device, and the server never knows it.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
function layoutToggle(Alpine) {
    Alpine.data('layoutToggle', () => ({
        view: 'grid',

        init() {
            const saved = memory.get('fable:view');
            if (saved === 'grid' || saved === 'list') {
                this.view = saved;
            }
            this.$watch('view', (value) => memory.set('fable:view', value));
        },

        get isGrid() {
            return this.view === 'grid';
        },

        get isList() {
            return this.view === 'list';
        },

        setGrid() {
            this.view = 'grid';
        },

        setList() {
            this.view = 'list';
        },

        /**
         * The listing's classes: a grid of cards (four columns on the front page, three where a section sets
         * data-columns="3" on its x-data element), or one row each.
         */
        get listClass() {
            if (!this.isGrid) {
                return 'grid gap-y-6 sm:gap-y-0';
            }
            return this.$el.dataset.columns === '3'
                ? 'grid sm:grid-cols-2 xl:grid-cols-3 gap-x-6 gap-y-6 sm:gap-y-10'
                : 'grid sm:grid-cols-2 xl:grid-cols-4 gap-x-6 gap-y-6 sm:gap-y-10';
        },

        /** Each card's class in the list view, which the drawing calls a card row. */
        get rowClass() {
            return this.isList ? 'card-row' : '';
        },
    }));
}

/**
 * The share button that copies the page's address.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
function copyLink(Alpine) {
    Alpine.data('copyLink', () => ({
        copied: false,

        async copy() {
            try {
                await navigator.clipboard.writeText(window.location.href);
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch {
                this.copied = false;
            }
        },

        get idle() {
            return !this.copied;
        },
    }));
}

/**
 * The share button that opens the phone's own share sheet. It shows only where the browser has one.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
function shareNative(Alpine) {
    Alpine.data('shareNative', () => ({
        supported: false,

        init() {
            this.supported = typeof navigator.share === 'function';
        },

        async share() {
            try {
                await navigator.share({ title: document.title, url: window.location.href });
            } catch {
                /* The reader closed the sheet, or the browser refused: nothing to do. */
            }
        },
    }));
}

/**
 * Registers every Fable component.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerFable(Alpine) {
    shell(Alpine);
    railSection(Alpine);
    layoutToggle(Alpine);
    copyLink(Alpine);
    shareNative(Alpine);
}
