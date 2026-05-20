<?php

/*
 * Plugin Name: BBCustomCssJs
 * Author: Gimcholas@BB
 * Description: Injects CSS and JS into every page
 */

defined('ABSPATH') || exit;

define('BBCUSTOMCSSJS_DIRNAME', 'bbcustomcssjs');

function bbcustomcssjs_get_custom_code_dir()
{
    $upload_dir = wp_upload_dir();

    return trailingslashit($upload_dir['basedir']) . BBCUSTOMCSSJS_DIRNAME;
}

function bbcustomcssjs_get_custom_code_url()
{
    $upload_dir = wp_upload_dir();

    return trailingslashit($upload_dir['baseurl']) . BBCUSTOMCSSJS_DIRNAME;
}

function bbcustomcssjs_activate()
{
    wp_mkdir_p(bbcustomcssjs_get_custom_code_dir());

    // Optional: prevent directory listing.
    $index_file = trailingslashit(bbcustomcssjs_get_custom_code_dir()) . 'index.php';

    if (!file_exists($index_file)) {
        file_put_contents($index_file, "<?php\n// Silence is golden.\n");
    }
}

function bbcustomcssjs_inject_custom_css_code()
{
    if (is_admin()) {
        return;
    }

    $custom_code_url = bbcustomcssjs_get_custom_code_url();
    $custom_code_dir = bbcustomcssjs_get_custom_code_dir();

    foreach (glob($custom_code_dir . '/*.css') as $filename) {
        $url = trailingslashit($custom_code_url) . rawurlencode(basename($filename));
        echo '<link rel="stylesheet" href="' . esc_url($url) . '">' . "\n";
    }
}

function bbcustomcssjs_inject_custom_js_code()
{
    if (is_admin()) {
        return;
    }

    $custom_code_url = bbcustomcssjs_get_custom_code_url();
    $custom_code_dir = bbcustomcssjs_get_custom_code_dir();

    foreach (glob($custom_code_dir . '/*.js') as $filename) {
        $url = trailingslashit($custom_code_url) . rawurlencode(basename($filename));
        echo '<script src="' . esc_url($url) . '"></script>' . "\n";
    }
}

function bbcustomcssjs_inject_custom_html_code()
{
    if (is_admin()) {
        return;
    }

    $custom_code_dir = bbcustomcssjs_get_custom_code_dir();

    foreach (glob($custom_code_dir . '/*.html') as $filename) {
        if (!is_readable($filename)) {
            continue;
        }

        // WARNING: This intentionally allows raw trusted HTML.
        echo file_get_contents($filename);  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

register_activation_hook(__FILE__, 'bbcustomcssjs_activate');

add_action('wp_head', 'bbcustomcssjs_inject_custom_html_code', 998);
add_action('wp_head', 'bbcustomcssjs_inject_custom_css_code', 999);
add_action('wp_print_footer_scripts', 'bbcustomcssjs_inject_custom_js_code');
