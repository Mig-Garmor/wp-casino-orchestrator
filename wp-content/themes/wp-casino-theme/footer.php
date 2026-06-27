<footer class="site-footer">
    <div class="container">
        <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>.</p>

        <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Footer navigation', 'wp-casino-theme'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container' => false,
                'fallback_cb' => false,
            ]);
            ?>
        </nav>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>