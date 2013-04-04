<?php
/*
Plugin Name: GA Universal
Plugin URI: http://www.ethoseo.com/tools/ga-universal
Description: The first Wordpress plugin for Google's Universal Analytics script Analytics.js.
Author: Ethoseo Internet Marketing
Version: 1.0
Author URI: http://www.ethoseo.com/
License: MIT License

© 2013 Ethoseo Internet Marketing

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

$ethoseo_gau_version = "1.0";
define( 'ETHOSEO_GAU_PATH', plugin_dir_path(__FILE__) );
define( 'ETHOSEO_GAU_FILE', __FILE__);

// PRINT SCRIPT
if((bool)get_option("ethoseo_gau_infooter")){
	add_action('wp_footer', 'ethoseo_gau_print_script');
}else{
	add_action('wp_head', 'ethoseo_gau_print_script');
}

// GET USER ROLE
function get_user_role($uid) {
	global $wpdb;
	$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
	  if(!$role) return 'non-user';
	$rarr = unserialize($role);
	$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
	return $roles[0];
}

function ethoseo_gau_print_script () {

	global $ethoseo_gau_version, $current_user;
	$debug = get_option("ethoseo_gau_debug");
	$console_debug = get_option("ethoseo_gau_consoledebug");

	echo "<script>";
	if(!$console_debug){
		echo "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
	}else{
		echo "ga = function() { return console.log.apply(console, arguments); };";
	}
	echo "\n\n";
	echo $debug ? "/* BEFORE GA() */\n" : "";
	echo get_option("ethoseo_gau_before");
	echo $debug ? "/* CREATE(S) */\n" : "";
	foreach(get_option("ethoseo_gau_properties") as $property){
		if(!$property['roles'][get_user_role($current_user->id)]){
			if($property['custom']){
				echo "ga('create', '" . $property['id'] . "', " . $property['custom'] . ");";
			}else{
				echo "ga('create', '" . $property['id'] . "');";
			}
		}
	}
	echo $debug ? "/* PAGE VIEW */\n" : "";
	if(get_option("ethoseo_gau_titleoverride") && get_the_title()){
		echo "ga('send', 'pageview', {'title' : '" . addslashes(get_the_title()) . "'});";
	}else{
		echo "ga('send', 'pageview');";
	}
	echo $debug ? "/* AFTER GA() */\n" : "";
	echo get_option("ethoseo_gau_after");
	echo "</script>";


}

// DEFAULTS

register_activation_hook( __FILE__, 'ethoseo_gau_activate' );
function ethoseo_gau_activate() {
	if(!get_option("ethoseo_gau_activated")){
		update_option("ethoseo_gau_activated", true);
		update_option("ethoseo_gau_properties", array());
		update_option("ethoseo_gau_titleoverride", false);
		update_option("ethoseo_gau_before", "");
		update_option("ethoseo_gau_after", "");
		update_option("ethoseo_gau_hidefor", array());
		update_option("ethoseo_gau_infooter", true);
		update_option("ethoseo_gau_debug", false);
		update_option("ethoseo_gau_consoledebug", false);
	}
}

// SETTINGS

add_action('admin_menu', 'ethoseo_gau_create_menu');
function ethoseo_gau_create_menu() {

	$ethoseo_gau_options_page = add_submenu_page('options-general.php', 'GA Universal', 'GA Universal', 'activate_plugins', 'ga-universal', 'ethoseo_gau_settings_page');
}

add_action( 'admin_enqueue_scripts', 'ethoseo_gau_admin_enqueue' );
function ethoseo_gau_admin_enqueue($hook) {
	global $ethoseo_gau_version;
	if($hook == "settings_page_ga-universal"){
    	wp_enqueue_script('jquery-form-repeater', plugins_url('js/admin.js', __FILE__), array(), "0.1.0", 'all');
    	wp_enqueue_style('ethoseo_gau_admin_css', plugins_url('css/admin.css', __FILE__), array(), $ethoseo_gau_version, 'all');
    }
    if($hook == "post-new.php" && $_GET['ethoseo-thanks-template'] == 1){
    	wp_enqueue_script('ethoseo-thanks', plugins_url('js/thanks.js', __FILE__), array(), "0.1.0", 'all');
    }
}

function ethoseo_gau_page ($pagename){

	if (!current_user_can('activate_plugins'))	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	global $wpdb;

	include(ETHOSEO_GAU_PATH . "inc/screens/$pagename.php");

}

function ethoseo_gau_settings_page() {
	ethoseo_gau_page('settings');
}

?>