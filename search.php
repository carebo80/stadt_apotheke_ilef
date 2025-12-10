<!-- Header/Badge -->
<section class="bg-gradient-to-r from-blue-500 to-indigo-500 py-10 mb-10">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold text-white">
            <?php
            printf(
                esc_html__('Suchergebnisse für: %s', 'stadt_apotheke_ilef'),
                '<span class="font-light">' . esc_html(get_search_query()) . '</span>'
            );
            ?>
        </h1>
    </div>
</section>

<!-- Ergebnisse -->
<div class="container mx-auto px-4">
    <?php if (have_posts()) : ?>
        <div class="grid gap-6 md:grid-cols-2">
            <?php while (have_posts()) : the_post(); ?>
                <article class="rounded-xl border border-zinc-200 p-6 hover:bg-zinc-50 transition">
                    <h2 class="text-xl font-semibold mb-2">
                        <a class="!no-underline hover:underline" href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <p class="text-zinc-600"><?php echo wp_trim_words(get_the_excerpt(), 26); ?></p>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="mt-10">
            <?php the_posts_pagination([
                'prev_text' => '« ' . __('Zurück', 'stadt_apotheke_ilef'),
                'next_text' => __('Weiter', 'stadt_apotheke_ilef') . ' »',
            ]); ?>
        </div>

    <?php else : ?>
        <div class="rounded-xl border border-zinc-200 p-8 text-zinc-700">
            <?php esc_html_e('Keine Treffer gefunden. Bitte versuchen Sie einen anderen Suchbegriff.', 'stadt_apotheke_ilef'); ?>
        </div>
    <?php endif; ?>
</div>