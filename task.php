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
}

function task_plugin_init() {
    return Task::init();
}

task_plugin_init();