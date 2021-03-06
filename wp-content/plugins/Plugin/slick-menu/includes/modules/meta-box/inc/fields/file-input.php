<?php

/**
 * File input field class which uses an input for file URL.
 */
class SM_RWMB_File_Input_Field extends SM_RWMB_Field
{
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'sm-rwmb-file-input', SM_RWMB_JS_URL . 'file-input.js', array( 'jquery' ), SM_RWMB_VER, true );
		/**
		 * Prevent loading localized string twice.
		 * @link https://github.com/rilwis/meta-box/issues/850
		 */
		$wp_scripts = wp_scripts();
		if ( ! $wp_scripts->get_data( 'sm-rwmb-file-input', 'data' ) )
		{
			wp_localize_script( 'sm-rwmb-file-input', 'rwmbFileInput', array(
				'frameTitle' => __( 'Select File', 'meta-box' ),
			) );
		}
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{
		return sprintf(
			'<input type="text" class="sm-rwmb-file-input" name="%s" id="%s" value="%s" placeholder="%s" size="%s">
			<a href="#" class="sm-rwmb-file-input-select button-primary">%s</a>
			<a href="#" class="sm-rwmb-file-input-remove button %s">%s</a>',
			$field['field_name'],
			$field['id'],
			$meta,
			$field['placeholder'],
			$field['size'],
			__( 'Select', 'meta-box' ),
			$meta ? '' : 'hidden',
			__( 'Remove', 'meta-box' )
		);
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field )
	{
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'size'        => 30,
			'placeholder' => '',
		) );

		return $field;
	}
}
