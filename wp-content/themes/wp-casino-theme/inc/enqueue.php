<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    $css_path = WP_CASINO_THEME_DIR . '/assets/css/main.css';
    $js_path = WP_CASINO_THEME_DIR . '/assets/js/main.js';

    wp_enqueue_style(
        'wp-casino-theme-main',
        WP_CASINO_THEME_URI . '/assets/css/main.css',
        [],
        file_exists($css_path) ? filemtime($css_path) : WP_CASINO_THEME_VERSION
    );

    wp_enqueue_script(
        'wp-casino-theme-main',
        WP_CASINO_THEME_URI . '/assets/js/main.js',
        [],
        file_exists($js_path) ? filemtime($js_path) : WP_CASINO_THEME_VERSION,
        true
    );
});