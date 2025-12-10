    <style>
        /* Card-Rahmen/Shadow – BG der Card bleibt transparent */
        #features .feature-card {
            border: 1px solid rgb(var(--c-border));
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
            background: transparent;
        }

        html.dark #features .feature-card {
            border-color: rgb(63 63 70);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .35);
            background: transparent;
        }

        /* Nur der Body der Karte bekommt die Fläche (hell/dunkel) */
        #features .card-body {
            background: #fff;
            color: rgb(24 24 27);
        }

        html.dark #features .card-body {
            background: rgb(24 24 27);
            color: rgb(244 244 245);
        }

        /* Interaktion */
        #features .feature-card a {
            text-decoration: none !important;
        }

        #features .feature-card a:hover h3 {
            color: rgb(var(--c-primary));
        }
    </style>
    <section id="features" class="section-band">
        <div class="container mx-auto px-4">

            <!-- Überschrift über dem Grid -->
            <h2 class="section-title">
                Unsere Dienstleistungen
            </h2>

            <div class="grid gap-6 sm:grid-cols-3 lg:grid-cols-4">
                <!-- Karte 1 -->
                <article class="feature-card group h-full rounded-2xl overflow-hidden
               border border-zinc-200 dark:border-zinc-800
               bg-white shadow-sm hover:shadow-md transition">
                    <!-- Bild kann verlinkt sein -->
                    <a href="#" class="block">
                        <img class="w-full aspect-[4/3] object-cover"
                            src="/wp-content/uploads/2025/09/beratung.png"
                            alt="Online Shop">
                    </a>

                    <!-- Card-Body: immer WEISS, also KEINE dark:* Textfarben -->
                    <div class="p-5 bg-white text-zinc-900">
                        <h3 class="text-xl font-semibold mb-1">
                            <a href="#" class="no-underline group-hover:text-[rgb(var(--c-primary))]">
                                Beratung &amp; Service
                            </a>
                        </h3>
                        <!-- Absatz ist KEIN Link mehr, gut lesbar auf weiß -->
                        <p class="text-zinc-700">
                            Persönliche, pharmazeutische Betreuung auf höchstem Niveau.
                        </p>
                    </div>
                </article>

                <!-- Karte 2 -->
                <article class="feature-card group h-full rounded-2xl overflow-hidden
               border border-zinc-200 dark:border-zinc-800
               bg-white shadow-sm hover:shadow-md transition">
                    <!-- Bild kann verlinkt sein -->
                    <a href="#" class="block">
                        <img class="w-full aspect-[4/3] object-cover"
                            src="/wp-content/uploads/2025/09/online_shop.png"
                            alt="Online Shop">
                    </a>

                    <!-- Card-Body: immer WEISS, also KEINE dark:* Textfarben -->
                    <div class="p-5 bg-white text-zinc-900">
                        <h3 class="text-xl font-semibold mb-1">
                            <a href="#" class="no-underline group-hover:text-[rgb(var(--c-primary))]">
                                Online Shop
                            </a>
                        </h3>
                        <!-- Absatz ist KEIN Link mehr, gut lesbar auf weiß -->
                        <p class="text-zinc-700">
                            Bestellen Sie pharmazeutische und Schönheitsprodukte bequem online.
                        </p>
                    </div>
                </article>

                <!-- Karte 3 -->
                <article class="feature-card group h-full rounded-2xl overflow-hidden
               border border-zinc-200 dark:border-zinc-800
               bg-white shadow-sm hover:shadow-md transition">
                    <!-- Bild kann verlinkt sein -->
                    <a href="#" class="block">
                        <img class="w-full aspect-[4/3] object-cover"
                            src="/wp-content/uploads/2025/09/termin.png"
                            alt="Termin buchen">
                    </a>

                    <!-- Card-Body: immer WEISS, also KEINE dark:* Textfarben -->
                    <div class="p-5 bg-white text-zinc-900">
                        <h3 class="text-xl font-semibold mb-1">
                            <a href="#" class="no-underline group-hover:text-[rgb(var(--c-primary))]">
                                Termin online buchen
                            </a>
                        </h3>
                        <!-- Absatz ist KEIN Link mehr, gut lesbar auf weiß -->
                        <p class="text-zinc-700">
                            Buchen Sie eine persönliche Beratung online.
                        </p>
                    </div>
                </article>

                <!-- Karte 4 -->
                <article class="feature-card group h-full rounded-2xl overflow-hidden
               border border-zinc-200 dark:border-zinc-800
               bg-white shadow-sm hover:shadow-md transition">
                    <!-- Bild kann verlinkt sein -->
                    <a href="#" class="block">
                        <img class="w-full aspect-[4/3] object-cover"
                            src="/wp-content/uploads/2025/09/rezepte_upload.png"
                            alt="Termin buchen">
                    </a>

                    <!-- Card-Body: immer WEISS, also KEINE dark:* Textfarben -->
                    <div class="p-5 bg-white text-zinc-900">
                        <h3 class="text-xl font-semibold mb-1">
                            <a href="#" class="no-underline group-hover:text-[rgb(var(--c-primary))]">
                                Rezepte Upload
                            </a>
                        </h3>
                        <!-- Absatz ist KEIN Link mehr, gut lesbar auf weiß -->
                        <p class="text-zinc-700">
                            Laden Sie Ihr Medikamentenrezept bequem digital hoch.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>