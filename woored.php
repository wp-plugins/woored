<?php
/*
Plugin Name: WooRed
Description: A&ntilde;ade la posibilidad de cobrar a trav&eacute;s de tarjeta de cr&eacute;dito usando la pasarela de pago de Servired, junto con el plugin &quot; WooCommerce&quot; como plataforma de comercio, Servired / Sermepa / Reds&yacute;s / Cyberpack.
Version: 1.0
Author: Amir JM
Author URI: http://web2webs.com/
Text Domain: woocommerce-servired
Stable tag: 1.0
Plugin URI: http://web2webs.com/woored-pasarela-plugin-pago-gratis-wordpress-woocommerce/
*/

/*  
Copyright 2015  Amir JM

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function init_woored_gateway() 
{
    if (class_exists('WC_Payment_Gateway'))
    {	
        include_once('class.woored.php');
    }
}

function woored_action_link($links, $file) 
{
	static $this_plugin;
	if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
	if( $file == $this_plugin ){
		$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=wc_servired">Ajustes</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

function woored_get_version() 
{
	$plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

add_action('plugins_loaded', 'init_woored_gateway', 0);
add_filter('plugin_action_links', 'woored_action_link', 10, 2);