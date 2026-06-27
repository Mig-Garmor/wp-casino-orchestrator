<?php

if (!defined('ABSPATH')) {
    exit;
}

define('WP_CASINO_THEME_VERSION', '1.0.0');
define('WP_CASINO_THEME_DIR', get_template_directory());
define('WP_CASINO_THEME_URI', get_template_directory_uri());

require_once WP_CASINO_THEME_DIR . '/inc/setup.php';
require_once WP_CASINO_THEME_DIR . '/inc/enqueue.php';