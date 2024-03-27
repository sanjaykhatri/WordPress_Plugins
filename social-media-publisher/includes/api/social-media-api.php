<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class Social_Media_Publisher_API
{
    public function __construct()
    {
        add_action('admin_post_submit_post_to_twitter', array($this, 'submit_post_to_twitter'));
    }
    
    // twitter_post_page_callback
    public function twitter_post_page_callback()
    {
        // Ensure user is authorized to manage options.
        if (!current_user_can('manage_options')) {
            return;
        }

?>
        <div class="wrap">
            <h1>Post to Twitter</h1>
            <?php Social_Media_Publisher_Notices::display_notices(); ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('submit_post_to_twitter', 'submit_post_to_twitter_nonce'); ?>
                <label for="twitter_post_content"><?php esc_html_e('Tweet Content:', 'social-media-publisher'); ?></label><br>
                <?php
                    // Get existing tweet content if available
                     $tweet_content = isset($_POST['twitter_post_content']) ? $_POST['twitter_post_content'] : '';
                     // Set editor settings
                     $editor_settings = array(
                        'textarea_name' => 'twitter_post_content',
                        'textarea_rows' => 4,
                        'wpautop' => false
                    );
                    // Display classic editor
                    wp_editor($tweet_content, 'twitter_status_editor', $editor_settings);
                ?>
                <input type="hidden" name="action" value="submit_post_to_twitter">
                <input type="submit" name="submit_post_to_twitter" value="<?php esc_attr_e('Update Status', 'social-media-publisher'); ?>">
            </form>
        </div>
<?php
    }

    // Submit post to Twitter.
    public function submit_post_to_twitter()
    {
        // Check nonce for security.
        if (!isset($_POST['submit_post_to_twitter_nonce']) || !wp_verify_nonce($_POST['submit_post_to_twitter_nonce'], 'submit_post_to_twitter')) {
            Social_Media_Publisher_Notices::add_notice('Security check failed', 'error');
        }

        // Check user permissions.
        if (!current_user_can('manage_options')) {
            Social_Media_Publisher_Notices::add_notice('Unauthorized use', 'error');
        }

        // Get the content to post.
        $post_content = isset($_POST['twitter_post_content']) ? sanitize_textarea_field($_POST['twitter_post_content']) : '';

        // Validate content.
        if (empty($post_content)) {
            Social_Media_Publisher_Notices::add_notice('Tweet content is required', 'error');
        }

        // Call Twitter API to post the tweet
        $result = $this->post_to_twitter($post_content);

        // Handle API response.
        if ($result) {
            Social_Media_Publisher_Notices::add_notice('Tweet posted successfully!', 'updated');
        } else {
            // Add notice.
            Social_Media_Publisher_Notices::add_notice('Error posting tweet. Please try again later.', 'error');
        }
        $redirect_url = admin_url('admin.php?page=post-to-twitter');
        // Redirect back to the plugin settings page
        wp_redirect($redirect_url);
        exit();
    }


    // Twitter API integration to post content to Twitter.
    private function post_to_twitter($content)
    {
        // Fetch Twitter API credentials from WordPress options
        $settings = get_option('social_media_publisher_settings');
        $twitter_api_key = $settings['twitter_api_key'] ?? '';
        $twitter_api_secret = $settings['twitter_api_secret'] ?? '';
        $access_token = $settings['access_key'] ?? '';
        $access_token_secret = $settings['secret_token'] ?? '';

        // Check if any of the credentials are missing
        if (empty($twitter_api_key) || empty($twitter_api_secret) || empty($access_token) || empty($access_token_secret)) {
            Social_Media_Publisher_Notices::add_notice('Twitter API credentials are missing. Please check your settings.', 'error');
        }

        // Create TwitterOAuth object
        $connection = new TwitterOAuth($twitter_api_key, $twitter_api_secret, $access_token, $access_token_secret);

        // Make API request to post a tweet
        $tweet = $connection->post('statuses/update', ['status' => $content]);

        // Check if tweet is posted successfully
        if ($connection->getLastHttpCode() == 200) {
            // Handle success posting tweet
            Social_Media_Publisher_Notices::add_notice('Your Tweet has been posted.', 'update');
            return true; // Tweet posted successfully
        } else {
            // Handle error posting tweet
            $error_message = 'Error posting tweet. Please try again later.';
            if ($tweet && isset($tweet->errors[0]->message)) {
                $error_message .= ' Twitter API Error: ' . $tweet->errors[0]->message;
            }
            Social_Media_Publisher_Notices::add_notice($error_message, 'error');
        }
    }
}

// Initialize social media API.
new Social_Media_Publisher_API();
