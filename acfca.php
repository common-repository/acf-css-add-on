<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yatnam.com/
 * @since             1.0.1
 * @package           Acfca
 *
 * @wordpress-plugin
 * Plugin Name:       ACF CSS Add-on
 * Plugin URI:        
 * Description:       Add class or classes to all the acf fields available. Seperate the classes with spaces. This plugin can be used as an add-on to the Advanced Custom Fields Plugin. Helps you add a class to any of the ACF Custom Fields, including repeater, flexible content. Add multiple classes to the fields by seperating them with spaces. Add class to options pages also.
 * Version:           1.0.0
 * Author:            Yatnam
 * Author URI:        https://yatnam.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acfca
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACFCA_VERSION', '1.0.0' );
define( 'ACFCA_PATH', plugin_dir_path( __FILE__ ) );

include( ACFCA_PATH . 'includes/deprecated.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acfca-activator.php
 */
function activate_acfca() {
	if ( ! class_exists( 'acf' ) ) { _e( 'Please activate ACF or ACF Pro plugin to start using ACF CSS Add-on', 'ACFCA' ); exit; }
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acfca-activator.php';
	Acfca_Activator::activate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acfca-deactivator.php
 */
function deactivate_acfca() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acfca-deactivator.php';
	Acfca_Deactivator::deactivate();
}


register_activation_hook( __FILE__, 'activate_acfca' );
register_deactivation_hook( __FILE__, 'deactivate_acfca' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-acfca.php';


/**
 * Ajax action for adding class values to database
 *
 * @since     1.0.0
 * @return    Id updated    The version number of the plugin.
 */
function acfca_add_class_ajax() {
	global $wpdb;
	$adminObject 	= 	new Acfca_Admin( 'acfca', ACFCA_VERSION );

	$acfca_field_id	= 	sanitize_text_field( $_POST['acfca_field_id'] );
	if ( ! $acfca_field_id ) {
	  	$acfca_field_id = '';
	}

	$field_array 	= 	$adminObject->acfca_get_field_array( $acfca_field_id );
	$nth_subfield 	= 	$adminObject->acfca_get_field_array( $acfca_field_id, true);

	$field_name 	= 	$adminObject->acfca_generate_field_name( $field_array, $nth_subfield );
	
	$acfca_classes 	= 	sanitize_text_field( $_POST['acfca_field_class'] );
	if ( ! $acfca_classes ) {
	  	$acfca_classes = '';
	}

	$acfca_post_id 	= sanitize_text_field( $_POST['acfca_post_id'] );
	if ( ! $acfca_post_id ) {
	  	$acfca_post_id = 'option';
	}

	/* pages under theme settings does not have post id */
	if( $acfca_post_id == 'option' ){ 

		$options_field_name 	=	"options_".$field_name;
		$table 					= 	$wpdb->prefix.'options';
	    $meta_key['name']		= 	$options_field_name;
	    $meta_key_of_value 		= 	$options_field_name."_classes";
	    $meta_key_of_value 		= 	preg_replace('/\s+/', ' ', $meta_key_of_value);
    	$data 					= 	array( 
	    								'option_name' 	=> $meta_key_of_value, 
										'option_value' 	=> $acfca_classes, 
										'autoload' 		=> 'no'
								  	);
		$format 				= 	array(
										'%s',
										'%s'
								  	);
	    if( $adminObject->acfca_get_class_values( $meta_key , null, 'options') ){
	    	$wpdb->update( $table, $data, array( 'option_name' => $meta_key_of_value ), $format);
	    } else {
	    	$wpdb->insert( $table, $data, $format);
	    }	
	} else{	    

		$postID 				= 	$acfca_post_id;
	    $table 					= 	$wpdb->prefix.'postmeta';
	    $meta_key['name']		= 	$field_name;
	    $meta_key_of_value 		= 	$field_name.'_classes';
	    $meta_key_of_value 		= 	preg_replace( '/\s+/', ' ', $meta_key_of_value );
	    $data 					= 	array( 
		    							'post_id' 		=> $postID, 
								    	'meta_key' 		=> ltrim( $meta_key_of_value, " " ), 
								    	'meta_value' 	=> $acfca_classes
							      	);
		$format 				= 	array(
										'%d',
										'%s',
										'%s'
									);

		if( $adminObject->acfca_get_class_values( $meta_key, $postID , false) ) {
	    	$updateresult 		= 	$wpdb->update( $table, $data, array( 'meta_key' => ltrim( $meta_key_of_value, " " ) ), $format );
	    } else {
	    	$result				= 	$wpdb->insert( $table, $data, $format );
	    }	
    }	

	/* Restore original Post Data */
	wp_reset_postdata();
    wp_die();
}



/**
 * The frontend function to retrive classes for normal fields
 * @since     1.0.0
 * @return    simple fields classes      The version number of the plugin.
 */
if ( !function_exists('get_field_classes') ) {
	function get_field_classes( $selector, $post_id = false, $format_value = true ) {
		if ( 'option' == $post_id ) {
			// get the theme options classes
			return get_field( $selector.'_classes', 'option' );
		} else { 
			// get the other field classes
			return get_field( $selector.'_classes' );
		}	
	}
}


/**
 * The frontend function to retrive classes for recursive sub fields
 * @since     1.0.0
 * @return    recursive fields classes    The version number of the plugin.
 */
if ( !function_exists('get_sub_field_classes') ) {
	function get_sub_field_classes($selector, $post_id = false, $format_value = true ){
		$adminObject = new Acfca_Admin( 'acfca', ACFCA_VERSION );

		if ( 'option' == $post_id ) {
			$post_id 		= 	false;
			$post_id 		= 	acf_get_valid_post_id( $post_id );	// filter post_id		
			$sub_field 		= 	get_sub_field_object( $selector, $format_value );	// get sub field
			
			// bail early if no sub field
			if( !$sub_field ) return false;

			$field['name'] 	= 	"options_".$sub_field["name"];
			$value 			= 	$adminObject->acfca_get_class_values( $field, null, 'options' );
	 
			// return 
			return $value;

		} else{
			$post_id 		= 	acf_get_valid_post_id( $post_id );	// filter post_id		
			$sub_field 		= 	get_sub_field_object( $selector, $format_value );	// get sub field
			
			// bail early if no sub field
			if( !$sub_field ) return false;

			$value 			= 	$adminObject->acfca_get_class_values( $sub_field, $post_id );

			// return 
			return $value;
		}
		
	}
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acfca() {

	$plugin = new Acfca();
	$plugin->run();

	//AJAX Action Hooks
	add_action( 'wp_ajax_acfca_add_class_ajax', 'acfca_add_class_ajax' );
	add_action( 'wp_ajax_nopriv_acfca_add_class_ajax', 'acfca_add_class_ajax' );
}

run_acfca();