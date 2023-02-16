<?php 
/**
 * Basic setup functions for the plugin
 *
 * @since 1.0.0
 * @function	dvt_activate_plugin()		Plugin activatation todo list
 * @function	dvt_load_plugin_textdomain()	Load plugin text domain
 * @function	dvt_settings_link()			Print direct link to plugin settings in plugins list in admin
 * @function	dvt_plugin_row_meta()		Add donate and other links to plugins list
 * @function	dvt_footer_text()			Admin footer text
 * @function	dvt_footer_version()			Admin footer version
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Plugin activatation todo list
 *
 * This function runs when user activates the plugin. Used in register_activation_hook in the main plugin file. 
 * @since 1.0.0
 */
function dvt_activate_plugin() {
	
}

/**
 * Load plugin text domain
 *
 * @since 1.0.0
 */
function dvt_load_plugin_textdomain() {
    load_plugin_textdomain( 'dev-tasks-up', false, '/dev-tasks-up/languages/' );
}
add_action( 'plugins_loaded', 'dvt_load_plugin_textdomain' );

/**
 * Print direct link to plugin settings in plugins list in admin
 *
 * @since 1.0.0
 */
function dvt_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=dev-tasks-settings' ) . '">' . esc_html(__( 'Settings', 'dev-tasks-up' )) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . DVT_STARTER_PLUGIN . '/dev-tasks-up.php', 'dvt_settings_link' );

/**
 * Add donate and other links to plugins list
 *
 * @since 1.0.0
 */
function dvt_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'dev-tasks-up.php' ) !== false ) {
		$new_links = array(
				'donate' 	=> '<a href="https://revolut.me/mvalchev" target="_blank">Donate</a>',
				'hireme' 	=> '<a href="https://martinvalchev.com/#contact" target="_blank">Hire Me For A Project</a>',
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'dvt_plugin_row_meta', 10, 2 );

/**
 * Admin footer text
 *
 * A function to add footer text to the settings page of the plugin. Footer text contains plugin rating and donation links.
 * Note: Remove the rating link if the plugin doesn't have a WordPress.org directory listing yet. (i.e. before initial approval)
 *
 * @since 1.0.0
 * @refer https://codex.wordpress.org/Function_Reference/get_current_screen
 */
function dvt_footer_text($default) {

	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== 'devtasksup_page_dev-tasks-settings' && $screen->id !== 'toplevel_page_dev-tasks-admin-page' ) {
		return $default;
	}

    $dvt_footer_text = sprintf( __( 'If you like this plugin, please <a href="%s" target="_blank">make a donation</a> or leave me a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating to support continued development. Thanks a bunch!', 'dev-tasks-up' ),
        esc_url('https://revolut.me/mvalchev'),
        esc_url('https://wordpress.org/support/plugin/dev-tasks-up/reviews/?rate=5#new-post')
						);

	return $dvt_footer_text;
}
add_filter('admin_footer_text', 'dvt_footer_text');

/**
 * Admin footer version
 *
 * @since 1.0.0
 */
function dvt_footer_version($default) {

	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== 'devtasksup_page_dev-tasks-settings' && $screen->id !== 'toplevel_page_dev-tasks-admin-page' ) {
		return $default;
	}
	
	return 'DevtasksUp version ' . esc_html(DVT_VERSION_NUM);
}
add_filter( 'update_footer', 'dvt_footer_version', 11 );