<?php
/**
 * Heading field class.
 */
class SM_RWMB_Heading_Field extends SM_RWMB_Field
{
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'sm-rwmb-heading', SM_RWMB_CSS_URL . 'heading.css', array(), SM_RWMB_VER );
	}

	/**
	 * Show begin HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function begin_html( $meta, $field )
	{
		$attributes = empty( $field['id'] ) ? '' : " id='{$field['id']}'";
		return sprintf( '<h4%s>%s</h4>', $attributes, $field['name'] );
	}

	/**
	 * Show end HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function end_html( $meta, $field )
	{
		$id = $field['id'] ? " id='{$field['id']}-description'" : '';

		return $field['desc'] ? "<p{$id} class='description'>{$field['desc']}</p>" : '';
	}
}
