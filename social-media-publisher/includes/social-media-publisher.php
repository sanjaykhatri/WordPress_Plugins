<?php
class Social_Media_Publisher {
    public function __construct() {
        // Load necessary files.
        require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
        require_once plugin_dir_path(__FILE__) . 'admin/notices.php';
        require_once plugin_dir_path(__FILE__) . 'api/social-media-api.php';

        // Initialize admin settings.
        new Social_Media_Publisher_Settings();
    }
}
