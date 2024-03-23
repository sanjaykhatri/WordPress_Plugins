<?php
/*
Plugin Name: Social Media Publisher
Description: A plugin to publish and schedule posts on Twitter.
Version: 1.0
Author: Sanjay Khatri
License: GPL2
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
// Include TwitterOAuth library
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php'; // Include TwitterOAuth library
// Include the main class file.
require_once plugin_dir_path(__FILE__) . 'includes/social-media-publisher.php';

// Initialize the plugin.
function social_media_publisher_init() {
    new Social_Media_Publisher();
}
add_action('plugins_loaded', 'social_media_publisher_init');
