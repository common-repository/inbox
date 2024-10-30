<?php
/*
	Plugin Name: Inbox
	Plugin URI: https://androidbubble.com/blog/wordpress/plugins/inbox
	Description: All types of messages among users and admin including support departments are possible with this plugin.
	Version: 1.2.1
	Author: Fahad Mahmood
	Author URI: http://www.androidbubble.com/blog
	Text Domain: inbox
	Domain Path: /languages/	
	License: GPL2
	
	
	This WordPress plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This WordPress plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License	along with this WordPress plugin. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	

	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	
	global $wp_inbox_data, $wp_inbox_pro, $wp_inbox_pages, $wp_inbox_premium_link, $wp_inbox_dir, $wp_inbox_plugins_activated, $wp_inbox_all_plugins, $wp_inbox_woo_activated, $wp_inbox_required_plugins, $is_chat_ajax_based, $wp_inbox_url, $wp_inbox_timezone;
	
	$wp_inbox_premium_link = 'https://shop.androidbubbles.com/product/inbox/';//https://shop.androidbubble.com/products/wordpress-plugin?variant=36439507894427';//
	
	$wp_inbox_required_plugins = array(
		'wordpress-bootstrap-css'=>'wordpress-bootstrap-css/hlt-bootstrapcss.php',
		//'better-font-awesome'=>'better-font-awesome/better-font-awesome.php',
	);

    $wp_inbox_live_chat_settings = get_option('wp_inbox_live_chat_settings', array());
    $is_chat_ajax_based =  (array_key_exists('wp_inbox_ajax_based_chat', $wp_inbox_live_chat_settings) && $wp_inbox_live_chat_settings['wp_inbox_ajax_based_chat'] == 'on');
	
		
	$wp_inbox_woo_activated = false;		
	$wp_inbox_all_plugins = get_plugins();
	$wp_inbox_plugins_activated = apply_filters( 'active_plugins', get_option( 'active_plugins' ));
	
	if(is_multisite()){			
		
		$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
		
		$wp_inbox_plugins_activated = array_keys($active_sitewide_plugins);		
		
	}	
	
	if(array_key_exists('woocommerce/woocommerce.php', $wp_inbox_all_plugins) && in_array('woocommerce/woocommerce.php', $wp_inbox_plugins_activated)){
		$wp_inbox_woo_activated = true;
	}	
	
	
	
	
	$wp_inbox_data = get_plugin_data(__FILE__);
	
	$wp_inbox_url = plugin_dir_url( __FILE__ );
	$wp_inbox_dir = plugin_dir_path( __FILE__ );
	
	$wp_inbox_pro_file = $wp_inbox_dir . 'pro/inbox-pro.php';
	
	
	$wp_inbox_pro =  file_exists($wp_inbox_pro_file);
	
	
	
	if($wp_inbox_pro)
	include_once($wp_inbox_pro_file);
	
	include_once $wp_inbox_dir . 'inc/functions.php';
	
	


	if(is_admin()){
		add_action( 'admin_menu', 'wp_inbox_menu' );	
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'wp_inbox_plugin_links' );	
		add_action( 'admin_enqueue_scripts', 'wp_inbox_admin_scripts', 99 );			
	}else{		
		add_action( 'wp_enqueue_scripts', 'wp_inbox_front_scripts', 99 );	
		
	}