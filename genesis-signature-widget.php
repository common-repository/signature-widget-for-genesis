<?php
/*
Plugin Name: Signature Widget for Genesis
Plugin URI: http://cre8tivediva.com
Description: Signature Widget for Genesis give bloggers a way to add a nice customized signature to the bottom of their blog posts.
Version: 1.3.0
Author: Anita Carter (Cre8tive Diva)
Author URI: http://cre8tivediva.com
Text Domain: genesis-signature-widget
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/* Prevent direct access to the plugin */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Sorry, you are not allowed to access this page directly.' );
}


register_activation_hook( __FILE__, 'gsw_activation_check' );
/**
 * Activation hook callback.
 *
 * This functions runs when the plugin is activated. It checks to make sure the user is running
 * a minimum Genesis version, so there are no conflicts or fatal errors.
 *
 * @since 1.0.0
 */
function gsw_activation_check() {

	$genesis_min_version = '2.0.0';
	$wp_min_version      = '4.2.4';

	if ( ! defined( 'PARENT_THEME_VERSION' ) || ! version_compare( PARENT_THEME_VERSION, $genesis_min_version, '>=' ) ) {
		gsw_deactivate( $genesis_min_version, $wp_min_version );
	}
}

/**
 * Deactivate Signature Widget for Genesis.
 *
 * @since 1.0.0
 */
function gsw_deactivate( $genesis_version = '2.0.0', $wp_version = '4.2.4' ) {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	wp_die( sprintf( __( 'Sorry, you cannot run the Signature Widget for Genesis without WordPress %s and <a href="%s">Genesis %s</a>, or greater.', 'genesis-signature-widget' ), $wp_version, 'http://my.studiopress.com', $genesis_version ) );
	
}


/**
* Register and enqueues the signature style sheet.
*
* @since 1.0.0
*/
function gsw_signature_widget_css() {

	if ( ! is_singular( 'post' ) ) {
	    return;
	}
	wp_register_style( 'genesis-signature-widget', plugin_dir_url( __FILE__ ) . 'gsw-signature.css', false, '1.0' );
	$gsw_signature_widget = array(
		'genesis_signature_widget'       => is_active_sidebar( 'genesis-signature-widget' ),
	);

	// Return early if no sidebars are active
	if ( ! in_array( true, $gsw_signature_widget ) ) {
		return;
	}
	wp_enqueue_style( 'genesis-signature-widget' );
	gsw_do_widget_areas( $gsw_signature_widget );
}

/**
* Output Signature Widget for Genesis. If no widget active, display nothing.
*
* @since 1.1.0
*/
function gsw_do_widget_areas($content) {
    if ( is_singular( 'post' ) ) {
        $content .= "<div class='genesis-signature-widget'>";
        ob_start();
        dynamic_sidebar( 'genesis-signature-widget' );
        $content .= ob_get_clean();
        $content .= "</div>";
    }
    return $content;
}
add_filter ('the_content', 'gsw_do_widget_areas', 8 );

/**
 * @since 1.0.0
*/

add_action( 'genesis_setup', 'gsw_signature_widget' );
function gsw_signature_widget () {
    genesis_register_sidebar( array(
		'id' => 'genesis-signature-widget',
		'name' => __( 'Signature Widget for Genesis', 'genesis-signature-widget' ),
		'description' => __( 'This is the signature widget that shows up on posts .', 'genesis-signature-widget' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'gsw_signature_widget_css' );
