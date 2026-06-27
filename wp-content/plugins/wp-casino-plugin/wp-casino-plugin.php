<?php

/**
 * Plugin Name: WP Casino Plugin
 * Description: Custom plugin for casino affiliate functionality.
 * Version: 1.0.0
 * Author: Miguel Garcia
 */

if (!defined('ABSPATH')) {
    exit;
}

function wp_casino_register_casino_post_type(): void
{
    register_post_type('casino', [
        'labels' => [
            'name' => 'Casinos',
            'singular_name' => 'Casino',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => [
            'slug' => 'casinos',
        ],
        'supports' => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
        ],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-tickets-alt',
    ]);
}

add_action('init', 'wp_casino_register_casino_post_type');