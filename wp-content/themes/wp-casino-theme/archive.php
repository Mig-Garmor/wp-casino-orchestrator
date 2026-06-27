<?php get_header(); ?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <h1><?php the_archive_title(); ?></h1>
            <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="card-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content-card'); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p>No content found.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>