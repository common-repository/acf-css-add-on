<?php
/**
 * Deprecated pluggable functions from past acfca versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed in a
 * later version.
 *
 * Deprecated warnings are also thrown if one of these functions is being defined by a plugin.
 *
 * @package ACFCA
 * @subpackage Deprecated
 */
 

 /**
 * Get the options page acf fields.
 *
 * @since 1.0.0
 * @deprecated 2.0.0 Use get_field_classes()
 * @see get_field_classes()
 *
 * @param int|null $selector Selector.
 * @param string $name Optional. The user's username
 * @return class 
 */
 if ( !function_exists('get_theme_settings_classes') ){
	function get_theme_settings_classes($selector, $option = false, $post_id = false, $format_value = true ) {
		_deprecated_function( 'get_theme_settings_classes', '2.0.0', 'get_field_classes' );
		return get_field_classes( $selector, 'option', $format_value );
	}
}


/**
 * Get the options page acf sub fields.
 *
 * @since 1.0.0
 * @deprecated 2.0.0 Use get_sub_field_classes()
 * @see get_sub_field_classes()
 *
 * @param int|null $selector Selector.
 * @param string $name Optional. The user's username
 * @return class
 */
if ( !function_exists('get_theme_settings_sub_classes') ) {
	function get_theme_settings_sub_classes($selector, $option = false, $post_id = false, $format_value = true ){
		_deprecated_function( 'get_theme_settings_sub_classes', '2.0.0', 'get_sub_field_classes' );
		return get_sub_field_classes( $selector, 'option', $format_value );
	}
}