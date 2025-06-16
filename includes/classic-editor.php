<?php
/**
 * Enqueue styles and display custom post meta in the classic editor.
 *
 * @package Revelio
 * @since   1.0.0
 */

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
		REVELIO_PLUGIN_URL . 'assets/classic-editor.css',
		array(),
		REVELIO_VERSION
	);

	wp_enqueue_script_module( 'revelio-js', REVELIO_PLUGIN_URL . 'assets/classic-editor.js', array( 'wp-i18n' ), REVELIO_VERSION );
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
	if ( current_user_can( 'manage_options' ) ) {
		add_meta_box(
			'revelio-post-meta-box',
			__( 'Revelio Post Meta', 'revelio' ),
			'revelio_render_meta_box_cb',
			'post',
			'side',
			'core',
			array(
				'__back_compat_meta_box' => true,
			)
		);
	}
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