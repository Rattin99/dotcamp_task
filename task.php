<?php
/**
 * Plugin Name: Task Plugin
 * Description: A Plugin for handling tasks.
 * Plugin URI: https://github.com/Rattin99/dotcamp_task.git
 * Author: Rattin Sadman
 * Version: 1.0
 * License: GPL2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

final class Task {
    const VERSION = '1.0';

    private function __construct() {
        $this->define_constants();
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);

        register_block_type('task-plugin/signup-list', [
            'render_callback' => 'render_signup_list_block',
            'attributes' => [
                'selectedPerson' => [
                    'type' => 'number',
                    'default' => 0
                ]
            ]
        ]);

        add_action('enqueue_block_editor_assets', 'enqueue_block_assets');
    }

    public static function init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function define_constants() {
        define('TASK_VERSION', self::VERSION);
        define('TASK_FILE', __FILE__);
        define('TASK_PATH', plugin_dir_path(__FILE__));
        define('TASK_URL', plugin_dir_url(__FILE__));
        define('TASK_ASSETS', TASK_URL . 'assets/');
    }

    public function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_signups';
        $charset_collate = $wpdb->get_charset_collate();
    
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            address text NOT NULL,
            phone varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            hobbies text NOT NULL,
            signup_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    
        if(!function_exists('dbDelta')){
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        dbDelta($sql);


        $signup = new Rattin\Task\Frontend\Signup();
        $signup->add_custom_rewrite_rules();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function init_plugin() {
        new Rattin\Task\Frontend\Signup();
        new Rattin\Task\Admin\Admin();
    }

    function enqueue_block_assets() {
        $asset_file = include(plugin_dir_path(__FILE__) . 'build/signup-list.asset.php');
    
        wp_enqueue_script(
            'task-plugin-signup-list',
            plugins_url('build/signup-list.js', __FILE__),
            $asset_file['dependencies'],
            $asset_file['version']
        );
    }

    function render_signup_list_block($attributes) {
        $selected_person_id = $attributes['selectedPerson'];
        
        if (!$selected_person_id) {
            return '<p>' . __('Please select a person.', 'task-plugin') . '</p>';
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_signups';
        $person = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $selected_person_id));
    
        if (!$person) {
            return '<p>' . __('Person not found.', 'task-plugin') . '</p>';
        }
    
        $output = '<div class="contact-card">';
        $output .= '<h3>' . esc_html($person->name) . '</h3>';
        $output .= '<p><strong>' . __('Address:', 'task-plugin') . '</strong> ' . esc_html($person->address) . '</p>';
        $output .= '<p><strong>' . __('Phone:', 'task-plugin') . '</strong> ' . esc_html($person->phone) . '</p>';
        $output .= '<p><strong>' . __('Email:', 'task-plugin') . '</strong> ' . esc_html($person->email) . '</p>';
        $output .= '<p><strong>' . __('Hobbies:', 'task-plugin') . '</strong> ' . esc_html($person->hobbies) . '</p>';
        $output .= '</div>';
    
        return $output;
    }
    
    
}

function task_plugin_init() {
    return Task::init();
}

task_plugin_init();