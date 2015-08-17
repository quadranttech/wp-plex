<?php
// Added by WarmStal
if(!is_admin())
return;

require_once (dirname(__FILE__).'/wp-plex-options.php');

/**
 * Wrapper for the option 'duplicate_post_version'
 */
function plex_config_get_installed_version() {
	return get_option( 'plex_config_version' );
}

/**
 * Wrapper for the defined constant DUPLICATE_POST_CURRENT_VERSION
 */
function plex_config_get_current_version() {
	return PLEX_CONFIG_CURRENT_VERSION;
}

/**
 * Plugin upgrade
 */
// add_action('admin_init','duplicate_post_plugin_upgrade');



//Add some links on the plugin page
add_filter('plugin_row_meta', 'plex_config_add_plugin_links', 10, 2);

function plex_config_add_plugin_links($links, $file) {
	if ( $file == plugin_basename(dirname(__FILE__).'/wp-plex.php') ) {
		$links[] = '<a href="#">' . __('Donate', PLEX_CONFIG_DOMAIN) . '</a>';
		$links[] = '<a href="#">' . __('Translate', PLEX_CONFIG_DOMAIN) . '</a>';
	}
	return $links;
}
?>