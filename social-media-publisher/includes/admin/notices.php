<?php
class Social_Media_Publisher_Notices
{
    public function __construct()
    {
        // Initialize session if not already started.
        if (!session_id()) {
            session_start();
        }
        add_action('admin_notices', array($this, 'display_notices'));
    }

    // Display notices.
    public static function display_notices()
    {
        // Check for any notices stored in the session.
        $notices = isset($_SESSION['social_media_publisher_notices']) ? $_SESSION['social_media_publisher_notices'] : array();

        // Display each notice.
        foreach ($notices as $notice) {
            $class = isset($notice['type']) ? $notice['type'] : 'info';
            printf('<div class="notice notice-%s"><p>%s</p></div>', esc_attr($class), esc_html($notice['message']));
        }

        // Clear stored notices after displaying them.
        $_SESSION['social_media_publisher_notices'] = array();
    }

    // Add a notice.
    public static function add_notice($message, $type = 'info')
    {
        // Store the notice in the session.
        $_SESSION['social_media_publisher_notices'][] = array(
            'message' => $message,
            'type' => $type,
        );
    }
}

// Initialize notices.
new Social_Media_Publisher_Notices();
