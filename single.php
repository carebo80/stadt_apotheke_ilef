<?php

/**
 * Single post template file.
 *
 * @package TailPress
 */

$blog_id = get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : get_post_type_archive_link('post');

get_header();
?>

<main id="site-main" class="py-10 lg:py-14">
    <div class="container mx-auto max-w-3xl px-4">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php // Kategorien als Chips 
                ?>
                <div class="mb-4 flex flex-wrap gap-2">
                    <?php foreach (get_the_category() as $cat): ?>
                        <a href="<?php echo esc_url(get_category_link($cat)); ?>"
                            class="inline-flex items-center rounded-full border border-[rgb(var(--c-border))]
                    px-3 py-1 text-xs font-medium text-[color:rgb(var(--c-on-surface)/.85)]
                    hover:border-[color:rgb(var(--c-primary))] hover:text-[color:rgb(var(--c-primary))]">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="mb-3 flex items-center gap-3">
                    <a href="<?php echo esc_url($blog_url); ?>" class="sa-chip">
                        <!-- optionales Heroicon: Pfeil links -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.5 19.5 3 12l7.5-7.5 1.06 1.06L6.12 11.0H21v1.99H6.12l5.44 5.44-1.06 1.06Z" />
                        </svg>
                        <span>Zurück zum Blog</span>
                    </a>
                </div>
                <h1 class="mb-2 text-4xl/tight md:text-5xl/tight font-extrabold text-[color:rgb(var(--c-on-surface))]">
                    <?php the_title(); ?>
                </h1>

                <?php // Meta: Autor + Datum 
                ?>
                <div class="mb-6 text-sm text-[color:rgb(var(--c-on-surface)/.6)]">
                    <span><?php the_author(); ?></span>
                    <span class="mx-2">•</span>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>
                </div>

                <?php // Hero-Bild (optional) 
                ?>
                <?php if (has_post_thumbnail()): ?>
                    <figure class="mb-8 overflow-hidden rounded-3xl">
                        <?php the_post_thumbnail('large', ['class' => 'w-full h-auto']); ?>
                    </figure>
                <?php endif; ?>

                <?php // Inhalt mit angenehmer Typo 
                ?>
                <article class="sa-prose">
                    <?php the_content(); ?>
                </article>

                <?php // Tags (wenn benutzt) 
                ?>
                <?php the_tags(
                    '<div class="mt-8 flex flex-wrap gap-2"><span class="text-sm opacity-70 mr-1">Tags:</span>',
                    '',
                    '</div>'
                ); ?>

                <?php // Prev/Next Navigation 
                ?>
                <nav class="mt-10 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <?php previous_post_link(
                            '<span class="block text-xs opacity-70 mb-1">Vorheriger Beitrag</span><span class="line-clamp-1">%link</span>',
                            '%title',
                            true
                        ); ?>
                    </div>
                    <div class="text-right min-w-0">
                        <?php next_post_link(
                            '<span class="block text-xs opacity-70 mb-1">Nächster Beitrag</span><span class="line-clamp-1">%link</span>',
                            '%title',
                            true
                        ); ?>
                    </div>
                </nav>
                <?php get_template_part('template-parts/related-posts'); ?>
                <?php // Kommentare (optional) 
                ?>
                <section class="mt-10">
                    <?php comments_template(); ?>
                </section>

        <?php endwhile;
        endif; ?>
    </div>
</main>

<?php
get_footer();
