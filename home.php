<?php get_header(); ?>
<main class="container mx-auto max-w-6xl px-4 py-10">
    <header class="mb-8 flex items-center justify-between">
        <h1 class="text-3xl font-extrabold tracking-tight">News</h1>
    </header>

    <?php if (have_posts()): ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php while (have_posts()): the_post(); ?>
                <article <?php post_class('post-card group rounded-2xl border border-[rgb(var(--c-border))] bg-[rgb(var(--c-surface))] shadow-sm overflow-hidden'); ?>>
                    <a href="<?php the_permalink(); ?>" class="block">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="aspect-[16/10] overflow-hidden">
                                <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition duration-300 group-hover:scale-[1.02]']); ?>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="p-4 sm:p-5">
                        <h3 class="post-title text-lg font-semibold leading-snug">
                            <a href="<?php the_permalink(); ?>" class="no-underline transition-colors duration-150">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <p class="mt-2 text-sm text-[rgb(var(--c-on-surface)/.80)]">
                            <?php echo wp_trim_words(get_the_excerpt(), 28); ?>
                        </p>

                        <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-xs meta">
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                            <?php
                            $cats = get_the_category();
                            if ($cats) {
                                echo '<span aria-hidden="true">•</span>';
                                foreach ($cats as $i => $c) {
                                    echo '<a href="' . esc_url(get_category_link($c)) . '">' . esc_html($c->name) . '</a>';
                                    if ($i < count($cats) - 1) echo '<span>,</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>

        <nav class="mt-10 flex items-center justify-between">
            <div><?php previous_posts_link('← Neuere Beiträge'); ?></div>
            <div><?php next_posts_link('Ältere Beiträge →'); ?></div>
        </nav>
    <?php else: ?>
        <p class="text-zinc-600 dark:text-zinc-400">Keine Beiträge gefunden.</p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>