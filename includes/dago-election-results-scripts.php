<?php

defined ( 'ABSPATH' ) or die();  //Makes sure that the plugin is inizialized by WP.

// Add Scripts

function dago_election_results_add_scripts() {
	//Add Main CSS
	wp_enqueue_style( 'dago-election-results-style', plugins_url().'/dago-election-results/assets/css/style.css' );
	//Add Main JS
	wp_enqueue_script( 'dago-election-results-script', plugins_url().'/dago_election-results/assets/js/script.js' );
}

add_action( 'wp_enqueue_scripts', 'dago_election_results_add_scripts' ); 