<?php
/**
 * Plugin Name: Post info mail
 * Description: Plugin to email details about a post.
 * Version: 1.0.0
 * Author: Taranjeet
 */

 function my_plugin_activation_hook() {
    if (!is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')) {
        
        deactivate_plugins(__FILE__);
        unset($_GET['activate']);
        add_action(
            'admin_notices' ,
            function(){
                echo '<div class="error"><p>'.esc_html__('Post info mail requires wp-mail-smtp plugin to be installed and activated.').'</p></div>';
            }
        );
    } 
}

add_action('admin_init','my_plugin_activation_hook');
register_activation_hook( __FILE__, 'my_plugin_activation_hook' );

function my_plugin_deactivation_hook() {
    wp_clear_scheduled_hook('mail_post_summary');
}
register_deactivation_hook( __FILE__, 'my_plugin_deactivation_hook' );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-pinfo-plugin.php';


$my_object = new Pinfo_Plugin();

