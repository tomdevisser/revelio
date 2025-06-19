<?php
/**
 * Enqueue assets and expose post meta via REST for the block editor.
 *
 * @package Revelio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Enqueue styles and scripts for the block editor, including the Revelio sidebar.
 *
 * @since 1.0.0
 *
 * @return void
 */
function revelio_enqueue_admin_scripts() {
  wp_enqueue_style( 'revelio-css', REVELIO_PLUGIN_URL . 'build/block-editor.css', array( 'wp-editor' ), REVELIO_VERSION );

  wp_enqueue_script( 'revelio-js', REVELIO_PLUGIN_URL . 'build/block-editor.js', array( 'wp-plugins', 'wp-editor', 'wp-icons', 'wp-i18n' ), REVELIO_VERSION );
	wp_set_script_translations( 'revelio-js', 'revelio' );
}
add_action( 'enqueue_block_editor_assets', 'revelio_enqueue_admin_scripts' );

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
		'revelio-meta',
		array(
			'get_callback' => function( $object ) {
				if ( ! isset( $object['id'] ) || ! is_numeric( $object['id'] ) ) {
					return array();
				}

				/**
				 * Retrieve all post meta for the given post ID.
				 */
				$meta = get_post_meta( $object['id'] );

				/**
				 * Fetch excluded keys from plugin settings.
				 */
				$options      = get_option( 'revelio_settings', array() );
				$excluded_raw = isset( $options['excluded_meta'] ) ? $options['excluded_meta'] : '';
				$exclude_keys = array_filter( array_map( 'trim', explode( ',', $excluded_raw ) ) );

				/**
				 * Remove excluded keys.
				 */
				if ( ! empty( $exclude_keys ) ) {
					foreach ( $exclude_keys as $key ) {
						if ( isset( $meta[ $key ] ) ) {
							unset( $meta[ $key ] );
						}
					}
				}

				return $meta;
			},
			'schema' => null,
		)
	);
}
add_action( 'rest_api_init', 'revelio_post_meta_in_rest' );
