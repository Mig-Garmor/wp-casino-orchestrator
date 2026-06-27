<?php get_header(); ?>

<main class="site-main">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $bonus = get_post_meta(get_the_ID(), '_casino_bonus', true);
            $rating = get_post_meta(get_the_ID(), '_casino_rating', true);
            $regions = get_post_meta(get_the_ID(), '_casino_regions', true);
            ?>

            <article class="casino-review">
                <p class="eyebrow">Casino Review</p>
                <h1><?php the_title(); ?></h1>

                <div class="casino-meta">
                    <?php if ($rating) : ?>
                        <span>Rating: <?php echo esc_html($rating); ?>/5</span>
                    <?php endif; ?>

                    <?php if ($bonus) : ?>
                        <span>Bonus: <?php echo esc_html($bonus); ?></span>
                    <?php endif; ?>

                    <?php if ($regions) : ?>
                        <span>Regions: <?php echo esc_html($regions); ?></span>
                    <?php endif; ?>
                </div>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>