<?php


namespace Rattin\Task\Admin;


class Admin {
    public function __construct()
    {
        add_action('admin_menu',[$this,'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        add_action('wp_ajax_get_signups', [$this, 'get_signups']);
    }

    public function add_admin_menu(){
        add_menu_page(
            'Task Signups',
            'Task Signups',
            'manage_options',
            'task-signups',
            [$this, 'render_admin_page'],
            'dashicons-list-view',
            100
        );
    }

    public function render_admin_page() {
        echo '<div id="task-signups-app"></div>';
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_task-signups') {
            return;
        }

        wp_enqueue_script('task-admin-react', 
        TASK_URL . 'build/static/js/main.9b67c240.js', 
        ['wp-element'], 
        TASK_VERSION, 
        true);
        wp_enqueue_style('task-admin-style',
        TASK_URL . 'build/static/css/main.e6c13ad2.css', 
        [], 
        TASK_VERSION);

        wp_localize_script('task-admin-react', 'taskAdminData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('task_admin_nonce')
        ]);
    }

    public function get_signups() {
        check_ajax_referer('task_admin_nonce', 'nonce');

        if (!check_ajax_referer('task_admin_nonce', 'nonce', false)) {
            error_log('Nonce check failed');
            wp_send_json_error('Invalid nonce');
            return;
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_signups';
        $signups = $wpdb->get_results("SELECT * FROM $table_name ORDER BY signup_date DESC", ARRAY_A);
    
        wp_send_json_success(['signups' => $signups]);
    }
}

?>