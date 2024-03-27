<?php
class Social_Media_Publisher_Settings
{
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_settings'));
    }

    // Add settings page.
    public function add_settings_page()
    {
        add_menu_page('Social Media Publisher', 'Social Media', 'manage_options', 'social-media-publisher', array($this, 'settings_page_content'));

        // Add submenu
            add_submenu_page(
                'social-media-publisher',
                __('Post to Twitter', 'social-media-publisher'),
                __('Post to Twitter', 'social-media-publisher'),
                'manage_options',
                'post-to-twitter',
                 array(new Social_Media_Publisher_API, 'twitter_post_page_callback'),
            );
    }

    // Settings page content.
    public function settings_page_content()
    {
        // Ensure user is authorized to manage options.
        if (!current_user_can('manage_options')) {
            return;
        }

        // Display settings errors.
        settings_errors();

        // Get plugin options.
        $this->options = get_option('social_media_publisher_settings');

?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                // Add settings fields.
                settings_fields('social_media_publisher_settings_group');
                do_settings_sections('social-media-publisher');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
<?php
    }

    // Initialize settings.
    public function init_settings()
    {
        // Register settings.
        register_setting('social_media_publisher_settings_group', 'social_media_publisher_settings', array($this, 'validate_settings'));

        // Add settings section and fields.
        add_settings_section('twitter_settings_section', 'Twitter Configuration', array($this, 'twitter_settings_section_callback'), 'social-media-publisher');

        add_settings_field('twitter_api_key', 'Twitter API Key', array($this, 'twitter_api_key_callback'), 'social-media-publisher', 'twitter_settings_section');
        add_settings_field('twitter_api_secret', 'Twitter API Secret', array($this, 'twitter_api_secret_callback'), 'social-media-publisher', 'twitter_settings_section');
        add_settings_field('access_key', 'Access Token', array($this, 'access_key_callback'), 'social-media-publisher', 'twitter_settings_section');
        add_settings_field('secret_token', 'Access Token Secret', array($this, 'secret_token_callback'), 'social-media-publisher', 'twitter_settings_section');
    }

    // Validate settings.
    public function validate_settings($input)
    {
        $validated_input = array();

        // Validate Twitter API Key.
        if (!empty($input['twitter_api_key'])) {
            // Check if the format is correct (dummy check)
            if (preg_match('/^[a-zA-Z0-9]+$/', $input['twitter_api_key'])) {
                $validated_input['twitter_api_key'] = sanitize_text_field($input['twitter_api_key']);
            } else {
                add_settings_error('twitter_api_key', 'invalid_twitter_api_key', 'Invalid Twitter API Key format.', 'error');
            }
        } else {
            add_settings_error('twitter_api_key', 'empty_twitter_api_key', 'Twitter API Key is required.', 'error');
        }

        // Validate Twitter API Secret.
        if (!empty($input['twitter_api_secret'])) {
            // Check if the format is correct (dummy check)
            if (preg_match('/^[a-zA-Z0-9]+$/', $input['twitter_api_secret'])) {
                $validated_input['twitter_api_secret'] = sanitize_text_field($input['twitter_api_secret']);
            } else {
                add_settings_error('twitter_api_secret', 'invalid_twitter_api_secret', 'Invalid Twitter API Secret format.', 'error');
            }
        } else {
            add_settings_error('twitter_api_secret', 'empty_twitter_api_secret', 'Twitter API Secret is required.', 'error');
        }

        // Validate Access Key (optional).
        if (!empty($input['access_key'])) {
            // Check if the format is correct (dummy check)
            if (preg_match('/^[a-zA-Z0-9_\-]+$/', $input['access_key'])) {
                $validated_input['access_key'] = sanitize_text_field($input['access_key']);
            } else {
                add_settings_error('access_key', 'invalid_access_key', 'Invalid Access Key format.', 'error');
            }
        } else {
            add_settings_error('access_key', 'empty_access_key', 'Access key is required.', 'error');
        }

        // Validate Secret Token (optional).
        if (!empty($input['secret_token'])) {
            // Check if the format is correct (dummy check)
            if (preg_match('/^[a-zA-Z0-9]+$/', $input['secret_token'])) {
                $validated_input['secret_token'] = sanitize_text_field($input['secret_token']);
            } else {
                add_settings_error('secret_token', 'invalid_secret_token', 'Invalid Secret Token format.', 'error');
            }
        } else {
            add_settings_error('secret_token', 'empty_secret_token', 'Secret Token is required.', 'error');
        }

        return $validated_input;
    }


    // Twitter settings section callback.
    public function twitter_settings_section_callback()
    {
        echo '<p>Enter your Twitter API credentials and other options below:</p>';
    }

    // Twitter API Key callback.
    public function twitter_api_key_callback()
    {
        printf(
            '<input type="text" id="twitter_api_key" name="social_media_publisher_settings[twitter_api_key]" value="%s" />',
            isset($this->options['twitter_api_key']) ? esc_attr($this->options['twitter_api_key']) : ''
        );
    }

    // Twitter API Secret callback.
    public function twitter_api_secret_callback()
    {
        printf(
            '<input type="text" id="twitter_api_secret" name="social_media_publisher_settings[twitter_api_secret]" value="%s" />',
            isset($this->options['twitter_api_secret']) ? esc_attr($this->options['twitter_api_secret']) : ''
        );
    }

    // Access Key callback.
    public function access_key_callback()
    {
        printf(
            '<input type="text" id="access_key" name="social_media_publisher_settings[access_key]" value="%s" />',
            isset($this->options['access_key']) ? esc_attr($this->options['access_key']) : ''
        );
    }

    // Secret Token callback.
    public function secret_token_callback()
    {
        printf(
            '<input type="text" id="secret_token" name="social_media_publisher_settings[secret_token]" value="%s" />',
            isset($this->options['secret_token']) ? esc_attr($this->options['secret_token']) : ''
        );
    }
}
