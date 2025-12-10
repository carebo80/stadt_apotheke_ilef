<?php
if (!is_single()) return;

$cats = wp_get_post_categories(get_the_ID());
if (!$cats) return;

$q = new WP_Query([
    'post_type'           => 'post',
    'posts_per_page'      => 3,
    'post__not_in'        => [get_the_ID()],
    'ignore_sticky_posts' => true,
    'category__in'        => $cats,
]);

if ($q->have_posts()): ?>
    <section class="mt-12 related-posts">
        <h2 class="mb-5 text-xl font-extrabold text-[color:rgb(var(--c-on-surface))]">
            Verwandte Beiträge
        </h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php while ($q->have_posts()): $q->the_post(); ?>
                <article class="group overflow-hidden rounded-2xl border border-[rgb(var(--c-border))]
                        bg-[rgb(var(--c-surface))] transition hover:shadow-lg">
                    <a href="<?php the_permalink(); ?>" class="block no-underline">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="aspect-[16/10] overflow-hidden">
                                <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover transition group-hover:scale-[1.03]']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="mb-2 line-clamp-2 font-semibold text-[color:rgb(var(--c-on-surface))] group-hover:text-[color:rgb(var(--c-primary))]">
                                <?php the_title(); ?>
                            </h3>
                            <p class="mb-3 text-sm text-[color:rgb(var(--c-on-surface)/.7)] line-clamp-3">
                                <?php echo esc_html(wp_strip_all_tags(get_the_excerpt() ?: wp_trim_words(get_the_content(), 24))); ?>
                            </p>
                            <div class="text-xs opacity-70">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    </section>
<?php endif; ?>