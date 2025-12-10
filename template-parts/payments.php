<?php
$ids_raw = (string) get_theme_mod('sa_payment_logo_ids', '');
$ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $ids_raw))));
$grayscale = false; // oder get_theme_mod('sa_payment_grayscale', false);
?>
<section id="payments" class="section-band logos-section" aria-labelledby="payments-title">
    <div class="container mx-auto px-4">
        <h2 id="payments-title" class="section-title">Zahlungsmöglichkeiten</h2>

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


<?php if ($grayscale): ?>
    <style>
        /* dezentes Graustufen-Optik fürs Grid, auf Hover farbig */
        #payments.is-grayscale img {
            filter: grayscale(100%);
            opacity: .85;
            transition: filter .2s ease, opacity .2s ease;
        }

        #payments.is-grayscale img:hover {
            filter: none;
            opacity: 1;
        }
    </style>
<?php endif; ?>