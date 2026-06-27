<?php get_header(); ?>

<main class="site-main">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="entry">
                <h1><?php the_title(); ?></h1>

                <div class="entry-meta">
                    <span><?php echo esc_html(get_the_date()); ?></span>
                </div>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>