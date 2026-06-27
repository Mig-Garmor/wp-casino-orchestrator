<?php get_header(); ?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <p class="eyebrow">Casino Reviews</p>
            <h1>Online Casinos</h1>
            <p>Browse casino reviews, bonuses, and market availability.</p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="card-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/casino-card'); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p>No casinos found.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>