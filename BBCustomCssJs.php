<?php

/*
 * Plugin Name: BBCustomCssJs
 * Author: Gimcholas@BB
 * Description: Injects CSS and JS into every page
 */

function getCustomCodeDir()
{
    $upload_dir = wp_upload_dir();
    $customCodeDir = $upload_dir['basedir'] . '/' . get_plugin_data(__FILE__)['Name'];
    return $customCodeDir;
}

function getCustomCodeUrl()
{
    $upload_dir = wp_upload_dir();
    $customCodeDir = $upload_dir['baseurl'] . '/' . get_plugin_data(__FILE__)['Name'];
    return $customCodeDir;
}

function BBCustomCssJs_activate()
{
    wp_mkdir_p(getCustomCodeDir());
}

function InjectStyle($url)
{
    return '<link rel="stylesheet" href="' . $url . '">';
}

function InjectScript($url)
{
    return '<script src="' . $url . '"></script>';
}

function InjectHtmlFile($filePath)
{
    if (!file_exists($filePath)) {
        return '';
    }

    return file_get_contents($filePath);
}

function InjectCustomCssCode()
{
    if (is_admin()) {
        return;
    }

    $customCodeUrl = getCustomCodeUrl();

    foreach (glob(getCustomCodeDir() . '/*.css') as $filename) {
        echo InjectStyle($customCodeUrl . '/' . basename($filename));
    }
}

function InjectCustomJsCode()
{
    if (is_admin()) {
        return;
    }

    $customCodeUrl = getCustomCodeUrl();

    foreach (glob(getCustomCodeDir() . '/*.js') as $filename) {
        echo InjectScript($customCodeUrl . '/' . basename($filename));
    }
}

function InjectCustomHtmlCode()
{
    if (is_admin()) {
        return;
    }

    foreach (glob(getCustomCodeDir() . '/*.html') as $filename) {
        echo InjectHtmlFile($filename);
    }
}

register_activation_hook(__FILE__, 'BBCustomCssJs_activate');
add_action('wp_head', 'InjectCustomHtmlCode', 998);
add_action('wp_head', 'InjectCustomCssCode', 999);
add_action('wp_print_footer_scripts', 'InjectCustomJsCode');
