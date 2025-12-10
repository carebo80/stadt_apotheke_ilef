<?php
// template-parts/search/form.php
$variant     = $args['variant']     ?? 'desktop';   // 'desktop' | 'mobile'
$post_type   = $args['post_type']   ?? 'product';
$placeholder = $args['placeholder'] ?? __('Produkte suchen…', 'stadt_apotheke_ilef');

$wrap_cls = 'sa-search sa-search--' . esc_attr($variant);
?>
<form role="search" method="get" class="<?php echo $wrap_cls; ?>" action="<?php echo esc_url(home_url('/')); ?>">
    <input
        type="search"
        name="s"
        class="search-field"
        value="<?php echo esc_attr(get_search_query()); ?>"
        placeholder="<?php echo esc_attr($placeholder); ?>"
        autocomplete="off" />
    <input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Suchen', 'stadt_apotheke_ilef'); ?>">
        <?php echo function_exists('sa_icon') ? sa_icon('search', 'h-4 w-4') : '🔎'; ?>
    </button>
</form>