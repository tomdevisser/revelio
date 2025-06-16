<?php
/**
 * Enqueue styles and display custom post meta in the classic editor.
 *
 * @package Revelio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Enqueue styles and scripts for the classic editor.
 *
 * @since 1.0.0
 *
 * @return void
 */
function revelio_enqueue_admin_styles() {
	wp_enqueue_style(
		'revelio-styles',
		REVELIO_PLUGIN_URL . 'build/classic-editor.css',
		array(),
		REVELIO_VERSION
	);

	wp_enqueue_script_module( 'revelio-js', REVELIO_PLUGIN_URL . 'build/classic-editor.js', array( 'wp-i18n' ), REVELIO_VERSION );
	wp_set_script_translations( 'revelio-js', 'revelio' );
}
add_action( 'admin_enqueue_scripts', 'revelio_enqueue_admin_styles' );

/**
 * Register the Revelio post meta box in the classic editor for users with manage_options.
 *
 * @since 1.0.0
 *
 * @return void
 */
function revelio_post_meta_box() {
	/**
	 * Role-based access control for classic editor.
	 */
	$options       = get_option( 'revelio_settings', array() );
	$allowed_roles = isset( $options['allowed_roles'] ) ? $options['allowed_roles'] : array();
	$current_user  = wp_get_current_user();

	if ( empty( $allowed_roles ) ) {
		/**
		 * Fallback to manage_options if no roles selected.
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	} elseif ( ! array_intersect( $allowed_roles, $current_user->roles ) ) {
		/**
		 * User role not allowed.
		 */
		return;
	}

	add_meta_box(
		'revelio-post-meta-box',
		__( 'Revelio Post Meta', 'revelio' ),
		'revelio_render_meta_box_cb',
		get_post_types(),
		'side',
		'core',
		array(
			'__back_compat_meta_box' => true,
		)
	);
}
add_action( 'add_meta_boxes', 'revelio_post_meta_box' );

/**
 * Render callback for the Revelio post meta box.
 *
 * @since 1.0.0
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function revelio_render_meta_box_cb( $post ) {
	$meta = get_post_meta( $post->ID );

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

	if ( empty( $meta ) ) {
		echo '<p>' . __( 'No post meta found.', 'revelio' ) . '</p>';
		return;
	}

	ob_start();
	?>

	<table class="revelio-post-meta-table">
	<?php
	foreach ( $meta as $key => $values ) {
		?>
		<tr>
			<th scope="row">
				<code><?php echo esc_html( $key ); ?></code>
			</th>
			<td>
				<?php
				foreach ( $values as $value ) {
					?>
					<p><?php echo esc_html( $value ); ?></p>
					<?php
				}
				?>
			</td>
		</tr>
		<?php
	}
	?>
	</table>

	<?php
	$markup = ob_get_clean();

	/**
	 * Filter the Revelio meta box markup.
	 *
	 * @param string  $markup The meta box HTML.
	 * @param WP_Post $post   The current post object.
	 */
	echo apply_filters( 'revelio_meta_box_markup', $markup, $post );
}