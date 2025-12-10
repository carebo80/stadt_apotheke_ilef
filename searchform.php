<?php
// Standard-Form (z.B. falls ein Plugin nur get_search_form() aufruft)
// 'post_type'   => '', für gesamtes WP durchsuchen oder nur Woo: 'post_type'   => 'product',
get_template_part('template-parts/search/form', null, [
    'variant'     => 'full',
    'post_type'   => 'product',
    'placeholder' => __('Suche …', 'stadt_apotheke_ilef'),
]);
