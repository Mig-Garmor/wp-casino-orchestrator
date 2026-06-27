<?php get_header(); ?>

<main class="site-main">
    <section class="hero">
        <div class="container">
            <p class="eyebrow">Casino Affiliate Demo</p>
            <h1>Find trusted online casino offers for regulated North American markets.</h1>
            <p>
                This demo project is built to practice WordPress themes, plugins,
                custom post types, metadata, template hierarchy, and affiliate-style content.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Featured casinos</h2>
                <p>Casino cards will be powered by the plugin custom post type later.</p>
            </div>

            <?php if (post_type_exists('casino')) : ?>
                <?php
                $casino_query = new WP_Query([
                    'post_type' => 'casino',
                    'posts_per_page' => 6,
                ]);
                ?>

                <?php if ($casino_query->have_posts()) : ?>
                    <div class="card-grid">
                        <?php while ($casino_query->have_posts()) : $casino_query->the_post(); ?>
                            <?php get_template_part('template-parts/casino-card'); ?>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>No casinos have been added yet.</p>
                <?php endif; ?>
            <?php else : ?>
                <div class="card-grid">
                    <article class="card">
                        <h3>Casino custom post type pending</h3>
                        <p>The plugin will register the casino post type later.</p>
                    </article>

                    <article class="card">
                        <h3>Theme is working</h3>
                        <p>The theme is active and ready for casino templates.</p>
                    </article>

                    <article class="card">
                        <h3>Next step</h3>
                        <p>Build the plugin to register casinos and casino metadata.</p>
                    </article>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>