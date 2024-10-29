<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://yatnam.com/
 * @since      1.0.0
 *
 * @package    Acfca
 * @subpackage Acfca/admin
 */


/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acfca
 * @subpackage Acfca/admin
 * @author     Yatnam <mail@yatnam.com>
 */
class Acfca_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		//For Styles
		add_action('admin_enqueue_scripts', array($this, 'acfca_enqueue_styles'));
		
		//For Scripts		
		add_action('admin_enqueue_scripts', array($this, 'acfca_enqueue_scripts'));

		//Form HTML Hook
		add_action( 'acf/render_field', array($this, 'acfca_form_html'), 10, 1 );

		//Modal HTML Hook
		add_action('admin_footer',  array($this, 'acfca_modal_html'));
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function acfca_enqueue_styles() {
		
		wp_enqueue_style( 'acfca-admin-style', plugin_dir_url( __FILE__ ) . 'css/acfca-admin.css', false, '1.0.0' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function acfca_enqueue_scripts() {

		wp_enqueue_script(  'acfca-admin-script', plugin_dir_url( __FILE__ ) . 'js/acfca-admin.js',  array('jquery'), null, true);

	}

	/**
	 * Add Class form into field labels
	 *
	 * @since    1.0.0
	 */
	public function acfca_form_html( $field ) {
		$screen 			= 	get_current_screen();
		if ( ! ( $screen->post_type == 'acf-field-group' ) ) { 
			if ( ( $screen->post_type == null ) ) { 
				$options	= 	'options';
			} else{
				$options	= 	false;
			}
			if ( $this->acfca_is_sub_field( $field['parent'] ) ) { 
				$sub_field 		= 	get_sub_field_object( $field['_name'], true );	
				$fieldClasses 	= 	$this->acfca_get_backend_sub_field_classes( $field['parent'], $field['_name'], $field['id'], $options );					
			} else { 
				$fieldClasses 	= 	$this->acfca_get_backend_field_classes( $field['_name'], $options);
			}	
			$formContent 	= 	"<div id='".esc_attr($field['id'])."' data-name='".esc_attr($field['name'])."' data-id='".esc_attr($field['id'])."'  class='m-opener wp-menu-image dashicons-before dashicons-admin-appearance'><input type='hidden' value='".esc_attr($fieldClasses)."'/></div>";
			echo $formContent;
		}
	}

	/**
	 * Modal that appear for each Add Class Html
	 *
	 * @since    1.0.0
	 */
	public function acfca_modal_html() {
		global $post;
		if ($post != null) {
			$id 	= 	$post->ID;
		} else{
			$id 	= 	null;
		}
		
		$modalContent 	= 
		"<div id='acfca_modal' class='modal'> 
			<div class= 'acfca_modal_content'>
				<button type='button' class='close components-icon-button dashicons-before dashicons-no-alt' data-dismiss='modal'></button>
				<h2 class='dashicons-before dashicons-admin-appearance'>Add Class</h2>
				<form action='post' id='add_class_form'>
					<input id='action' type='hidden' name='action' value='acfca_add_class_ajax'/>
					<input id='acfca_field_id' type='hidden' name='acfca_field_id' value='' />
					<input id='acfca_post_id' type='hidden' name='acfca_post_id' value='".esc_attr( $id )."' />
					<input id='acfca_field_class' type='text' name='acfca_field_class' value= '' >
					<input type='submit' class='acf-button button button-primary saveButton' value='Save'/>
				</form>
			</div>
		</div>";
		echo $modalContent;
	}
	
	/**
	 * Retrieve either a heirarchical array of a recursive field or 
	 * the index of the recursive subfield
	 *
	 * @since    1.0.0
	 */
	public function acfca_get_field_array( $fieldId, $nth_subfield = false ){
		$array 		= 	explode( "-", $fieldId );
	    $fieldId 	= 	preg_grep( '@[\d]@', $array );
	    if ($nth_subfield) {
	    	return preg_grep('/^field/', $fieldId, PREG_GREP_INVERT);
	    } else {
	    	return preg_grep( "/^field/", $fieldId );
	    }
	}

	/**
	 * Retrieve the name of the field to which classes are applied
	 * @since    1.0.0
	 */
	public function acfca_generate_field_name($field_array, $nth_subfield){
		global $wpdb;
		$field 		= 	null;
		$table 		= 	$wpdb->prefix.'posts';	
		$i 			= 	0;
		$x 			= 	0;
		$index 		= 	key($nth_subfield);
		foreach ($field_array as $key) {
			$key 		= 	preg_replace('/\s+/', ' ', $key);
			$posts		= 	$wpdb->get_results("SELECT * FROM ".$table." WHERE post_name = '".$key."'");
			if( count($nth_subfield) > $x ){
				$i 				= 	$i + $index; 
				$field['name'] 	= 	$field['name']."_".$posts[0]->post_excerpt."_".$nth_subfield[$i];
				$x++;
			}
			else{
				$field['name'] 	= 	$field['name']."_".$posts[0]->post_excerpt."_";
			}
		}
		$value		= 	substr($field['name'], 1, -1);
		return $value;
	}

	/**
	 * Retrieve the classes applied to certain selector in the database
	 *
	 * @since    1.0.0
	 */
	public function acfca_get_class_values( $field, $post_id = 0, $option = false){
		if ( ( $option == false ) && ( $post_id != null ) ) { 	//normal pages
			global $wpdb;
			$table 			= 	$wpdb->prefix.'postmeta';		
			$field['name']	= 	preg_replace('/\s+/', ' ', $field['name']);
			if( substr_count( $field['name'], "%" ) == 1 ) { 
				$val 		= 	"%"; 
			} else { 
				$val 		= 	""; 
			}
			$posts 			= 	$wpdb->get_results( "SELECT * FROM ".$table." WHERE post_id = ".$post_id."  AND meta_key = '".$field['name']."_classes".$val."'" );
			if( ! empty( $posts ) ) {
				if ( $posts[0]->meta_value == null ) {
					return null;
				} else {
					return $posts[0]->meta_value;
				}
			} else {
				return null;
			}
		} else if ( ( 'options' == $option ) && ( $post_id == null ) )  {  	//options page
			global $wpdb;
			$table 			= 	$wpdb->prefix.'options';	
			$field['name']	= 	preg_replace( '/\s+/', ' ', $field['name'] );
			$posts 			= 	$wpdb->get_results( "SELECT * FROM ".$table." WHERE option_name = '".$field['name']."_classes'" );
			if( ! empty( $posts ) ) {
				if ( $posts[0]->option_value == null ) {
					return null;
				} else {
					return $posts[0]->option_value;
				}
			} else {
				return null;
			}
		}		
	}

	/**
	 * Retrieve whether the field is a subfield, return true if subfield else return false
	 *
	 * @since    1.0.0
	 */
	public function acfca_is_sub_field( $parent ){
		global $wpdb;
		$table 		= $wpdb->prefix.'posts';	
		$parent 	= preg_replace( '/\s+/', ' ', $parent );	
		$posts 		= $wpdb->get_results( "SELECT post_type, ID FROM ".$table." WHERE ID = ".$parent."" );
		if( ! empty( $posts ) ) {
			if ( ( $posts[0]->post_type )  ==  "acf-field" ) {
				return true;
			} else{ 
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Retrieve the Classes added to the simple fields in the backend
	 *
	 * @since    1.0.0
	 */
	public function acfca_get_backend_field_classes( $selector, $options = false, $post_id = false, $format_value = true ) {
		$post_id 	= acf_get_valid_post_id( $post_id );			// filter post_id
		$field 		= acf_maybe_get_field( $selector, $post_id );	// get field

		// create dummy field
		if( ! $field ) {		
			$field 	= acf_get_valid_field( array(
						'name'	=> $selector,
						'key'	=> '',
						'type'	=> '',
					 	) );					
			$format_value = false;		// prevent formatting	
		}

		if ( $options == 'options' ) {
			global $wpdb;						
			$info 	= acf_get_post_id_info($post_id);		// get value for field

			if( ( $info['type'] != 'post' ) || ( $info['type'] != 'user' ) || ( $info['type'] != 'comment' ) || ( $info['type'] != 'term' ) ){
				$table 			= $wpdb->prefix.'options';
				$field['name']	= preg_replace('/\s+/', ' ', $field['name']);	
				$option_name 	= 'options_'.$field['name'];
				$result 		= $wpdb->get_results("SELECT * FROM ".$table." WHERE option_name = '".$option_name."_classes'");
				if (! empty( $result )) {
					return $result[0]->option_value;
				} else {
					return false;
				}
				
			} else {
				return false;
			}	 
		} else {
			$value 	= $this->acfca_get_class_values( $field, $post_id );  // get value for field
			return $value;	
		}
		 
	}

	/**
	 * Retrieve the Classes added to the recursive fields in the backend
	 *
	 * @since    1.0.0
	 */
	public function acfca_get_backend_sub_field_classes($parent, $selector, $nth_field, $options = false, $post_id = false, $format_value = true ){
		//filter post_id
		$post_id 		= 	acf_get_valid_post_id( $post_id );	
		$field_array 	= 	$this->acfca_get_field_array($nth_field);
		$nth_subfield 	= 	$this->acfca_get_field_array($nth_field, true);

		if ( 'options' == $options ) {
			$field['name'] 	= 	"options_".$this->acfca_generate_field_name($field_array, $nth_subfield);
			$value 			= 	$this->acfca_get_class_values( $field, $post_id, 'options');
		} else{
			$field['name'] 	= 	$this->acfca_generate_field_name($field_array, $nth_subfield);
			$value 			= 	$this->acfca_get_class_values( $field, $post_id);
		}		
		return $value;
	}

}
