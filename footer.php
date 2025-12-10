<?php if (!defined('ABSPATH')) exit; ?>

<footer id="site-footer" class="section-band footer-band footer--sep">
    <div class="container mx-auto px-4">
        <div class="grid gap-8 md:grid-cols-3">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="ft-col"><?php dynamic_sidebar("footer-$i"); ?></div>
            <?php endfor; ?>
        </div>

        <div class="ft-bottom mt-10 pt-6 border-t">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-center">
                <?php if (has_nav_menu('secondary')): ?>
                    <nav aria-label="<?php esc_attr_e('Footernavigation', 'stadt_apotheke_ilef'); ?>"
                        class="ft-nav order-2 md:order-1">
                        <?php wp_nav_menu([
                            'theme_location' => 'secondary',
                            'container'      => false,
                            'menu_class'     => 'flex flex-wrap items-center gap-x-4 gap-y-2',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]); ?>
                    </nav>
                <?php endif; ?>

                <p class="ft-copy text-sm w-full text-center order-1 md:order-2">
                    <?php if (is_active_sidebar('footer-bottom')) : ?>
                        <?php dynamic_sidebar('footer-bottom'); ?>
                    <?php else : ?>
                        © <?php echo date('Y'); ?> Stadt Apotheke Illnau-Effretikon
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>