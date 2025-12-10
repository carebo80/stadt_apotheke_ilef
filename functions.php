<?php

/**
 * Theme Functions – Stadt Apotheke ILEF (safe/minimal)
 */
add_action('after_setup_theme', function () {
    // Passt in die Bühne, ohne Zwangs-Crop (Seitenverhältnis bleibt)
    add_image_size('partner_logo', 320, 140, false);
});

/* 1) Übersetzungen, Theme-Supports, Menüs, TailPress-Hook stilllegen */
add_action('after_setup_theme', function () {
    load_theme_textdomain('stadt_apotheke_ilef', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo', [
        'height' => 64,
        'width' => 220,
        'flex-height' => true,
        'flex-width' => true,
    ]);

    register_nav_menus([
        'primary'   => __('Primary Menu', 'stadt_apotheke_ilef'),
        'secondary' => __('Secondary Menu', 'stadt_apotheke_ilef'),
    ]);

    // Falls das Startertheme irgendwas an tailpress_header hängt → deaktivieren
    remove_all_actions('tailpress_header');
});

add_action('wp_enqueue_scripts', function () {
    $dist_dir = get_stylesheet_directory() . '/dist';
    $dist_uri = get_stylesheet_directory_uri() . '/dist';
    $manifest = $dist_dir . '/manifest.json';

    if (file_exists($manifest)) {
        $data = json_decode(file_get_contents($manifest), true);
        $entry = 'assets/js/app.js';
        if (isset($data[$entry])) {
            $e = $data[$entry];
            if (!empty($e['css'])) {
                foreach ($e['css'] as $i => $css) {
                    wp_enqueue_style('theme-app-' . $i, $dist_uri . '/' . ltrim($css, '/'), [], null);
                }
            }
            if (!empty($e['file'])) {
                wp_enqueue_script('theme-app', $dist_uri . '/' . ltrim($e['file'], '/'), [], null, true);
            }
            return;
        }
    }

    // Fallback, falls mal kein Manifest da ist
    foreach (glob($dist_dir . '/assets/*.css') as $i => $css) {
        wp_enqueue_style('theme-app-fb-' . $i, $dist_uri . '/assets/' . basename($css), [], filemtime($css));
    }
    foreach (glob($dist_dir . '/assets/*.js') as $i => $js) {
        wp_enqueue_script('theme-app-fb-' . $i, $dist_uri . '/assets/' . basename($js), [], filemtime($js), true);
    }
}, 20);

/* 3) Kompaktes Suchformular global verwenden */
add_filter('get_search_form', function () {
    ob_start();
    get_template_part('parts/searchform-compact', null, ['post_type' => 'product']);
    return ob_get_clean();
});

/* 4) Menü-Dropdowns per Klassen (ohne Walker) */
add_filter('nav_menu_css_class', function ($classes, $item, $args) {
    if (($args->theme_location ?? '') !== 'primary') return $classes;
    if (!in_array('menu-item-has-children', (array)$classes, true)) return $classes;
    $classes[] = 'relative';
    $classes[] = 'group';
    return $classes;
}, 10, 3);

// Submenü: versteckt + per Hover/Focus sichtbar
add_filter('nav_menu_submenu_css_class', function ($classes, $args, $depth) {
    if (($args->theme_location ?? '') !== 'primary') return $classes;
    return [
        'sub-menu',
        'absolute',
        'left-0',
        'top-full',
        'min-w-56',
        'rounded-xl',
        'border',
        'p-2',
        'shadow-lg',
        'border-zinc-200',
        'bg-white',
        'dark:border-zinc-800',
        'dark:bg-zinc-900',
    ];
}, 10, 3);

/* 5) Wartungsmodus (Option in Einstellungen) */
add_action('template_redirect', function () {
    if (!current_user_can('manage_options') && get_option('theme_maintenance_mode') === '1') {
        wp_die(
            '<h1>Wartungsmodus</h1><p>Unsere Website wird gerade aktualisiert. Bitte schauen Sie bald wieder vorbei.</p>',
            'Wartung',
            ['response' => 503]
        );
    }
});

add_action('admin_menu', function () {
    add_options_page(
        'Theme Optionen',
        'Theme Optionen',
        'manage_options',
        'theme-options',
        function () {
?>
        <div class="wrap">
            <h1>Theme Optionen</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('theme_options');
                do_settings_sections('theme-options');
                submit_button();
                ?>
            </form>
        </div>
    <?php
        }
    );
});

add_action('admin_init', function () {
    register_setting('theme_options', 'theme_maintenance_mode');
    add_settings_section('theme_main', 'Allgemeine Einstellungen', null, 'theme-options');
    add_settings_field(
        'theme_maintenance_mode',
        'Wartungsmodus aktivieren',
        function () {
            $value = get_option('theme_maintenance_mode');
            echo '<input type="checkbox" name="theme_maintenance_mode" value="1" ' . checked(1, $value, false) . '> Ja';
        },
        'theme-options',
        'theme_main'
    );
});
// functions.php
add_action('customize_register', function (WP_Customize_Manager $wp) {
    // Sektion
    $wp->add_section('sa_colors', [
        'title'    => __('Designfarben', 'yourtextdomain'),
        'priority' => 30,
    ]);

    // Helper zum Hinzufügen (Setting + Control)
    $add_color = function ($id, $label, $default = '#00B140', $section = 'sa_colors') use ($wp) {
        $wp->add_setting($id, [
            'default'           => $default,
            'transport'         => 'postMessage',        // Live-Preview
            'sanitize_callback' => 'sanitize_hex_color',
            'type'              => 'theme_mod',
        ]);
        $wp->add_control(new WP_Customize_Color_Control($wp, $id, [
            'label'   => $label,
            'section' => $section,
            'settings' => $id,
        ]));
    };

    // Light
    $add_color('sa_primary',        'Primär (hell)',     '#00B140');
    $add_color('sa_surface',        'Surface (hell)',    '#FFFFFF');
    $add_color('sa_on_surface',     'Text auf Surface',  '#0A0A0A');
    $add_color('sa_border',         'Border (hell)',     '#E4E4E7');

    // Dark
    $add_color('sa_primary_dark',   'Primär (dunkel)',   '#15735F');
    $add_color('sa_surface_dark',   'Surface (dunkel)',  '#090A0C');
    $add_color('sa_on_surface_dark', 'Text auf Surface (dunkel)', '#F4F4F5');
    $add_color('sa_border_dark',    'Border (dunkel)',   '#3F3F46');
});

/* 6) Übersetzungs-Fix für Search-Title (optional; kannst du später entfernen) */
add_filter('gettext', function ($translated, $text, $domain) {
    if ($text === 'Search results for: %s') {
        return 'Suchergebnisse für: %s';
    }
    return $translated;
}, 10, 3);

add_action('init', function () {
    register_block_pattern(
        'stadt_apotheke_ilef/logo-cloud',
        [
            'title'       => __('Logo Cloud', 'stadt_apotheke_ilef'),
            'description' => __('Reihe von Partner-Logos', 'stadt_apotheke_ilef'),
            'categories'  => ['media', 'columns'],
            'content'     => '
<!-- wp/group {"className":"py-16"} -->
<div class="wp-block-group py-16">
  <div class="container mx-auto px-4">
    <!-- wp/heading {"textAlign":"center","level":2,"className":"mb-8"} -->
    <h2 class="wp-block-heading has-text-align-center mb-8">Unsere Partner</h2>
    <!-- /wp/heading -->

    <!-- wp/group {"className":"grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-8 place-items-center"} -->
    <div class="wp-block-group grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-8 place-items-center">
      <!-- wp/image {"sizeSlug":"medium","className":"h-10 w-auto opacity-80 hover:opacity-100 transition"} -->
      <figure class="wp-block-image size-medium h-10 w-auto opacity-80 hover:opacity-100 transition"><img src="' . esc_url(get_theme_file_uri('assets/img/voigt.png.svg')) . '" alt=""/></figure>
      <!-- /wp/image -->

      <!-- wp/image {"sizeSlug":"medium","className":"h-10 w-auto opacity-80 hover:opacity-100 transition"} -->
      <figure class="wp-block-image size-medium h-10 w-auto opacity-80 hover:opacity-100 transition"><img src="' . esc_url(get_theme_file_uri('assets/img/partner2.svg')) . '" alt=""/></figure>
      <!-- /wp/image -->

      <!-- wp/image {"sizeSlug":"medium","className":"h-10 w-auto opacity-80 hover:opacity-100 transition"} -->
      <figure class="wp-block-image size-medium h-10 w-auto opacity-80 hover:opacity-100 transition"><img src="' . esc_url(get_theme_file_uri('assets/img/Axapharm.png')) . '" alt=""/></figure>
      <!-- /wp/image -->
    </div>
    <!-- /wp/group -->
  </div>
</div>
<!-- /wp/group -->
',
        ]
    );
});
add_filter('tailpine_use_preline', '__return_true');
// Temporäre Diagnose: Welches Template rendert? Kommen HEAD/FOOTER-Hooks?
add_filter('template_include', function ($tpl) {
    error_log('TEMPLATE_USED: ' . $tpl);
    add_action('wp_head', function () use ($tpl) {
        echo "<!-- HEAD OK (template: " . esc_html($tpl) . ") -->\n";
    }, 999);
    add_action('wp_footer', function () use ($tpl) {
        echo "<!-- FOOTER OK (template: " . esc_html($tpl) . ") -->\n";
    }, 999);
    return $tpl;
}, 99);

# WooCommerce Theme Support
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
});
// === Hero-Bild & Logos im Customizer ========================================
add_action('customize_register', function (WP_Customize_Manager $c) {

    // Sektion: Hero
    $c->add_section('sa_hero', [
        'title'       => __('Startseite: Hero', 'stadt_apotheke_ilef'),
        'priority'    => 35,
        'description' => __('Hero-Bild und Optionen', 'stadt_apotheke_ilef'),
    ]);

    // Hero: Bild
    $c->add_setting('sa_hero_image_id', [
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);
    $c->add_control(new WP_Customize_Media_Control($c, 'sa_hero_image_id', [
        'label'       => __('Hero-Bild', 'stadt_apotheke_ilef'),
        'section'     => 'sa_hero',
        'mime_type'   => 'image',
        'description' => __('Großes, querformatiges Bild wählen.'),
    ]));

    // Hero: Mindesthöhe (vh)
    $c->add_setting('sa_hero_min_h', [
        'type'              => 'theme_mod',
        'default'           => 60,
        'sanitize_callback' => function ($v) {
            $v = (int)$v;
            return max(30, min(90, $v));
        },
        'transport'         => 'refresh',
    ]);
    /*
    $c->add_control('sa_hero_min_h', [
        'label'       => __('Mindesthöhe (vh)', 'stadt_apotheke_ilef'),
        'section'     => 'sa_hero',
        'type'        => 'range',
        'input_attrs' => ['min' => 30, 'max' => 90, 'step' => 5],
    ]);
*/
    // Hero: Overlay-Deckkraft
    $c->add_setting('sa_hero_overlay', [
        'type'    => 'theme_mod',
        'default' => 65,
        'sanitize_callback' => function ($v) {
            $v = (int)$v;
            return max(0, min(95, $v));
        },
    ]);
    $c->add_control('sa_hero_overlay', [
        'label'       => __('Overlay-Stärke (%)', 'stadt_apotheke_ilef'),
        'section'     => 'sa_hero',
        'type'        => 'range',
        'input_attrs' => ['min' => 0, 'max' => 95, 'step' => 5],
    ]);
    $c->add_control('sa_hero_text', [
        'label'   => __('Textfarbe auf Bild', 'stadt_apotheke_ilef'),
        'section' => 'sa_hero',
        'type'    => 'radio',
        'choices' => [
            'light' => __('Hell (weiß)', 'stadt_apotheke_ilef'),
            'dark'  => __('Dunkel (schwarz)', 'stadt_apotheke_ilef'),
        ],
    ]);

    // Sektion: Logos (Partner & Zahlungsarten)
    $c->add_section('sa_logos', [
        'title'    => __('Logos & Zahlungsarten', 'stadt_apotheke_ilef'),
        'priority' => 36,
    ]);

    // Partner IDs (kommasepariert)
    $c->add_setting('sa_partner_logo_ids', [
        'type'              => 'theme_mod',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $c->add_control('sa_partner_logo_ids', [
        'label'   => __('Partner-Logo IDs (kommasepariert)', 'stadt_apotheke_ilef'),
        'section' => 'sa_logos',
        'type'    => 'text',
    ]);

    // Payment IDs (kommasepariert)
    $c->add_setting('sa_payment_logo_ids', [
        'type'              => 'theme_mod',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $c->add_control('sa_payment_logo_ids', [
        'label'   => __('Zahlungs-Logo IDs (kommasepariert)', 'stadt_apotheke_ilef'),
        'section' => 'sa_logos',
        'type'    => 'text',
    ]);
});

// WooCommerce "Shop-Mitteilung" (Store Notice) vollständig abschalten
add_filter('woocommerce_demo_store', '__return_false', 99);

function sa_icon(string $name, string $class = '', ?string $label = null, array $extra_attrs = []): string
{
    $class = trim($class);

    // 1) eingebaute Mini-Icons (schnellster Pfad)
    $builtin = [
        'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5" stroke-linecap="round"></path></svg>',
        'menu'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>',
        'close'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>',
        'user'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-3.5 4.5-5.5 8-5.5s6.5 2 8 5.5" stroke-linecap="round"/></svg>',
        'cart'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 6h14l-1.5 9h-11L5 3H3" stroke-linecap="round"/><circle cx="9" cy="20" r="1.6" fill="currentColor"/><circle cx="17" cy="20" r="1.6" fill="currentColor"/></svg>',
    ];
    if (isset($builtin[$name])) {
        $svg = $builtin[$name];
    } else {
        // 2) Datei-Fallback (Child vor Parent)
        $candidates = [
            get_stylesheet_directory() . "/assets/icons/{$name}.svg",
            get_template_directory()   . "/assets/icons/{$name}.svg",
        ];
        $file = null;
        foreach ($candidates as $p) {
            if (is_readable($p)) {
                $file = $p;
                break;
            }
        }
        if (!$file) return '';

        static $raw_cache = [];
        $ckey = $file . ':' . @filemtime($file);
        $svg  = $raw_cache[$ckey] ?? null;
        if (!$svg) {
            $svg = @file_get_contents($file);
            if (!$svg) return '';
            $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
            $raw_cache[$ckey] = $svg;
        }
    }

    // A11y
    $has_label = $label !== null && $label !== '';
    $title_id  = $has_label ? ('svg-title-' . uniqid()) : null;
    $a11y      = $has_label ? ' role="img" aria-labelledby="' . $title_id . '"' : ' aria-hidden="true" focusable="false"';

    // Extra Attrs
    $attr_str = '';
    foreach ($extra_attrs as $k => $v) if ($v !== null) $attr_str .= ' ' . esc_attr($k) . '="' . esc_attr((string)$v) . '"';

    // class + a11y injizieren
    $svg = preg_replace_callback('/<svg\b([^>]*)>/i', function ($m) use ($class, $a11y, $attr_str) {
        $attrs = $m[1] ?? '';
        if ($class !== '') {
            if (preg_match('/\bclass=("|\')(.*?)\1/i', $attrs)) {
                $attrs = preg_replace('/\bclass=("|\')(.*?)\1/i', 'class="$2 ' . esc_attr($class) . '"', $attrs);
            } else {
                $attrs .= ' class="' . esc_attr($class) . '"';
            }
        }
        $attrs .= $a11y . $attr_str;
        return '<svg' . $attrs . '>';
    }, $svg, 1);

    if ($has_label) {
        $svg = preg_replace('/(<svg\b[^>]*>)/i', '$1<title id="' . $title_id . '">' . esc_html($label) . '</title>', $svg, 1);
    }
    return $svg;
}

/**
 * Runde Icon-Buttons rendern.
 *
 * echo icon_btn('search', [
 *   'id'    => 'mobile-search-toggle',
 *   'label' => __('Suche öffnen','stadt_apotheke_ilef'),
 *   'class' => 'md:hidden border border-white/30 bg-white/10 text-white/90',
 *   'glow'  => true,           // grüner Glow am Icon beim Hover
 * ]);
 *
 * echo icon_btn('facebook', [
 *   'href'  => 'https://facebook.com/…',
 *   'label' => 'Facebook',
 *   'size'  => 'sm',           // sm | md | lg
 *   'class' => 'text-zinc-700 hover:bg-black/5 dark:text-zinc-200',
 * ]);
 */
function icon_btn(string $icon, array $o = []): string
{
    $o = array_merge([
        'id'      => null,
        'label'   => null,      // aria-label / Tooltip-Text (empfohlen)
        'href'    => null,      // wenn gesetzt => <a>, sonst <button>
        'target'  => null,
        'rel'     => null,
        'size'    => 'md',      // sm|md|lg
        'class'   => '',
        'glow'    => false,     // Icon-Greenshadow on hover
        'attrs'   => [],        // weitere HTML-Attribute ['data-x'=>'y']
        'disabled' => false,
        'title'   => null,      // optional Tooltip (falls ≠ label)
    ], $o);

    $size_btn = ['sm' => 'h-8 w-8', 'md' => 'h-10 w-10', 'lg' => 'h-12 w-12'][$o['size']] ?? 'h-10 w-10';
    $size_svg = ['sm' => 'h-4 w-4', 'md' => 'h-5 w-5', 'lg' => 'h-6 w-6'][$o['size']] ?? 'h-5 w-5';

    $base = implode(' ', [
        'inline-flex items-center justify-center rounded-full',
        $size_btn,
        'transition focus:outline-none focus:ring-2',
        // Ring in deiner Primärfarbe-Variable
        'focus:ring-2',
        'focus:ring-[rgb(var(--c-primary)/0.35)]',
        'focus:ring-offset-2',
        'focus:ring-offset-[rgb(var(--c-surface))]',
        'hover:bg-black/5 dark:hover:bg-white/10',
    ]);

    $classes = trim($base . ' ' . ($o['class'] ?? ''));
    if ($o['disabled']) $classes .= ' opacity-50 pointer-events-none';

    // Attribute zusammenbauen
    $attrs = (array)$o['attrs'];
    if ($o['id'])     $attrs['id'] = $o['id'];
    if ($o['title'])  $attrs['title'] = $o['title'];
    if ($o['label'])  $attrs['aria-label'] = $o['label'];
    $attrs['class'] = $classes;

    // Icon (dekorativ im Button → kein zusätzliches Label im <svg>)
    $svg_class = $size_svg . ($o['glow'] ? ' icon-glow' : '');
    $icon_html = function_exists('sa_icon') ? sa_icon($icon, $svg_class) : '';

    // Attr-String
    $attr_str = '';
    foreach ($attrs as $k => $v) {
        if ($v === null) continue;
        $attr_str .= ' ' . esc_attr($k) . '="' . esc_attr((string)$v) . '"';
    }

    // Rendern
    if (!empty($o['href'])) {
        $target = $o['target'] ? ' target="' . esc_attr($o['target']) . '"' : '';
        $rel    = $o['rel']    ? ' rel="' . esc_attr($o['rel']) . '"'       : '';
        return '<a href="' . esc_url($o['href']) . '"' . $attr_str . $target . $rel . '>' . $icon_html . '</a>';
    } else {
        $disabled = $o['disabled'] ? ' disabled' : '';
        return '<button type="button"' . $attr_str . $disabled . '>' . $icon_html . '</button>';
    }
}

function sa_hex2rgb($hex)
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $int = hexdec($hex);
    return sprintf('%d %d %d', ($int >> 16) & 255, ($int >> 8) & 255, $int & 255);
}
// Falls ≠ system, Google Font laden
add_action('wp_enqueue_scripts', function () {
    $font = get_theme_mod('sa_nav_font', 'system');
    $urls = [
        'inter'  => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        'nunito' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap',
        'roboto' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
    ];
    if (isset($urls[$font])) {
        wp_enqueue_style('sa-nav-font', $urls[$font], [], null);
    }
}, 30);
add_action('wp_head', function () {
    $p   = sa_hex2rgb(get_theme_mod('sa_primary', '#00B140'));
    $pd  = sa_hex2rgb(get_theme_mod('sa_primary_dark', '#15735F'));
    $sf  = sa_hex2rgb(get_theme_mod('sa_surface', '#FFFFFF'));
    $osf = sa_hex2rgb(get_theme_mod('sa_on_surface', '#0A0A0A'));
    $sfd = sa_hex2rgb(get_theme_mod('sa_surface_dark', '#090A0C'));
    $osd = sa_hex2rgb(get_theme_mod('sa_on_surface_dark', '#F4F4F5'));
    $font = get_theme_mod('sa_nav_font', 'system');
    $stack = 'ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji"';
    if ($font === 'inter')  $stack = '"Inter",' . $stack;
    if ($font === 'nunito') $stack = '"Nunito",' . $stack;
    if ($font === 'roboto') $stack = '"Roboto",' . $stack;
    echo '<style id="sa-nav-font">#site-header .sa-tabs a{font-family:' . $stack . '}</style>';

    echo '<style id="sa-dynamic-vars">
  :root{
    --c-primary: ' . $p . ';
    --c-surface: ' . $sf . ';
    --c-on-surface: ' . $osf . ';
    --c-on-primary: 255 255 255;
  }
  html.dark{
    --c-primary: ' . $pd . ';
    --c-surface: ' . $sfd . ';
    --c-on-surface: ' . $osd . ';
    --c-on-primary: 255 255 255;
  }
  </style>';
});
add_action('wp_head', function () { ?>
    <style id="sa-helpers">
        /* Utilities auf Basis von --c-primary */
        .text-primary {
            color: rgb(var(--c-primary)) !important;
        }

        .border-primary {
            border-color: rgb(var(--c-primary)) !important;
        }

        .bg-primary {
            background-color: rgb(var(--c-primary)) !important;
        }

        /* Optional: UL mit Klasse .sa-tabs geben (im Template gesetzt) */
        .sa-tabs a {
            border-bottom-width: 2px
        }

        .sa-tabs .current-menu-item>a,
        .sa-tabs .current_page_item>a,
        .sa-tabs .current-menu-ancestor>a,
        .sa-tabs a.is-active {
            color: rgb(var(--c-primary));
            border-color: rgb(var(--c-primary));
        }

        .sa-tabs .sa-icon>svg {
            width: 1rem;
            height: 1rem
        }
    </style>
<?php }, 31);

// Link-Styles (Top-Level Tabs & Submenu) + Active-State in Primärfarbe
add_filter('nav_menu_link_attributes', function ($atts, $item, $args, $depth) {
    if (($args->theme_location ?? '') !== 'primary') return $atts;

    $style   = get_theme_mod('sa_nav_style', 'tabs');      // tabs|pills
    $compact = get_theme_mod('sa_nav_compact', false);

    if ($depth === 0) {
        if ($style === 'pills') {
            $pad  = $compact ? 'px-2.5 py-2' : 'px-3 py-2.5';
            $base = "inline-flex items-center gap-1.5 {$pad} rounded-full no-underline group";
        } else { // tabs
            $pad  = $compact ? 'px-2 py-2' : 'px-2.5 py-3';
            $base = "inline-flex items-center gap-1.5 {$pad} border-b-2 border-transparent rounded-t-lg no-underline group";
        }
    } else {
        $base = 'relative flex items-center gap-2 px-3 py-2 rounded-md no-underline text-zinc-700 dark:text-zinc-200 text-sm';
    }

    $atts['class'] = trim(($atts['class'] ?? '') . ' ' . $base);

    // Active-Zustand
    $is_active = in_array('current-menu-item', (array)$item->classes, true)
        || in_array('current_page_item', (array)$item->classes, true)
        || in_array('current-menu-ancestor', (array)$item->classes, true);

    if ($is_active) {
        if ($depth === 0 && $style === 'tabs') {
            $atts['class'] .= ' text-[color:rgb(var(--c-primary))] border-[color:rgb(var(--c-primary))]';
        } else {
            $atts['class'] .= ' text-[color:rgb(var(--c-primary))]';
        }
        $atts['aria-current'] = 'page';
    }
    return $atts;
}, 10, 4);

// Icon in <a> injizieren (vor den Linktext). Unterstützt "outline" / "solid" + "solid:home" Syntax
if (function_exists('get_field')) {
    add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
        if (!function_exists('get_field')) return $item_output;

        $acf_id   = 'menu_item_' . $item->ID;
        $icon_key = trim((string) get_field('menu_icon', $acf_id)); // z.B. "home" ODER "solid:home"
        if ($icon_key === '') return $item_output;

        $set = (string) get_field('menu_icon_set', $acf_id);

        if (strpos($icon_key, ':') !== false) {
            [$maybeSet, $name] = explode(':', $icon_key, 2);
            $set = $set ?: $maybeSet;
            $icon_key = $name;
        } elseif (strpos($icon_key, '/') !== false) {
            [$maybeSet, $name] = explode('/', $icon_key, 2);
            $set = $set ?: $maybeSet;
            $icon_key = $name;
        }
        $set = $set ?: 'outline';

        $svg = sa_heroicon($icon_key, $set);
        if ($svg === '') return $item_output;

        // <a> sicher "group" geben
        $count = 0;
        $item_output = preg_replace('/(<a\b[^>]*class=")([^"]*)"/i', '$1$2 group"', $item_output, 1, $count);
        if (!$count) {
            $item_output = preg_replace('/(<a\b)([^>]*?)>/i', '$1$2 class="group">', $item_output, 1);
        }

        // Icon davor einfügen
        $icon_html = '<span class="sa-ico me-1.5" aria-hidden="true">' . $svg . '</span>';

        return preg_replace('/(<a\b[^>]*>)/i', '$1' . $icon_html, $item_output, 1);
    }, 10, 4);
}

/**
 * Heroicon loader – akzeptiert ($name, $set) und versucht Fallbacks (outline/solid…).
 * Unterstützt "solid:home" oder "outline/home" ebenfalls.
 */
function sa_heroicon(string $name, string $set = 'outline'): string
{
    $name = sanitize_file_name($name);
    $set  = ($set === 'solid') ? 'solid' : 'outline';

    $path = get_template_directory() . "/assets/heroicons/{$set}/{$name}.svg";
    if (!file_exists($path)) return '';

    $cache_key = "sa_icon:{$set}:{$name}";
    $cached    = wp_cache_get($cache_key, 'sa_icons');
    if ($cached !== false) return $cached;

    $svg = file_get_contents($path);

    // 1) XML-Header entfernen
    $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);

    // 2) Störende Attribute aus <svg> entfernen (Klassen + width/height)
    //    => Größe/Farbe kommen von außen (Wrapper/Link)
    $svg = preg_replace_callback(
        '/<svg\b([^>]*)>/i',
        function (array $m): string {
            $attrs = $m[1] ?? '';

            // width/height raus
            $attrs = preg_replace('/\s(?:width|height)=("|\').*?\1/i', '', $attrs);
            // class entfernen (Tailwind/size-* etc. sollen vom Wrapper kommen)
            $attrs = preg_replace('/\sclass=("|\')(.*?)\1/i', '', $attrs);

            // dekoratives Icon
            if (!preg_match('/\baria-hidden=/i', $attrs)) {
                $attrs .= ' aria-hidden="true" focusable="false"';
            }
            return '<svg' . $attrs . '>';
        },
        $svg,
        1
    );
    // 3) Sehr restriktiv whitelisten
    $allowed = [
        'svg'    => ['xmlns' => [], 'viewBox' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => [], 'aria-hidden' => [], 'focusable' => []],
        'path'   => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-linecap' => [], 'stroke-linejoin' => [], 'stroke-width' => []],
        'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => []],
        'rect'   => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'rx' => [], 'ry' => [], 'fill' => [], 'stroke' => []],
        'g'      => ['fill' => [], 'stroke' => []],
    ];
    $svg = wp_kses($svg, $allowed);

    wp_cache_set($cache_key, $svg, 'sa_icons', DAY_IN_SECONDS);
    return $svg;
}

// Füllt das Select "menu_icon" mit allen Dateinamen aus allen Sets (union, ohne .svg)
add_filter('acf/load_field/name=menu_icon', function ($field) {
    $bases   = [get_stylesheet_directory() . '/assets/heroicons', get_template_directory() . '/assets/heroicons'];
    $names   = [];
    foreach ($bases as $base) {
        if (!is_dir($base)) continue;
        foreach (glob($base . '/*', GLOB_ONLYDIR) as $dir) {
            foreach (glob($dir . '/*.svg') as $file) {
                $names[basename($file, '.svg')] = basename($file, '.svg');
            }
        }
    }
    ksort($names);
    $field['choices']    = $names;
    $field['allow_null'] = 1;
    $field['ui']         = 1;
    return $field;
});

// 2. Select "menu_icon_set" dynamisch aus vorhandenen Unterordnern (outline, solid, …)
add_filter('acf/load_field/name=menu_icon_set', function ($field) {
    $bases = [get_stylesheet_directory() . '/assets/heroicons', get_template_directory() . '/assets/heroicons'];
    $sets  = [];
    foreach ($bases as $base) {
        if (!is_dir($base)) continue;
        foreach (glob($base . '/*', GLOB_ONLYDIR) as $dir) {
            $key = basename($dir);
            $sets[$key] = $key;
        }
    }
    if (!$sets) $sets = ['outline' => 'outline', 'solid' => 'solid'];
    ksort($sets);

    $field['choices']       = $sets;
    $field['default_value'] = 'outline';
    $field['allow_null']    = 0;
    $field['ui']            = 1;
    return $field;
});
// Body-Klassen für Nav-Varianten (Tabs/Pills) + Kompakt
add_filter('body_class', function (array $classes) {
    $style = get_theme_mod('sa_nav_style', 'tabs');  // 'tabs' | 'pills'
    $classes[] = 'navstyle-' . sanitize_html_class($style);
    if (get_theme_mod('sa_nav_compact', false)) {
        $classes[] = 'nav-compact';
    }
    return $classes;
});
// === Hero-Bild im Customizer ================================================
add_action('customize_register', function (WP_Customize_Manager $c) {
    // Eigene Sektion
    $c->add_section('sa_hero', [
        'title'       => __('Startseite: Hero', 'stadt_apotheke_ilef'),
        'priority'    => 35,
        'description' => __('Hero-Bild und Optionen', 'stadt_apotheke_ilef'),
    ]);

    // Bild-Setting (speichert Attachment-ID)
    $c->add_setting('sa_hero_image_id', [
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    // Control (Medienauswahl, nur Bilder)
    $c->add_control(new WP_Customize_Media_Control($c, 'sa_hero_image_id', [
        'label'       => __('Hero-Bild', 'stadt_apotheke_ilef'),
        'section'     => 'sa_hero',
        'mime_type'   => 'image',
        'description' => __('Großes, querformatiges Bild wählen.'),
    ]));

    // Optional: Mindesthöhe (in vh)
    $c->add_setting('sa_hero_min_h', [
        'type'              => 'theme_mod',
        'default'           => 60,
        'sanitize_callback' => function ($v) {
            $v = (int)$v;
            return max(30, min(90, $v));
        },
        'transport'         => 'refresh',
    ]);
    /*
    $c->add_control('sa_hero_min_h', [
        'label'       => __('Mindesthöhe (vh)', 'stadt_apotheke_ilef'),
        'section'     => 'sa_hero',
        'type'        => 'range',
        'input_attrs' => ['min' => 30, 'max' => 90, 'step' => 5],
    ]);
    */
    // Overlay-Deckkraft (0–95 %)
    $c->add_setting('sa_hero_overlay', [
        'type' => 'theme_mod',
        'default' => 65,
        'sanitize_callback' => function ($v) {
            $v = (int)$v;
            return max(0, min(95, $v));
        }
    ]);
    $c->add_control('sa_hero_overlay', [
        'label' => __('Overlay-Stärke (%)', 'stadt_apotheke_ilef'),
        'section' => 'sa_hero',
        'type' => 'range',
        'input_attrs' => ['min' => 0, 'max' => 95, 'step' => 5],
    ]);

    // Textfarbe
    $c->add_setting('sa_hero_text', [
        'type' => 'theme_mod',
        'default' => 'light',
        'sanitize_callback' => function ($v) {
            return in_array($v, ['light', 'dark'], true) ? $v : 'light';
        }
    ]);
    $c->add_control('sa_hero_text', [
        'label' => __('Textfarbe auf Bild', 'stadt_apotheke_ilef'),
        'section' => 'sa_hero',
        'type' => 'radio',
        'choices' => ['light' => __('Hell (weiß)', 'stadt_apotheke_ilef'), 'dark' => __('Dunkel (schwarz)', 'stadt_apotheke_ilef')],
    ]);
});
// === Design & Theme (Nav-Stil + Dichte) =====================================
add_action('customize_register', function (WP_Customize_Manager $wp) {
    // Section (einmalig)
    if (!$wp->get_section('sa_design')) {
        $wp->add_section('sa_design', [
            'title'    => __('Design & Theme', 'stadt_apotheke_ilef'),
            'priority' => 30,
        ]);
    }

    // Setting: Tabs | Pills
    $wp->add_setting('sa_nav_style', [
        'default'           => 'tabs', // 'tabs' | 'pills'
        'sanitize_callback' => function ($v) {
            return in_array($v, ['tabs', 'pills'], true) ? $v : 'tabs';
        },
        'transport'         => 'refresh',
        'type'              => 'theme_mod',
    ]);
    $wp->add_control('sa_nav_style', [
        'section' => 'sa_design',
        'label'   => __('Navigation – Stil', 'stadt_apotheke_ilef'),
        'type'    => 'select',
        'choices' => [
            'tabs'  => __('Tabs (Unterstrich)', 'stadt_apotheke_ilef'),
            'pills' => __('Pills (gefüllt)', 'stadt_apotheke_ilef'),
        ],
    ]);

    // Setting: kompakt (wirkt für beide Stile)
    $wp->add_setting('sa_nav_compact', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'refresh',
        'type'              => 'theme_mod',
    ]);
    $wp->add_control('sa_nav_compact', [
        'section' => 'sa_design',
        'label'   => __('Navigation kompakter', 'stadt_apotheke_ilef'),
        'type'    => 'checkbox',
    ]);
});
add_action('wp_head', function () {
    if (!is_singular('post')) return;
    global $post;

    $title = wp_get_document_title();
    $desc  = wp_strip_all_tags(get_the_excerpt($post) ?: wp_trim_words($post->post_content, 24));
    $img   = has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'large') : '';

    echo "\n<!-- OG/Twitter (basic) -->\n";
    printf('<meta property="og:type" content="article" />' . "\n");
    printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($desc));
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url(get_permalink($post)));
    if ($img) printf('<meta property="og:image" content="%s" />' . "\n", esc_url($img));

    printf('<meta name="twitter:card" content="summary_large_image" />' . "\n");
    printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($desc));
    if ($img) printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($img));
    echo "<!-- /OG/Twitter -->\n";
}, 5);
// Thumbnails aktivieren + sinnvolle Größen
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(640, 360, true);             // default crop
    add_image_size('card', 640, 360, true);              // für Karten
});
// Bildgrößen für Shop-Karten
add_action('after_setup_theme', function () {
    add_image_size('sa_shop_card', 800, 600, true); // 4:3 für Karten
});

// Woo-Wrapper optional auf dein Container-Layout mappen
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', function () {
    echo '<main id="primary" class="min-h-[40vh]">';
}, 10);

add_action('woocommerce_after_main_content', function () {
    echo '</main>';
}, 10);

// Produktbild in Loop auf eigene Größe umbiegen (optional)
add_filter('single_product_archive_thumbnail_size', function ($size) {
    return 'sa_shop_card';
});
// Wie viele Spalten im Shop-Loop?
add_filter('loop_shop_columns', function () {
    return 3;
}, 999);

// Produkte pro Seite (optional)
add_filter('loop_shop_per_page', function () {
    return 12;
}, 999);
// Shop-Filter Sidebar
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => 'Shop Filter',
        'id'            => 'shop-filters',
        'description'   => 'Filter und Kategorien für den Shop.',
        'before_widget' => '<section class="mb-6 last:mb-0">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="mb-3 text-sm font-semibold tracking-wide text-zinc-600 dark:text-zinc-300">',
        'after_title'   => '</h3>',
    ]);
});
// Footer-Widgetbereiche
add_action('widgets_init', function () {
    $common = [
        'before_widget' => '<section class="footer-widget mb-6 last:mb-0">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="footer-title mb-3">',
        'after_title'   => '</h3>',
    ];

    register_sidebar(array_merge($common, [
        'name'        => __('Footer 1', 'stadt_apotheke_ilef'),
        'id'          => 'footer-1',
        'description' => __('Linke Fußspalte', 'stadt_apotheke_ilef'),
    ]));

    register_sidebar(array_merge($common, [
        'name'        => __('Footer 2', 'stadt_apotheke_ilef'),
        'id'          => 'footer-2',
        'description' => __('Mittlere Fußspalte', 'stadt_apotheke_ilef'),
    ]));

    register_sidebar(array_merge($common, [
        'name'        => __('Footer 3', 'stadt_apotheke_ilef'),
        'id'          => 'footer-3',
        'description' => __('Rechte Fußspalte', 'stadt_apotheke_ilef'),
    ]));

    register_sidebar(array_merge($common, [
        'name'        => __('Footer unten', 'stadt_apotheke_ilef'),
        'id'          => 'footer-bottom',
        'description' => __('Bereich in der unteren Fußzeile (Copyrightzeile)', 'stadt_apotheke_ilef'),
    ]));
});

// Kategorien-Kacheln im Shop-/Kategorieseiten-Grid komplett unterdrücken
add_filter('woocommerce_product_subcategories', function ($subcats) {
    if (is_shop()) return [];
    return $subcats;
}, 99);
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;

    if (is_shop() || is_product_category() || is_product_tag()) {
        $q->set('post_type', 'product');
    }
});
/**
 * Kategorien im Shop-Archiv unterdrücken (nur Produkte).
 */
add_action('init', function () {
    // Falls ein Theme die Ausgabe über den Hook macht:
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_product_categories', 10);
});
// Woo: "Unkategorisiert" im Produktkategorien-Widget ausblenden
add_filter('woocommerce_product_categories_widget_args', function ($args) {
    $slugs = array('uncategorized', 'unkategorisiert'); // je nach Sprache
    $exclude = array();

    foreach ($slugs as $slug) {
        if ($term = get_term_by('slug', $slug, 'product_cat')) {
            $exclude[] = (int) $term->term_id;
        }
    }

    if (!empty($exclude)) {
        $args['exclude'] = array_merge($args['exclude'] ?? array(), $exclude);
    }
    return $args;
});

add_action('after_setup_theme', function () {
    // Shop-Loop
    remove_action(
        'woocommerce_before_shop_loop_item_title',
        'woocommerce_show_product_loop_sale_flash',
        10
    );
    // Einzelprodukt
    remove_action(
        'woocommerce_before_single_product_summary',
        'woocommerce_show_product_sale_flash',
        10
    );
});

if (! function_exists('sa_sale_badge_with_percent')) {
    function sa_sale_badge_with_percent(WC_Product $product)
    {
        $regular = (float) wc_get_price_to_display($product, ['price' => $product->get_regular_price()]);
        $sale    = (float) wc_get_price_to_display($product, ['price' => $product->get_sale_price()]);

        if ($regular <= 0 || $sale <= 0 || $sale >= $regular) {
            return ''; // kein Badge, wenn kein echter Rabatt
        }

        $percent = floor((1 - ($sale / $regular)) * 100);
        $label   = sprintf(_x('%d%%', 'sale percent', 'stadt'), $percent); // nur Zahl + %

        return '
        <span class="sa-badge-sale" aria-label="' . esc_attr__('Im Angebot', 'stadt') . '">
            <span class="sa-badge-sale__txt">' . esc_html($label) . '</span>
        </span>';
    }
}

add_filter('woocommerce_loop_add_to_cart_link', function ($link) {
    return preg_replace('/>(.*?)<\/a>/s', '><span class="sr-only">$1</span></a>', $link);
}, 10, 1);

// Cart-Zähler im Header per AJAX aktualisieren
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

    ob_start(); ?>
    <span
        class="cart-count-badge absolute -top-1.5 -right-1.5 grid h-5 min-w-5 place-items-center rounded-full bg-primary px-1 text-[11px] font-semibold text-white <?php echo $count ? '' : 'is-empty'; ?>"
        aria-live="polite" aria-atomic="true">
        <?php echo $count ?: ''; ?>
    </span>
<?php
    $html = ob_get_clean();

    // Update any existing badge(s)
    $fragments['.cart-count-badge'] = $html;

    return $fragments;
});
add_action('after_setup_theme', function () {
    load_theme_textdomain('stadt_apotheke_ilef', get_template_directory() . '/languages');
});

/**
 * Register custom block styles.
 */
if (! function_exists('sai_block_styles')) :
    /**
     * Register custom block styles
     *
     * @since 1.0
     * @return void
     */
    function sai_block_styles()
    {

        register_block_style(
            'core/details',
            array(
                'name'         => 'arrow-icon-details',
                'label'        => __('Arrow icon', 'stadt_apotheke_ilef'),
                /*
				 * Styles for the custom Arrow icon style of the Details block
				 */
                'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
            )
        );

        register_block_style(
            'core/post-terms',
            array(
                'name'         => 'pill',
                'label'        => __('Pill', 'stadt_apotheke_ilef'),
                /*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
                'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
            )
        );

        register_block_style(
            'core/list',
            array(
                'name'         => 'checkmark-list',
                'label'        => __('Checkmark', 'stadt_apotheke_ilef'),
                /*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
                'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
            )
        );

        register_block_style(
            'core/navigation-link',
            array(
                'name'         => 'arrow-link',
                'label'        => __('With arrow', 'stadt_apotheke_ilef'),
                /*
				 * Styles for the custom arrow nav link block style
				 */
                'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
            )
        );

        register_block_style(
            'core/heading',
            array(
                'name'         => 'asterisk',
                'label'        => __('With asterisk', 'stadt_apotheke_ilef'),
                'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}

				/* Hide the asterisk if the heading has no content, to avoid using empty headings to display the asterisk only, which is an A11Y issue */
				.is-style-asterisk:empty:before {
					content: none;
				}

				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}

				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}

				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}

				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
            )
        );
    }
endif;
add_action('init', 'sai_block_styles');

/**
 * Enqueue block stylesheets.
 */
if (! function_exists('sai_block_stylesheets')) :
    /**
     * Enqueue custom block stylesheets
     *
     * @since 1.0
     * @return void
     */
    function sai_block_stylesheets()
    {
        /**
         * Styles nur laden, wenn der Block gerendert wird.
         * Nutzung von get_theme_file_uri()/path statt *parent* im eigenen Theme.
         */
        wp_enqueue_block_style(
            'core/button',
            array(
                'handle' => 'sai-button-style-outline',
                'src'    => get_theme_file_uri('assets/css/button-outline.css'),
                'ver'    => wp_get_theme(get_template())->get('Version'),
                'path'   => get_theme_file_path('assets/css/button-outline.css'),
            )
        );
    }
endif;
add_action('init', 'sai_block_stylesheets');

/**
 * Register pattern categories.
 */
if (! function_exists('sai_pattern_categories')) :
    /**
     * Register pattern categories
     *
     * @since 1.0
     * @return void
     */
    function sai_pattern_categories()
    {
        register_block_pattern_category(
            'sai_page',
            array(
                'label'       => _x('Pages', 'Block pattern category', 'stadt_apotheke_ilef'),
                'description' => __('A collection of full page layouts.', 'stadt_apotheke_ilef'),
            )
        );
    }
endif;
add_action('init', 'sai_pattern_categories');
