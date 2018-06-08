<?php
/*
 * Plugin Name: CARES Restricted Content Shortcodes
 * Description: Show or hide content based on the user's capabilities.
 * Version: 1.0.0
*/
/**
 * CARES Restricted Content Shortcodes
 *
 * @package   CARES Restricted Content Shortcodes
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2018 CommmunityCommons.org
 */

namespace CARES_RCS;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add the shortcode handlers.
add_action( 'init', __NAMESPACE__ . '\\register_shortcodes' );
function register_shortcodes() {
	add_shortcode( 'loggedin', __NAMESPACE__ . '\\member_check_shortcode' );
	add_shortcode( 'visitor', __NAMESPACE__ . '\\visitor_check_shortcode' );
	add_shortcode( 'access', __NAMESPACE__ . '\\access_check_shortcode' );
}

// Restricted-content shortcodes. These are useful especially when content is generated via shortcode, like Gravity Forms
// Two basic levels: [loggedin] requires user to be logged in, [visitor] only shows to non-logged-in visitors
// More advanced uses WordPress capabilities to show content to admins only, etc.
// From Justin Tadlock: http://justintadlock.com/archives/2009/05/09/using-shortcodes-to-show-members-only-content

/**
 * Show contained to logged in only. Use in page or post content.
 * Takes the form: [loggedin message=''] content... [/loggedin]
 * "Message" attribute is optional. Will fall back to default. Specify message='' for no message.
 *
 * @param $atts Array of arguments
 * @param $string Content contained within shortcode.
 *
 * @return string HTML to display.
 */
function member_check_shortcode( $atts, $content = null ) {

	$r = shortcode_atts( array(
		'message' => 'You must be <a href="/wp-login.php" title="Log in to this site">logged in</a> to view this content.'
	), $atts );

	if ( is_user_logged_in() && ! is_null( $content ) && ! is_feed() ) {
		return do_shortcode( $content );
	} else {
		return $r['message'];
	}
}

/**
 * Show contained to visitors only. Use in page or post content.
 * Takes the form: [visitor] content... [/visitor]
 * Not necessary as an else with [loggedin], the other shortcode's else provides a message and a login link.
 *
 * @param $atts Array of arguments
 * @param $string Content contained within shortcode.
 *
 * @return string HTML to display.
 */
function visitor_check_shortcode( $atts, $content = null ) {
	if ( ( ! is_user_logged_in() && ! is_null( $content ) ) || is_feed() ) {
		return do_shortcode( $content );
	} else {
		return '';
	}
}

/**
 * Show contained to certain users only. Use in page or post content.
 * Takes the form: [access] content... [/access]
 *
 * @param $atts Array of arguments
 * @param $string Content contained within shortcode.
 *
 * @return string HTML to display.
 */

function access_check_shortcode( $attr, $content = null ) {

	$r = shortcode_atts( array(
		'capability' => 'read'
	), $attr );

	if ( current_user_can( $r['capability'] ) && ! is_null( $content ) && ! is_feed() ) {
		return do_shortcode( $content );
	} else {
		return '';
	}

}
