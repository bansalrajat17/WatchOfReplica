<?php

/**
 * Abstract field to select an object: post, user, taxonomy, etc.
 */
abstract class SM_RWMB_Object_Choice_Field extends SM_RWMB_Choice_Field
{
	/**
	 * Get field HTML
	 *
	 * @param mixed $options
	 * @param mixed $db_fields
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function walk( $field, $options, $db_fields, $meta )
	{
		return call_user_func( array( self::get_type_class( $field ), 'walk' ), $field, $options, $db_fields, $meta );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function normalize( $field )
	{
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'flatten'    => true,
			'query_args' => array(),
			'field_type' => 'select_advanced',
		) );

		if ( 'checkbox_tree' === $field['field_type'] )
		{
			$field['field_type'] = 'checkbox_list';
			$field['flatten']    = false;
		}
		if ( 'radio_list' == $field['field_type'] )
		{
			$field['multiple'] = false;
		}
		if ( 'checkbox_list' == $field['field_type'] )
		{
			$field['multiple'] = true;
		}
		return call_user_func( array( self::get_type_class( $field ), 'normalize' ), $field );
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null )
	{
		$attributes = call_user_func( array( self::get_type_class( $field ), 'get_attributes' ), $field, $value );
		if ( 'select_advanced' == $field['field_type'] )
		{
			$attributes['class'] .= ' sm-rwmb-select_advanced';
		}
		return $attributes;
	}

	/**
	 * Get field names of object to be used by walker
	 * @return array
	 */
	public static function get_db_fields()
	{
		return array(
			'parent' => '',
			'id'     => '',
			'label'  => '',
		);
	}

	/**
	 * Save meta value
	 *
	 * @param $new
	 * @param $old
	 * @param $post_id
	 * @param $field
	 */
	static function save( $new, $old, $post_id, $field )
	{
		delete_post_meta( $post_id, $field['id'] );
		parent::save( $new, array(), $post_id, $field );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		SM_RWMB_Input_List_Field::admin_enqueue_scripts();
		SM_RWMB_Select_Field::admin_enqueue_scripts();
		SM_RWMB_Select_Tree_Field::admin_enqueue_scripts();
		SM_RWMB_Select_Advanced_Field::admin_enqueue_scripts();
	}

	/**
	 * Get correct rendering class for the field.
	 * @param array $field Field parameter
	 * @return string
	 */
	protected static function get_type_class( $field )
	{
		if ( in_array( $field['field_type'], array( 'checkbox_list', 'radio_list' ) ) )
		{
			return 'SM_RWMB_Input_List_Field';
		}
		return self::get_class_name( array( 'type' => $field['field_type'] ) );
	}
}
