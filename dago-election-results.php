<?php 
/**
 * @package da-first-plugin
 */
/*
	Plugin Name: DAGO Election Results
	Plugin URI: https:/wp_dago.com/plugin/da-election-results
	Description:  Plugin to display election result
	Version: 1.0.0
	Author: David Arago
	Author URI: https://davidarago.com
	License: GPLV2
	Text Domain: dago-election-resulsts-domain
 */
define('WP_DEBUG', true);

defined ( 'ABSPATH' ) or die();  //Makes sure that the plugin is inizialized by WP.

//Load Scripts
require_once( plugin_dir_path( __FILE__ ).'/includes/dago-election-results-scripts.php' );

//Load Class
require_once( plugin_dir_path( __FILE__ ).'/includes/dago-election-results-class.php' );

//Register Widget
function dago_register_election_results() {
	register_widget(  'DAGO_Election_Results_Widget' );  //Class Name
}
//Hook in function
add_action( 'widgets_init', 'dago_register_election_results' );