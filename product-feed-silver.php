<?php
/* Plugin Name: Murphsy.com Product Feed Silver
Plugin URI: http://murphsy.com/product/product-feed-silver
Description: Want a FREE Product Feed containing your Woocommerce products? Want to use it for Google Shopping? Look no further! Murphsy.com Product Feed Silver is completely free, both for commercial and non-commercial use. Forever.  
Version: 1.0.2
Author: Murphsy.com
Author URI: http://murphsy.com/
License: Free to use! 
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$pf_silver_path = str_replace( '\\', '/', plugin_dir_path( __FILE__ ));
add_action('admin_menu', 'pf_silver_menu');
function pf_silver_menu() {
	add_menu_page( 'Product Feed Silver', 'Product Feed Silver', 'manage_options', 'pf_silver', 'pf_silver_menu_options', 'none', 59 );
	add_submenu_page( 'pf_silver', 'Feed structure', 'Feed structure', 'manage_options', 'pf_silver_1', 'pf_silver_structure_page' );
	add_submenu_page( 'pf_silver', 'Want more options?', 'Want more options?', 'manage_options', 'pf_silver_2', 'pf_silver_upgrade_page' );
}


register_activation_hook( __FILE__, 'pf_silver_register_settings' );
register_deactivation_hook( __FILE__, 'pf_silver_unregister_settings' );

function admin_script() {
    if( is_admin() )
		{
        wp_enqueue_script('admin_jquery', plugin_dir_url( __FILE__ ).'/js/admin_jquery.js', array('jquery'));
		}   
	}

add_action( 'admin_enqueue_scripts', 'admin_script' );
	
include_once( 'functions.php' );					// all PHP functions 
include_once( 'settings.php' );						// all WP settings 
include_once( 'settings_pages.php' );				// building WP admin pages



?>