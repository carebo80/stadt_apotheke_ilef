<?php
defined('ABSPATH') || exit;
global $product;

if (empty($product) || ! $product->is_visible()) return;
?>

<li <?php wc_product_class('group flex flex-col rounded-2xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900', $product); ?>>

	<!-- THUMB + FAB -->
	<!-- THUMB + BADGE + FAB -->
	<div class="relative overflow-hidden rounded-t-2xl">
		<a href="<?php the_permalink(); ?>" class="relative block overflow-hidden rounded-t-2xl">
			<?php
			// Sale-Badge mit Prozent
			if ($product->is_on_sale()) :
				echo sa_sale_badge_with_percent($product); // <— Funktion unten in functions.php
			endif;

			woocommerce_template_loop_product_thumbnail();
			?>
		</a>
	</div>

	<div class="flex flex-col gap-2 p-4">
		<?php echo wc_get_product_category_list($product->get_id(), ', ', '<div class="text-xs text-zinc-500">', '</div>'); ?>

		<a href="<?php the_permalink(); ?>" class="no-underline">
			<h3 class="line-clamp-2 text-base font-semibold leading-snug transition group-hover:text-[color:rgb(var(--c-primary))]">
				<?php the_title(); ?>
			</h3>
		</a>

		<div class="mt-1"><?php woocommerce_template_loop_price(); ?></div>

		<div class="mt-auto pt-3">
			<?php woocommerce_template_loop_add_to_cart(); ?>
			<!-- wenn du NUR den runden FAB willst, kannst du diese Zeile entfernen -->
		</div>
	</div>
</li>