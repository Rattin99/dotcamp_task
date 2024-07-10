<?php

namespace Rattin\Task\Frontend;

class Signup {
    public function __construct() {
        add_action('init', [$this, 'add_custom_rewrite_rules']);
        add_action('template_redirect', [$this, 'handle_custom_endpoints']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_tuts_sign_up', [$this, 'process_signup_form']);
        add_action('wp_ajax_nopriv_tuts_sign_up', [$this, 'process_signup_form']);
    }

    public function add_custom_rewrite_rules() {
        add_rewrite_rule('^sign-up/?$', 'index.php?sign_up=1', 'top');
        add_rewrite_tag('%sign_up%', '([^&]+)');
    }

    public function handle_custom_endpoints() {
        if (get_query_var('sign_up')) {
            $this->render_sign_up_form();
            exit;
        }
    }

    public function enqueue_scripts() {
        if (get_query_var('sign_up')) {
            wp_enqueue_style('task-signup-style', TASK_ASSETS . 'css/signup.css', [], TASK_VERSION);
            wp_enqueue_script('task-signup-script', TASK_ASSETS . 'js/signup.js', ['jquery'], TASK_VERSION, true);
            wp_localize_script('task-signup-script', 'taskSignup', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('task_signup_nonce')
            ]);
        }
    }

    public function render_sign_up_form() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php _e('Sign Up', 'task-plugin'); ?></title>
            <?php wp_head(); ?>
        </head>
        <body <?php body_class(); ?>>
            <div id="sign-up-form">
                <h1><?php _e('Sign Up', 'task-plugin'); ?></h1>
                <form id="tuts-sign-up" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('tuts_sign_up_action', 'tuts_sign_up_nonce'); ?>
                    <input type="hidden" name="action" value="tuts_sign_up">
                    
                    <label for="name"><?php _e('Name', 'task-plugin'); ?></label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="address"><?php _e('Address', 'task-plugin'); ?></label>
                    <input type="text" id="address" name="address" required>
                    
                    <label for="phone"><?php _e('Phone Number', 'task-plugin'); ?></label>
                    <input type="tel" id="phone" name="phone" required>
                    
                    <label for="email"><?php _e('Email', 'task-plugin'); ?></label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="hobbies"><?php _e('Hobbies', 'task-plugin'); ?></label>
                    <input type="text" id="hobbies" name="hobbies" required>
                    
                    <button type="submit"><?php _e('Sign Up', 'task-plugin'); ?></button>
                </form>
                <div id="form-messages"></div>
            </div>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
    }

    public function process_signup_form() {
        try {
            error_log('process_signup_form method called');
    
            // Verify nonce
            if (!isset($_POST['tuts_sign_up_nonce']) || !wp_verify_nonce($_POST['tuts_sign_up_nonce'], 'tuts_sign_up_action')) {
                throw new \Exception('Security check failed');
            }
    
            error_log('POST data: ' . print_r($_POST, true));
    
            // Sanitize and validate inputs
            $name = sanitize_text_field($_POST['name'] ?? '');
            $address = sanitize_text_field($_POST['address'] ?? '');
            $phone = sanitize_text_field($_POST['phone'] ?? '');
            $email = sanitize_email($_POST['email'] ?? '');
            $hobbies = sanitize_text_field($_POST['hobbies'] ?? '');
    
            $errors = [];
    
            if (empty($name)) $errors[] = 'Name is required.';
            if (empty($address)) $errors[] = 'Address is required.';
            if (empty($phone) || !preg_match('/^\+?[0-9\s\-().]{7,20}$/', $phone)) $errors[] = 'Valid phone number is required.';
            if (empty($email) || !is_email($email)) $errors[] = 'Valid email is required.';
            if (empty($hobbies)) $errors[] = 'Hobbies are required.';
    
            if (!empty($errors)) {
                error_log('Validation errors: ' . print_r($errors, true));
                wp_send_json_error(['errors' => $errors]);
            } else {
                // Save user data to the database
                $user_data = [
                    'name' => $name,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                    'hobbies' => $hobbies,
                    'signup_date' => current_time('mysql')
                ];
    
                $inserted = $this->save_user_data($user_data);
    
                if (!$inserted) {
                    throw new \Exception('Failed to save user data');
                }
    
                error_log('User data saved successfully');
                wp_send_json_success(['message' => 'Sign up successful!']);
            }
        } catch (\Exception $e) {
            error_log('Sign-up form error: ' . $e->getMessage());
            wp_send_json_error(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    private function save_user_data($user_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_signups';

        return $wpdb->insert($table_name, $user_data);
    }
}