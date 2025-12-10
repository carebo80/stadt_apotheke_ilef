<?php
$hero_id   = (int) get_theme_mod('sa_hero_image_id', 0);
$min_vh    = (int) get_theme_mod('sa_hero_min_h', 60);
$overlay   = max(0, min(95, (int) get_theme_mod('sa_hero_overlay', 65))); // %
$text_mode = get_theme_mod('sa_hero_text', 'light'); // 'light' | 'dark'
$txtClass  = $text_mode === 'dark' ? 'text-zinc-900' : 'text-white';
$min_h_cls = 'min-h-[' . $min_vh . 'vh]';
?>

<section class="hero relative <?php echo esc_attr($txtClass . ' ' . $min_h_cls); ?>" style="--hero-a: <?php echo $overlay / 100; ?>;">
    <?php
    if ($hero_id) {
        echo wp_get_attachment_image($hero_id, 'full', false, [
            'class' => 'absolute inset-0 h-full w-full object-cover',
            'alt' => '',
            'fetchpriority' => 'high'
        ]);
    }
    ?>
    <div class="hero__overlay absolute inset-0 pointer-events-none"></div>

    <div class="relative container mx-auto px-4 text-center">
        <div class="hero-copy mx-auto max-w-4xl py-10 md:py-16 lg:py-20">
            <h1 class="text-[clamp(2.2rem,4vw+1rem,4.2rem)] md:text-[clamp(2.8rem,3.3vw+1rem,5rem)]
                 font-extrabold leading-tight tracking-tight">
                Willkommen in der Stadt Apotheke Illnau-Effretikon
            </h1>
            <p class="mt-4 text-lg md:text-2xl font-medium opacity-95">
                Ihre Gesundheit liegt uns am Herzen – digital, lokal und persönlich
            </p>
            <a href="#features" class="btn btn-primary rounded-full mt-6 px-6 py-2">Mehr erfahren</a>
        </div>
    </div>
</section>