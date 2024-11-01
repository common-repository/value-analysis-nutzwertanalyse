<?php
 /*
  *
  * @package     value-analysis-nutzwertanalyse
  * @author      Severin Roth
  * @copyright   2021 Severin Roth
  * @version		 0.11
  * @license     GPL-2.0+
  *
  * @wordpress-plugin
	* Plugin Name: Value Analysis
	* Plugin URI: https://wordpress.org/plugins/value-analysis-nutzwertanalyse
	* Description: Value Analysis - Nutzwertanalyse mit gewichteter Paarvergleichsmethode
	* Author: Severin Roth
	* Version: 0.11
	* Tested up to: 6.0.1
	* Author URI: https://profiles.wordpress.org/severinroth
	* Text Domain: value_analysis
	* Domain Path: /languages
	* License:     GPL-2.0+
	* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

		if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(!is_plugin_active( 'value-analysis-nutzwertanalyse/init.php' )) return;

		define('vave_PATH',    	plugin_dir_path(__FILE__));
		define('vave_URL',     	plugins_url('', __FILE__));
		include_once(vave_PATH . '/PHP/vave_mainPHP.php');
		include_once(vave_PATH . '/PHP/vave_request.php');






		function vave_enqueue_scripts_core() {
			wp_register_style( 'value_analysis_css', 		vave_URL.'/CSS/style.css' );
			wp_enqueue_style( 'value_analysis_css' );

			wp_register_style( 'kendo_office365_css', 	vave_URL.'/CSS/Style/kendo.common-office365.min.css' );
			wp_enqueue_style( 'kendo_office365_css');
			wp_register_style( 'kendo_css', 						vave_URL.'/CSS/Style/kendo.common.min.css' );
			wp_enqueue_style( 'kendo_css' );


			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'kendo_all', 	 					vave_URL.'/JS/kendo.all.min.js', 				array() );
			wp_enqueue_script( 'vave_main', 	 					vave_URL.'/JS/vave_main.js', 						array() );
		}
		add_action( 'wp_enqueue_scripts',     'vave_enqueue_scripts_core', 1 );  // muss nach jQuery geladen werden
		add_action( 'admin_enqueue_scripts',  'vave_enqueue_scripts_core', 1 );


		function vave_menu() {
			add_options_page('value_analysis', 'Value Analysis', 'manage_options', 'vave_admin.php', 'vaAdminPageHTML');
		}
		if(is_admin()) {
			add_action('admin_menu', 'vave_menu', 100);
		}

		function vaAdminPageHTML(){
			require_once (vave_PATH . '/PHP/vave_admin.php');
		}

		function vave_addJSToHeader(){
			echo '<script type = "text/javascript">';
			echo 		' var vave_ajaxURL 				= "'.admin_url("admin-ajax.php").'"; ';
			echo 		' var vave_ajaxStatus 		= 0; ';							/* 0 => kein Request; 1 => Request läuft */
			echo 		' var vave_ajaxStack_i		= 0; ';
			echo 		' var vave_ajaxStack_vs 	= {}; ';
			echo 		' var vave_ajaxStack_e		= {}; ';
			echo 		'jQuery(document).ready(function(){';
			echo 			'';
			echo 		'});';
			echo 		'jQuery(window).load(function(){';
			echo 			'';
			echo 		'});';
			echo '</script>';
		}
		add_action( 'wp_head', 	'vave_addJSToHeader' );  	/* im Frontend	*/
		add_action( 'admin_head', 'vave_addJSToHeader' );	/* im Backend		*/

		/* Die staticNotification standardmässig einmal im Footer einbinden wp_footer	*/
		function vave_staticNotification_Hook() {
			echo '<script type = "text/javascript">';

			echo '	var elemSpan 				= document.createElement("span");			';
			echo '	elemSpan.id 				= "vave_staticNotification";						';
			echo '	document.body.appendChild(elemSpan);							';

			echo '	var elemDiv 				= document.createElement("div");			';
			echo '	elemDiv.id 					= "vave_staticNotificationAppendto";								';
			echo '	elemDiv.className 	= "vave_staticNotification-section k-content";	';
			echo '	document.body.appendChild(elemDiv);								';

			echo '</script>';

			wp_nonce_field( 'vave_nonce', 'vave_nonce' );
		}
		add_action( 'wp_footer', 	'vave_staticNotification_Hook' );  	/* im Frontend	*/
		add_action( 'admin_footer', 'vave_staticNotification_Hook' );	/* im Backend		*/


		function vave_plugin_loaded_textdomain() {
			// _e() __e() _x() _ex() usw das mo-File Laden Language File Internationalisierung
			// var_dump( determine_locale() );
			// var_dump( get_plugin_data( __FILE__ ) );
			load_plugin_textdomain( 'value_analysis', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
		add_action('plugins_loaded', 'vave_plugin_loaded_textdomain', 0 );
?>
