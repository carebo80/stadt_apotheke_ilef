<?php

/**
 * Template Name: Front Page
 *
 * @package TailPress
 * <?php get_template_part('template-parts/cta'); ?>
 */

get_header(); ?>

<main>
    <?php get_template_part('template-parts/hero'); ?>
    <?php get_template_part('template-parts/features'); ?>

    <?php
    // Partner (zieht notfalls sa_partner_logo_ids)
    get_template_part('template-parts/partners', null, [
        //'ids'       => [325, 324],
        //'title'     => 'Unsere Partner',    // optional
        //'grayscale' => true,                // optional
    ]);

    // Payments (zieht notfalls sa_payment_logo_ids)
    get_template_part('template-parts/payments', null, [
        // 'ids'       => [45,46,47,48,49,50], // optional override
        // 'title'     => 'Zahlungsmöglichkeiten',
        // 'grayscale' => true,
    ]);
    ?>
</main>


<?php get_footer(); ?>