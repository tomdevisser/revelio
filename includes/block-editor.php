<?php
/**
 * Enqueue assets and expose post meta via REST for the block editor.
 *
 * @package Revelio
 * @since   1.0.0
 */

/**
 * Enqueue styles and scripts for the block editor, including the Revelio sidebar.
 *
 * @since 1.0.0
 *
 * @return void
 */
function revelio_enqueue_admin_scripts() {
  wp_enqueue_style( 'revelio-css', REVELIO_PLUGIN_URL . 'assets/block-editor.css', array( 'wp-edit-post' ), REVELIO_VERSION );

  wp_enqueue_script_module( 'revelio-js', REVELIO_PLUGIN_URL . 'assets/block-editor.js', array( 'wp-plugins', 'wp-editor', 'wp-icons', 'wp-i18n' ), REVELIO_VERSION );
	wp_set_script_translations( 'revelio-js', 'revelio' );
}

/**
 * Register a REST field to expose all post meta in the block editor.
 *
 * @since 1.0.0
 *
 * @return void
 */
function revelio_post_meta_in_rest() {
	register_rest_field(
		get_post_types(),
		'all_meta',
		array(
			'get_callback' => function( $object ) {
				if ( ! isset( $object['id'] ) || ! is_numeric( $object['id'] ) ) {
					return array();
				}

				return get_post_meta( $object['id'] );
			},
			'schema' => null,
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'revelio_enqueue_admin_scripts' );
add_action( 'rest_api_init', 'revelio_post_meta_in_rest' );
