<?php

/**
 * Header – Stadt Apotheke ILEF
 */
$ui_rings      = get_theme_mod('sa_ui_rings', false)  ? ' ui-rings'  : '';
$ui_filled     = get_theme_mod('sa_ui_filled', false) ? ' ui-filled' : '';
$show_switcher = get_theme_mod('sa_theme_switcher', true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Pre-paint Dark Mode -->
    <script>
        (function() {
            try {
                const BP = 1080; // EIN Breakpoint
                const inMobile = () => window.innerWidth < BP;
                var DEF = <?php echo json_encode(get_theme_mod('sa_theme_default', 'system')); ?>;
                var saved = localStorage.getItem('theme') || DEF;
                var dark = (saved === 'dark') || (saved === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
                var el = document.documentElement;
                el.classList.toggle('dark', dark);
                el.dataset.theme = saved;
            } catch (e) {}
        })();
    </script>

    <?php wp_head(); ?>

    <style id="sa-search-override">

    </style>
</head>

<body <?php body_class('antialiased'); ?>>
    <?php do_action('tailpress_site_before'); ?>

    <div id="page" class="min-h-screen flex flex-col">
        <?php
        $tp_notice_on   = get_theme_mod('tp_notice_enabled', false);
        $tp_notice_text = trim(get_theme_mod('tp_notice_text', ''));
        $tp_notice_style = get_theme_mod('tp_notice_style', 'light');
        $tp_notice_x    = get_theme_mod('tp_notice_dismiss', true);

        // Key, damit sie nach Textänderung wieder erscheint
        $tp_notice_key  = md5($tp_notice_style . '|' . $tp_notice_text);
        if ($tp_notice_on && $tp_notice_text):
            // Style → Klassen
            $cls = 'bg-white text-zinc-900 border-b border-zinc-200';
            if ($tp_notice_style === 'dark')    $cls = 'bg-zinc-900 text-white border-b border-zinc-800';
            if ($tp_notice_style === 'primary') $cls = 'bg-primary text-white';
        ?>
            <div id="tp-announcement" class="w-full <?php echo esc_attr($cls); ?> px-3 sm:px-4 md:px-6 lg:px-8"
                data-notice-key="<?php echo esc_attr($tp_notice_key); ?>" hidden>
                <div class="mx-auto max-w-screen-2xl">
                    <div class="flex items-center justify-between gap-3 py-2">
                        <div class="prose-sm max-w-none"><?php echo wp_kses_post($tp_notice_text); ?></div>
                        <?php if ($tp_notice_x): ?>
                            <button type="button" class="tp-annc-close inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-black/10"
                                aria-label="<?php esc_attr_e('Hinweis ausblenden', 'stadt_apotheke_ilef'); ?>">×</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <script>
                (function() {
                    const bar = document.getElementById('tp-announcement');
                    if (!bar) return;
                    const key = 'tp_notice_' + bar.dataset.noticeKey;
                    if (localStorage.getItem(key) === 'dismissed') {
                        bar.remove();
                        return;
                    }
                    bar.hidden = false;
                    bar.querySelector('.tp-annc-close')?.addEventListener('click', () => {
                        localStorage.setItem(key, 'dismissed');
                        bar.remove();
                    });
                })();
                // Refs
                const gSearchBtn = document.getElementById('header-search-toggle');
                const gSearchPanel = document.getElementById('global-search');

                // Overlay-Styling einmalig setzen (leichtes Vollbild-Panel)
                if (gSearchPanel) {
                    gSearchPanel.classList.add(
                        'fixed', 'inset-x-0', 'top-[calc(var(--header-h)+8px)]',
                        'z-50', 'px-3', 'sm:px-4', 'md:px-6', 'lg:px-8'
                    );
                }

                // Öffnen/Schließen (reused)
                function setGlobalSearchOpen(open) {
                    if (!gSearchPanel) return;
                    if (open) {
                        gSearchPanel.removeAttribute('hidden');
                        gSearchPanel.classList.remove('hidden');
                        requestAnimationFrame(() => gSearchPanel.querySelector('input[type="search"],input[type="text"]')?.focus());
                    } else {
                        gSearchPanel.setAttribute('hidden', '');
                        gSearchPanel.classList.add('hidden');
                    }
                }

                // Button → Overlay togglen
                gSearchBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    const isHidden = gSearchPanel?.hasAttribute('hidden');
                    setGlobalSearchOpen(!!isHidden);
                });

                // Escape schließt
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') setGlobalSearchOpen(false);
                });

                // Outside-Click (nur Desktop sinnvoll)
                document.addEventListener('click', (e) => {
                    if (!gSearchPanel || gSearchPanel.hasAttribute('hidden')) return;
                    if (gSearchPanel.contains(e.target)) return;
                    if (e.target === gSearchBtn || gSearchBtn?.contains(e.target)) return;
                    setGlobalSearchOpen(false);
                });
            </script>
        <?php endif; ?>

        <?php
        $myacc_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/mein-konto/');
        $cart_url  = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/warenkorb/');
        $cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
        ?>
        <!-- STICKY HEADER -->
        <header id="site-header"
            class="sticky top-0 z-50 backdrop-blur
         bg-[rgba(var(--c-surface),0.95)]
         dark:bg-[rgba(var(--c-surface),0.86)]
         supports-[backdrop-filter]:bg-[rgba(var(--c-surface),0.78)]
         transition-shadow<?php echo $ui_rings . $ui_filled; ?>">

            <!-- BAR 1 -->
            <div class="topbar w-full px-3 sm:px-4" data-topbar>
                <div class="flex h-16 items-center gap-3">

                    <!-- LEFT: Logo + PRIMARY NAV -->
                    <div class="flex items-center gap-3 min-w-0">
                        <?php /* Burger nur < lg */ ?>
                        <button id="primary-menu-toggle" class="lg:hidden icon-btn h-10 w-10" aria-controls="primary-navigation-mobile" aria-expanded="false" aria-label="<?php esc_attr_e('Navigation öffnen', 'stadt_apotheke_ilef'); ?>">
                            <?= sa_heroicon('bars-3-bottom-left', 'outline'); ?>
                            <svg data-ico="close" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" />
                            </svg>
                        </button>

                        <a href="<?= esc_url(home_url('/')); ?>" class="logo-box site-logo shrink-0 block" aria-label="<?php bloginfo('name'); ?>">
                            <?php has_custom_logo() ? the_custom_logo() : printf(
                                '<img class="custom-logo h-10 w-auto" src="%s" alt="%s">',
                                esc_url(get_template_directory_uri() . '/assets/img/logo.png'),
                                esc_attr(get_bloginfo('name'))
                            ); ?>
                        </a>

                        <!-- Primärnav (Tabs) nur ≥ lg sichtbar -->
                        <nav id="primary-navigation" class="hidden lg:block">
                            <?php
                            wp_nav_menu([
                                'theme_location' => 'primary',
                                'container'      => false,
                                'menu_class'     => 'menu sa-tabs flex items-center gap-6',
                                'fallback_cb'    => '__return_false',
                            ]);
                            ?>
                        </nav>
                    </div>

                    <div class="flex-1"></div>

                    <!-- RIGHT: Search, Theme, Mail, Account, Cart -->
                    <div class="flex items-center gap-2">

                        <!-- MOBILE: Suche (immer sichtbar) -->
                        <button id="mobile-search-toggle"
                            class="icon-btn h-10 w-10 inline-flex lg:hidden"
                            aria-label="<?php esc_attr_e('Suche öffnen', 'stadt_apotheke_ilef'); ?>">
                            <?= sa_icon('search', 'h-5 w-5'); ?>
                        </button>

                        <!-- Theme-Switcher -->
                        <?php if ($show_switcher): ?>
                            <button id="themeSwitchBtn" class="icon-btn h-10 w-10" aria-label="Theme: System" title="Theme: System">
                                <svg data-ico="system" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <rect x="3" y="4" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 20h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                <svg data-ico="light" class="hidden" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5L19 19M5 19l1.5-1.5M17.5 6.5L19 5"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                <svg data-ico="dark" class="hidden" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 12.7A9 9 0 1 1 11.3 3a7 7 0 1 0 9.7 9.7Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        <?php endif; ?>

                        <!-- Mail -->
                        <a href="mailto:info@stadt-apotheke-ilef.ch" class="icon-btn h-10 w-10" aria-label="E-Mail">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="5" width="18" height="14" rx="2" ry="2"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path d="M3 7l9 7 9-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </a>

                        <!-- Konto -->
                        <a href="<?= esc_url($myacc_url); ?>" class="icon-btn h-10 w-10" aria-label="<?php esc_attr_e('Mein Konto', 'stadt_apotheke_ilef'); ?>">
                            <?= sa_icon('user', 'h-5 w-5'); ?>
                        </a>

                        <!-- Warenkorb -->
                        <a href="<?= esc_url($cart_url); ?>" class="icon-btn cart-link relative" aria-label="<?php esc_attr_e('Warenkorb', 'stadt_apotheke_ilef'); ?>">
                            <?= sa_icon('cart', 'h-6 w-6 shrink-0'); ?>
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-count-badge"><?= (int)$cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                </div>
            </div>
            <!-- MOBILE Search Panel -->
            <div id="mobile-search" class="hidden lg:hidden" hidden>
                <?php get_template_part('template-parts/search/form', null, [
                    'variant' => 'mobile',
                    'post_type' => 'product',
                    'placeholder' => __('Suchen…', 'stadt_apotheke_ilef')
                ]); ?>
            </div>
            <!-- BAR 2: nur Suche (≥ lg) -->
            <div class="bar2-wrapper hidden lg:block w-full relative px-3 sm:px-4 md:px-6 lg:px-8 lg:border-t border-zinc-100 dark:lg:border-zinc-800 lg:py-2 xl:py-3">
                <div class="flex justify-center" data-bar2>
                    <div class="w-full search-wrap max-w-2xl">
                        <?php get_template_part('template-parts/search/form', null, ['variant' => 'desktop', 'post_type' => 'product', 'placeholder' => __('Produkte suchen…', 'stadt_apotheke_ilef')]); ?>
                    </div>
                </div>
            </div>

            <!-- Overlay/Panel: dieselbe Suche wiederverwenden -->
            <div id="global-search" class="hidden" hidden>
                <?php get_template_part('template-parts/search/form', null, ['variant' => 'mobile', 'post_type' => 'product', 'placeholder' => __('Suchen…', 'stadt_apotheke_ilef')]); ?>
            </div>


            <!-- MOBILE nav (nur 1x!) -->
            <nav id="primary-navigation-mobile" class="lg:hidden hidden py-3 px-3 sm:px-4" hidden>
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'menu flex flex-col gap-2 [&_ul]:ml-4 [&_ul]:mt-1 [&_a]:!no-underline',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

        </header>
        <script>
            // globaler Namespace
            window.SA = window.SA || {};
            SA.isTouch = SA.isTouch || function() {
                return (window.matchMedia &&
                        (matchMedia('(hover: none)').matches || matchMedia('(pointer: coarse)').matches)) ||
                    ('ontouchstart' in window) ||
                    (navigator.maxTouchPoints > 0);
            };

            (() => {
                // Mehrfach-Init verhindern
                if (window.__saHeaderInit) return;
                window.__saHeaderInit = true;

                // ===== Settings: EIN Breakpoint (lg) =====
                const BP = 1080;
                const inMobile = () => window.innerWidth < BP;

                // ===== DOM refs =====
                const header = document.getElementById('site-header');
                const sBtn = document.getElementById('mobile-search-toggle');
                const sPanel = document.getElementById('mobile-search');
                const navBtn = document.getElementById('primary-menu-toggle');
                const navEl = document.getElementById('primary-navigation-mobile');
                const tBtn = document.getElementById('themeSwitchBtn');

                /* -------------------------------------------------------
                 * 1) Sticky + Topbar einklappen (nur ≥ BP)
                 * ----------------------------------------------------- */
                (function() {
                    if (!header) return;

                    const mq = window.matchMedia(`(min-width:${BP}px)`);

                    function applyState(scrolled) {
                        header.classList.toggle('is-scrolled', scrolled);
                        header.classList.toggle('shadow-md', scrolled);
                        header.classList.toggle('shadow-none', !scrolled);
                    }

                    function onScrollFallback() {
                        const scrolled = (window.scrollY || document.documentElement.scrollTop || 0) > 8;
                        applyState(scrolled);
                    }

                    let detach = () => {};

                    function enableDesktop() {
                        const sentinel = document.createElement('div');
                        sentinel.setAttribute('aria-hidden', 'true');
                        sentinel.style.cssText = 'position:absolute;top:0;left:0;width:1px;height:1px;';
                        header.parentNode.insertBefore(sentinel, header);

                        let io;
                        try {
                            io = new IntersectionObserver((entries) => {
                                const atTop = entries[0].isIntersecting;
                                applyState(!atTop);
                            }, {
                                threshold: 0
                            });
                            io.observe(sentinel);
                            detach = () => {
                                io.disconnect();
                                sentinel.remove();
                            };
                        } catch (e) {
                            window.addEventListener('scroll', onScrollFallback, {
                                passive: true
                            });
                            onScrollFallback();
                            detach = () => window.removeEventListener('scroll', onScrollFallback);
                        }
                        onScrollFallback();
                    }

                    function disableDesktop() {
                        detach();
                        detach = () => {};
                        applyState(false); // Topbar sichtbar halten
                    }

                    mq.matches ? enableDesktop() : disableDesktop();
                    (mq.addEventListener ? mq.addEventListener('change', e => e.matches ? enableDesktop() : disableDesktop()) :
                        mq.addListener && mq.addListener(e => e.matches ? enableDesktop() : disableDesktop()));
                })();

                /* -------------------------------------------------------
                 * 2) Theme Tri-State: system → light → dark
                 * ----------------------------------------------------- */
                const THEME_KEY = 'theme';
                const root = document.documentElement;
                const mql = window.matchMedia('(prefers-color-scheme: dark)');
                let mode = localStorage.getItem(THEME_KEY) || 'system';

                function applyTheme(m) {
                    const dark = (m === 'dark') || (m === 'system' && mql.matches);
                    root.classList.toggle('dark', dark);
                    root.dataset.theme = m;
                    if (tBtn) {
                        tBtn.dataset.mode = m;
                        const label = m === 'system' ? 'Theme: System' : (m === 'light' ? 'Theme: Hell' : 'Theme: Dunkel');
                        tBtn.setAttribute('aria-label', label);
                        tBtn.title = label + ' – klicken zum Wechseln';
                        ['system', 'light', 'dark'].forEach(n => {
                            tBtn.querySelector(`[data-ico="${n}"]`)?.classList.toggle('hidden', n !== m);
                        });
                    }
                }

                function setTheme(m) {
                    mode = m;
                    localStorage.setItem(THEME_KEY, m);
                    applyTheme(m);
                }

                function cycleTheme() {
                    setTheme(['system', 'light', 'dark'][(['system', 'light', 'dark'].indexOf(mode) + 1) % 3]);
                }

                applyTheme(mode);
                mql.addEventListener?.('change', () => {
                    if ((localStorage.getItem(THEME_KEY) || 'system') === 'system') applyTheme('system');
                });
                tBtn?.addEventListener('click', cycleTheme);

                /* -------------------------------------------------------
                 * 3) Mobile Search Toggle
                 * ----------------------------------------------------- */
                function setSearchOpen(open) {
                    if (!sPanel) return;
                    if (open) {
                        sPanel.removeAttribute('hidden');
                        sPanel.classList.remove('hidden');
                        requestAnimationFrame(() => sPanel.querySelector('input[type="search"],input[type="text"]')?.focus());
                    } else {
                        sPanel.setAttribute('hidden', '');
                        sPanel.classList.add('hidden');
                    }
                    sBtn?.setAttribute('aria-expanded', String(open));
                }

                // Delegation: öffnet/ schließt per Button
                document.addEventListener('click', (ev) => {
                    const btn = ev.target.closest('#mobile-search-toggle');
                    if (btn) {
                        ev.preventDefault();
                        setSearchOpen(sPanel?.hasAttribute('hidden'));
                    }
                }, true);

                // Outside-Click schließt (nur mobil & wenn offen)
                document.addEventListener('click', (ev) => {
                    if (!sPanel || sPanel.hasAttribute('hidden') || !inMobile()) return;
                    if (sPanel.contains(ev.target)) return;
                    if (ev.target.closest('#mobile-search-toggle')) return;
                    setSearchOpen(false);
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') setSearchOpen(false);
                });

                /* -------------------------------------------------------
                 * 4) Mobile Nav Toggle (< BP)
                 * ----------------------------------------------------- */
                function setNavOpen(open) {
                    if (!navEl) return;
                    if (open) {
                        navEl.removeAttribute('hidden');
                        navEl.classList.remove('hidden');
                    } else {
                        navEl.setAttribute('hidden', '');
                        navEl.classList.add('hidden');
                    }
                    navBtn?.setAttribute('aria-expanded', String(open));
                }
                navBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    setNavOpen(navEl?.hasAttribute('hidden'));
                });

                // Outside-Click schließt (nur mobil)
                document.addEventListener('click', (e) => {
                    if (!navEl || navEl.hasAttribute('hidden') || !inMobile()) return;
                    if (e.target === navBtn || navBtn?.contains(e.target) || navEl.contains(e.target)) return;
                    setNavOpen(false);
                });

                /* -------------------------------------------------------
                 * 5) Resize-Hygiene beim Schwellenwechsel
                 * ----------------------------------------------------- */
                let wasMobile = inMobile();
                window.addEventListener('resize', () => {
                    const m = inMobile();
                    if (m !== wasMobile) {
                        // Zustände bereinigen beim Wechsel zwischen Mobil/Desktop
                        if (sPanel) {
                            sPanel.setAttribute('hidden', '');
                            sPanel.classList.add('hidden');
                        }
                        if (navEl) {
                            navEl.setAttribute('hidden', '');
                            navEl.classList.add('hidden');
                        }
                        sBtn?.setAttribute('aria-expanded', 'false');
                        navBtn?.setAttribute('aria-expanded', 'false');
                        wasMobile = m;
                    }
                });

                /* -------------------------------------------------------
                 * 6) Touch-Nav
                 * ----------------------------------------------------- */
                (function() {
                    const nav = document.getElementById('primary-navigation-mobile');
                    if (!nav) return;

                    function closeAll() {
                        nav.querySelectorAll('li.menu-item-has-children > a').forEach(a => {
                            a.setAttribute('aria-expanded', 'false');
                        });
                        nav.querySelectorAll('li.menu-item-has-children').forEach(li => {
                            li.dataset.open = 'false';
                        });
                    }
                    nav.addEventListener('click', (e) => {
                        if (!SA.isTouch()) return; // Desktop via :hover
                        const a = e.target.closest('li.menu-item-has-children > a');
                        if (!a) return;

                        const li = a.parentElement;
                        const sub = li.querySelector(':scope > .sub-menu');
                        if (!sub) return;

                        if (li.dataset.open === 'true') return; // zweiter Tap darf navigieren
                        e.preventDefault(); // erster Tap nur öffnen
                        closeAll();
                        li.dataset.open = 'true';
                        a.setAttribute('aria-expanded', 'true');
                    });

                    document.addEventListener('click', (e) => {
                        if (!SA.isTouch()) return;
                        if (!nav.contains(e.target)) closeAll();
                    });

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') closeAll();
                    });
                })();
                // Debug
                console.debug('[Header] ready', {
                    header: !!header,
                    themeBtn: !!tBtn,
                    mobileSearchBtn: !!sBtn,
                    mobileSearchPanel: !!sPanel,
                    mobileNavBtn: !!navBtn,
                    mobileNav: !!navEl,
                    bp: BP
                });
            })();
        </script>