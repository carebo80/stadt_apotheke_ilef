<?php

/**
 * Partner-Logos – einheitliche API
 * Args: ids(array), title(string), grayscale(bool)
 * Fallback: theme_mod('sa_partner_logo_ids')
 */

$ids       = $args['ids'] ?? array_filter(array_map(
    'absint',
    explode(',', (string) get_theme_mod('sa_partner_logo_ids', ''))
));
$title     = $args['title'] ?? __('Unsere Partner', 'stadt_apotheke_ilef');
$grayscale = array_key_exists('grayscale', $args) ? (bool)$args['grayscale'] : true;

// Fallback-Chips wenn keine IDs vorhanden
$fallback = ['Partner A', 'Partner B', 'Partner C'];
?>
<section id="partners" class="section-band logos-section" aria-labelledby="partners-title">
    <div class="container mx-auto px-4">
        <h2 id="partners-title" class="section-title">Unsere Partner</h2>

        <!-- HIER die Klasse austauschen -->
        <div class="logos-grid">
            <?php foreach ($ids as $id): ?>
                <div class="logo-chip">
                    <?php echo wp_get_attachment_image($id, 'partner_logo', false, [
                        'class' => 'partner-logo',
                        'loading' => 'lazy',
                    ]); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    /* Gemeinsame Styles für Logos-Sektionen */
    .logos-section .logo-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 3.25rem;
        width: 10rem;
        padding: .5rem 1rem;
        border-radius: 9999px;
        border: 1px solid rgb(228 228 231);
        background-color: rgb(244 244 245 / .80);
    }

    html.dark .logos-section .logo-badge {
        border-color: rgb(63 63 70);
        background-color: rgb(39 39 42 / .35);
    }

    .logos-section.is-grayscale img {
        filter: grayscale(100%) contrast(1.05);
        opacity: .9
    }

    .logos-section.is-grayscale img:hover {
        opacity: 1
    }
</style>