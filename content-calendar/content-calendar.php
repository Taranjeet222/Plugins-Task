<?php
/**
 * Plugin Name: Content Calendar
 * Description: Plugin to display content calendar.
 * Version: 1.0.0
 * Author: Taranjeet
 * Author URI: http://example.com/
 * Plugin URI: https://example.com/
 */

function my_plugin_activation_hook() {
    db_myplugin();
}
function db_myplugin(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'content_calendar';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            date DATE NOT NULL,
            occasion VARCHAR(255) NOT NULL,
            post_title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            reviewer VARCHAR(255) NOT NULL
        )";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

register_activation_hook( __FILE__, 'my_plugin_activation_hook' );

function my_plugin_deactivation_hook() {
    // we don't want to delete the data.
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'content_calendar';
    // $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_deactivation_hook(__FILE__, 'my_plugin_deactivation_hook');

add_action('admin_print_styles', 'add_my_stylesheet');
function add_my_stylesheet() 
{
    wp_enqueue_style(
        'content-calendar-style',
        plugins_url('content-calendar-style.css', __FILE__)
    );
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
}

add_action('admin_enqueue_scripts','add_my_script');

function add_my_script() {
    wp_register_script(
        'content-calendar-script' ,
        plugins_url('content-calendar-script.js', __FILE__),
        array('jquery')
    );
    wp_enqueue_script(
        'content-calendar-script'
    );
}

add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
function enqueue_admin_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('wp-jquery-ui-dialog');
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-calendar.php';

$my_object = new Add_Menu();
