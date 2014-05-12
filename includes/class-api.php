<?php
/**
 * GravityView template tags API
 *
 * @package   GravityView
 * @author    Zack Katz <zack@katzwebservices.com>
 * @license   ToBeDefined
 * @link      http://www.katzwebservices.com
 * @copyright Copyright 2013, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */



class GravityView_API {



	/**
	 * Fetch Field Label
	 *
	 * @access public
	 * @static
	 * @param mixed $field
	 * @return string
	 */
	public static function field_label( $field ) {

		if( !empty( $field['show_label'] ) ) {
			$label = empty( $field['custom_label'] ) ? $field['label'] : $field['custom_label'];
			$label .= apply_filters( 'gravityview_render_after_label', '', $field );
		} else {
			$label = '';
		}

		return $label .' ';

	}


	/**
	 * Fetch Field class
	 *
	 * @access public
	 * @static
	 * @param mixed $field
	 * @return string
	 */
	public static function field_class( $field ) {

		if( !empty( $field['custom_class'] ) ) {
			return sanitize_html_class($field['custom_class'], esc_attr($field['custom_class']));
		}

		return '';
	}


	/**
	 * Given an entry and a form field id, calculate the entry value for that field.
	 *
	 * @access public
	 * @param array $entry
	 * @param integer $field
	 * @return null|string
	 */
	public static function field_value( $entry, $field_settings ) {

		if( empty( $entry['form_id'] ) || empty( $field_settings['id'] ) ) {
			return NULL;
		}

		$field_id = $field_settings['id'];

		$value = '';

		$form = gravityview_get_form( $entry['form_id'] );
		$field = gravityview_get_field( $form, $field_id );


		if( $field_type = RGFormsModel::get_input_type($field) ) {
			$value = RGFormsModel::get_lead_field_value($entry, $field);
		} else {
			// For non-integer field types (`id`, `date_created`, etc.)
			$field_type = $field_id;
			$field['type'] = $field_id;
			$value = isset($entry[$field_type]) ? $entry[$field_type] : NULL;
		}

		$display_value = GFCommon::get_lead_field_display($field, $value, $entry["currency"]);
		$display_value = apply_filters("gform_entry_field_value", $display_value, $field, $entry, $form);

		switch( $field_type ){

			case 'address':
			case 'radio':
			case 'checkbox':
			case 'name':
				if( floatval( $field_id ) === floor( floatval( $field_id ) ) ) {
					// For the complete field value
					$value = $display_value;
				} else {
					// For part of the field value
					$entry_keys = array_keys( $entry );
					foreach( $entry_keys as $input_key ) {
						if( is_numeric( $input_key ) && floatval( $input_key ) === floatval( $field_id ) ) {
							if( in_array( $field['type'], array( 'radio', 'checkbox' ) ) && !empty( $entry[ $input_key ] ) ) {
								$value = apply_filters( 'gravityview_field_tick', '<span class="dashicons dashicons-yes"></span>', $entry, $field);
							} else {
								$value = $entry[ $input_key ];
							}
						}
					}
				}
				break;
			case 'textarea' :
			case 'post_content' :
			case 'post_excerpt' :
				if( apply_filters( 'gravityview_show_fulltext', true, $entry, $field_id ) ) {
					$long_text = $value = '';

					if( isset( $entry[ $field_id ] ) && strlen( $entry[ $field_id ] ) >= GFORMS_MAX_FIELD_LENGTH ) {
					   $long_text = RGFormsModel::get_lead_field_value( $entry, RGFormsModel::get_field( $form, $field_id ));
					}
					if( isset( $entry[ $field_id ] ) ) {
						$value = !empty( $long_text ) ? $long_text : $entry[ $field_id ];
					}
				}

				$value = esc_html( $value );

				if( apply_filters( 'gravityview_entry_value_wpautop', true, $entry, $field_id ) ) {
					$value = wpautop( $value );
				};

				break;

			case 'date_created':
				$value = GFCommon::format_date( $entry['date_created'], true, apply_filters( 'gravityview_date_format', '' ) );
				break;

			case 'date':
				$value = GFCommon::date_display( $value, apply_filters( 'gravityview_date_format', $field['dateFormat'] ) );
				break;

			default:
				$value = $display_value;
				break;

		} //switch


		//if show as single entry link is active
		if( !empty( $field_settings['show_as_link'] ) ) {
			$href = self::field_link($entry, $field);
			$value = '<a href="'. $href .'">'. $value . '</a>';
		}

		return apply_filters( 'gravityview_field_entry_value', $value, $entry, $field_settings );
	}


	// return href for single entry
	public static function field_link( $entry, $field ) {

		$post = get_post();

		if( !empty( $post->ID ) ) {

			$query_arg_name = GravityView_frontend::get_entry_var_name();

			if( get_option('permalink_structure') ) {
				$href = trailingslashit( get_permalink( $post->ID ) ) . $query_arg_name . '/'. $entry['id'] .'/';
			} else {
				$href = add_query_arg( $query_arg_name, $entry['id'], get_permalink( $post->ID ) );
			}

			return $href;
		}

		return false;
	}


}


// inside loop functions

function gv_label( $field ) {
	return GravityView_API::field_label( $field );
}

function gv_class( $field ) {
	return GravityView_API::field_class( $field );
}

function gv_value( $entry, $field ) {
	return GravityView_API::field_value( $entry, $field );
}

function gv_link(  $entry, $field ) {
	return GravityView_API::field_link( $entry, $field );
}

//
function gravityview_back_link() {

	$post = get_post();

	if( empty( $post ) ) {
		return '';
	}

	$href = trailingslashit( get_permalink( $post->ID ) );
	$label = apply_filters( 'gravityview_go_back_label', __( 'Go back', 'gravity-view' ), $post );

	return '<a href="'. $href .'" id="gravityview_back_link">'. esc_html( $label ) . '</a>';

}



// Templates' hooks
function gravityview_before() {
	do_action( 'gravityview_before', gravityview_get_view_id() );
}

function gravityview_header() {

	do_action( 'gravityview_header', gravityview_get_view_id() );
}

function gravityview_footer() {
	do_action( 'gravityview_footer', gravityview_get_view_id() );
}

function gravityview_after() {
	do_action( 'gravityview_after', gravityview_get_view_id() );
}

function gravityview_get_view_id() {
	global $gravityview_view;
	return $gravityview_view->view_id;
}

function gravityview_get_context() {
	global $gravityview_view;
	return $gravityview_view->context;
}
