<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include_once( 'functions.php' );					// all PHP functions 


add_action( 'pf_silver_hook', 'build_xml' );			


/* INSTALL */
/* register settings */
function pf_silver_register_settings(){
	add_option('pf_silver_activate', 'off');	// ON or OFF - on/off switch
	add_option('pf_silver_cron', 'off');		// HOURLY OR DAILY OR TWICEDAILY - how often to update?
	add_option('pf_silver_create_xml', 'no');	// NO, FAIL OR SUCCESS - is the product feed file created? 
	add_option('pf_silver_count_products', 0);	// 0-500 - how many products have we found? 
	add_option('pf_silver_currency', 'EUR');	// EUR | USD | GBP - which currency to use? -> ISO 4217
	add_option('pf_silver_sale_prices', 'off');	// ON or OFF - DISPLAY SALE PRICES & DATE RANGES IN FEED? -> ISO 4217
	add_option('pf_silver_gtin', 'off');		// ON or OFF - DISPLAY gtin IN FEED?
	add_option('pf_silver_condition', 'new');	// new | used | refurbished - WHAT KIND OF PRODUCTS ARE YOU SELLING?
	add_option('pf_silver_google_product_category_id', 0);	// new | used | refurbished - WHAT KIND OF PRODUCTS ARE YOU SELLING?
	add_option('pf_silver_shipping_country', '');			// TO WHICH COUNTRY DO YOU SHIP?
	add_option('pf_silver_shipping_price', '');				// HOW MUCH DOES SHIPPING COST?
	add_option('pf_silver_shipping_name', 'standard');		// TEXT - name for shipping method
	add_option('pf_silver_enable_shipping', 'off');			// ON or OFF - toggles adding shipping to XML-feed
	
	}


/* UNINSTALL */
/* unregister settings */
/* THE CLEANUP CREW! :) */
function pf_silver_unregister_settings(){
	delete_option( 'pf_silver_activate' );
	delete_option( 'pf_silver_cron' );
	delete_option( 'pf_silver_create_xml' );
	delete_option( 'pf_silver_count_products' );
	delete_option( 'pf_silver_currency' );
	delete_option( 'pf_silver_sale_prices' );
	delete_option( 'pf_silver_gtin' );	
	delete_option( 'pf_silver_condition' );
	delete_option( 'pf_silver_google_product_category_id' );	
	delete_option( 'pf_silver_shipping_country' );
	delete_option( 'pf_silver_shipping_price' );
	delete_option( 'pf_silver_shipping_name' );
	delete_option( 'pf_silver_enable_shipping' );
	
	
	// REMOVE SCHEDULED CRON TASKS	
	wp_clear_scheduled_hook( 'pf_silver_hook' );
	}
	


?>