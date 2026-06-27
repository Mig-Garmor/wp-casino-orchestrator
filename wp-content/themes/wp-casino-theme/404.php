<?php get_header(); ?>

<main class="site-main">
    <div class="container">
        <section class="not-found">
            <h1>Page not found</h1>
            <p>The page you are looking for does not exist.</p>
            <a class="button" href="<?php echo esc_url(home_url('/')); ?>">Return home</a>
        </section>
    </div>
</main>

<?php get_footer(); ?>