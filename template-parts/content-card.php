<?php
$thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : null;
$cats  = get_the_category();
?>
<article class="blog-card group relative overflow-hidden rounded-2xl border border-[rgb(var(--c-border))] bg-[rgb(var(--c-surface))] shadow-sm hover:shadow-md transition-shadow">
    <a href="<?php the_permalink(); ?>" class="flex flex-col h-full">

        <!-- Bild -->
        <div class="relative aspect-[16/9] overflow-hidden">
            <?php if ($thumb): ?>
                <img class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
            <?php else: ?>
                <div class="absolute inset-0 bg-gradient-to-br from-white to-[rgb(var(--c-primary)/0.12)] dark:from-zinc-900 dark:to-zinc-800"></div>
                <svg class="absolute inset-0 m-auto h-10 w-10 text-[color:rgb(var(--c-on-surface)/0.25)]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M4 6h16v12H4zM4 6l6 6 4-4 6 6" stroke="currentColor" stroke-width="1.5" fill="none" />
                </svg>
            <?php endif; ?>

            <!-- Kategorie-Badges -->
            <?php if ($cats): ?>
                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                    <?php foreach ($cats as $c): ?>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium bg-white/90 text-[rgb(var(--c-primary))] dark:bg-white/10 dark:text-white/90 backdrop-blur">
                            <?php echo esc_html($c->name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="flex flex-col gap-3 p-4 sm:p-5">
            <h2 class="text-lg sm:text-xl font-semibold leading-snug text-[rgb(var(--c-on-surface))] group-hover:text-[rgb(var(--c-primary))] transition-colors">
                <?php the_title(); ?>
            </h2>

            <p class="line-clamp-3 text-sm text-[color:rgb(var(--c-on-surface)/0.75)]">
                <?php echo wp_strip_all_tags(get_the_excerpt()); ?>
            </p>

            <div class="mt-auto flex items-center justify-between pt-2 text-xs text-[color:rgb(var(--c-on-surface)/0.6)]">
                <span><?php echo get_the_date(); ?></span>
                <span class="inline-flex items-center gap-1 text-[rgb(var(--c-primary))]">
                    Weiterlesen
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13.5 5.5 20 12l-6.5 6.5-.7-.7L17.8 12l-5-5.5.7-.7z" />
                    </svg>
                </span>
            </div>
        </div>
    </a>
</article>