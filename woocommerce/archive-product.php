<?php
defined('ABSPATH') || exit;
get_header('shop'); ?>

<div class="container mx-auto px-4 lg:px-6">
	<?php do_action('woocommerce_before_main_content'); ?>

	<?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
		<h1 class="sr-only"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 lg:gap-8">
		<!-- Sidebar -->
		<aside class="lg:sticky lg:top-24 h-fit rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-[rgb(var(--c-surface))] p-4">
			<?php if (is_active_sidebar('shop-filters')) : dynamic_sidebar('shop-filters');
			endif; ?>
		</aside>

		<!-- Content -->
		<main>
			<?php do_action('woocommerce_before_shop_loop'); ?>

			<?php if (woocommerce_product_loop()) : ?>
				<?php woocommerce_product_loop_start(); ?>
				<?php if (wc_get_loop_prop('total')) : ?>
					<?php while (have_posts()) : the_post();
						wc_get_template_part('content', 'product');
					endwhile; ?>
				<?php endif; ?>
				<?php woocommerce_product_loop_end(); ?>

				<?php do_action('woocommerce_after_shop_loop'); ?>
			<?php else : ?>
				<?php do_action('woocommerce_no_products_found'); ?>
			<?php endif; ?>
		</main>
	</div>

	<?php do_action('woocommerce_after_main_content'); ?>
</div>

<?php get_footer('shop'); ?>