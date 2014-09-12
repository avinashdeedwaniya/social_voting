<?php
if(!session_id() ) {
    session_start();
}
/**
Plugin Name: Social Voting Plugin
Plugin URI:
Description: Voting through facebook, twitter, linked-in and Email.
Version: 1.0
**/

$dir = plugin_dir_path( __FILE__ );

/* Load required lib files. */
require_once($dir.'includes/sv_setup.php');
require_once($dir.'twitteroauth/twitteroauth.php');
require_once($dir.'config.php');

add_action('init', 'sv_add_scripts');
function sv_add_scripts(){
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'plugin-style', plugins_url('assets/css/style.css', __FILE__) );
    wp_register_script( 'social_vote_ajax_script', plugins_url('assets/js/sv_voting.js', __FILE__) );
    wp_localize_script( 'social_vote_ajax_script', 'social', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    wp_enqueue_script( 'social_vote_ajax_script' );
}
require_once($dir.'includes/sv_voting.php');
require_once($dir.'includes/sv_ajax_functions.php');
require_once($dir.'includes/sv_widget.php');
require_once($dir.'includes/sv_admin.php');
require_once($dir.'includes/sv_shortcodes.php');
require_once($dir.'includes/sv_functions.php');