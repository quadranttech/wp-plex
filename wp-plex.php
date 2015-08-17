<?php

/*
Plugin Name: Plex WP
Version: 0.1
Description: Plex configuration.
Author: Quadrant Informatics
Author URI: http://www.quadrant.technology
Plugin URI: http://www.quadrant.technology
Text Domain: wp-plex

*/

/*  
Copyright 2015  QuadrantTechnologies  (email : dev@quadrant.technology)
Released under GPL License.
*/
// i18n plugin domain
define('PLEX_CONFIG_DOMAIN', 'wp-plex');

// Version of the plugin
define('PLEX_CONFIG_CURRENT_VERSION', '0.1' );
/*------------------------------------------------------------------*/ 

/* Version check */
global $wp_version;	

$exit_msg='WP Digg This requires WordPress 2.5 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

if (version_compare($wp_version,"2.5","<"))
{
	exit ($exit_msg);
}


/*------------------------------------------------------------------*/ 

if ( is_admin() ){ // admin actions
        add_action('admin_menu', 'plex_config_menu');
        // add_action( 'admin_init', 'duplicate_post_register_settings');
        function replace_admin_menu_icons_css() {
        ?>
            <style>
                /* CSS code goes here */                
                #adminmenu #toplevel_page_plexconfig div.wp-menu-image {
				    background: url(../wp-content/plugins/wp-plex/px-small-logo.png) no-repeat 0 0;
				}
                
            </style>
        <?php
        }
        add_action( 'admin_head', 'replace_admin_menu_icons_css' );
    }

function plex_config_menu() {
	add_menu_page(
		__("Plex Config Options", PLEX_CONFIG_DOMAIN), 
		__("Plex WP", PLEX_CONFIG_DOMAIN), 
		'manage_options', 'plexconfig', 
		'plex_config_options'
	);
}

if (is_admin()){
	require_once (dirname(__FILE__).'/wp-plex-admin.php');
}


?>
