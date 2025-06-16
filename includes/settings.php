<?php

/**
 * Add settings link to the Plugins page.
 *
 * @since 1.0.0
 *
 * @param array $links Existing plugin action links.
 * @return array Modified plugin action links.
 */
function revelio_plugin_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=revelio-settings' ) ) . '">' . esc_html__( 'Settings', 'revelio' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_revelio/revelio.php', 'revelio_plugin_action_links' );

/**
 * Settings for Revelio plugin.
 *
 * @package Revelio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add Revelio settings page under the Settings menu.
 *
 * @since 1.0.0
 */
function revelio_add_settings_page() {
	add_options_page(
		__( 'Revelio Settings', 'revelio' ),
		__( 'Revelio', 'revelio' ),
		'manage_options',
		'revelio-settings',
		'revelio_render_settings_page'
	);
}
add_action( 'admin_menu', 'revelio_add_settings_page' );

/**
 * Register Revelio settings, sections, and fields.
 *
 * @since 1.0.0
 */
function revelio_register_settings() {
	register_setting(
		'revelio_settings_group',
		'revelio_settings',
		'revelio_sanitize_settings'
	);

	add_settings_section(
		'revelio_common_section',
		__( 'Common Settings', 'revelio' ),
		'__return_false',
		'revelio-settings'
	);

	add_settings_field(
		'revelio_allowed_roles',
		__( 'Allowed Roles', 'revelio' ),
		'revelio_allowed_roles_field_cb',
		'revelio-settings',
		'revelio_common_section'
	);

	add_settings_field(
		'revelio_excluded_meta',
		__( 'Excluded Meta Keys', 'revelio' ),
		'revelio_excluded_meta_field_cb',
		'revelio-settings',
		'revelio_common_section'
	);
}
add_action( 'admin_init', 'revelio_register_settings' );

/**
 * Sanitize Revelio settings.
 *
 * @since 1.0.0
 *
 * @param array $input Raw input.
 * @return array Sanitized input.
 */
function revelio_sanitize_settings( $input ) {
	$output = array();

	/**
	 * Sanitize allowed roles as array of slugs.
	 */
	if ( ! empty( $input['allowed_roles'] ) && is_array( $input['allowed_roles'] ) ) {
		$output['allowed_roles'] = array_map( 'sanitize_key', $input['allowed_roles'] );
	} else {
		$output['allowed_roles'] = array();
	}

	/**
	 * Sanitize excluded meta keys as comma-separated string.
	 */
	if ( isset( $input['excluded_meta'] ) ) {
		$output['excluded_meta'] = sanitize_text_field( $input['excluded_meta'] );
	} else {
		$output['excluded_meta'] = '';
	}

	return $output;
}

/**
 * Render checkboxes for allowed roles.
 *
 * @since 1.0.0
 */
function revelio_allowed_roles_field_cb() {
	$options  = get_option( 'revelio_settings', array() );
	$selected = isset( $options['allowed_roles'] ) ? $options['allowed_roles'] : array();
	$roles    = get_editable_roles();

	/**
	 * Always include Administrator, cannot be unchecked.
	 */
	if ( isset( $roles['administrator'] ) ) {
		printf(
			'<input type="hidden" name="revelio_settings[allowed_roles][]" value="administrator">',
		);
		printf(
			'<label><input type="checkbox" checked disabled> %s</label><br>',
			esc_html( $roles['administrator']['name'] )
		);
	}

	foreach ( $roles as $role_slug => $role_info ) {
		if ( 'administrator' === $role_slug ) {
			continue;
		}

		printf(
			'<label><input type="checkbox" name="revelio_settings[allowed_roles][]" value="%1$s" %2$s> %3$s</label><br>',
			esc_attr( $role_slug ),
			checked( in_array( $role_slug, $selected, true ), true, false ),
			esc_html( $role_info['name'] )
		);
	}

		echo '<p class="description">' . esc_html__( 'Select which user roles are allowed to view hidden post meta fields.', 'revelio' ) . '</p>';
}

/**
 * Render input for excluded meta keys.
 *
 * @since 1.0.0
 */
function revelio_excluded_meta_field_cb() {
	$options = get_option( 'revelio_settings', array() );
	$value   = isset( $options['excluded_meta'] ) ? $options['excluded_meta'] : '';

	printf(
		'<input type="text" name="revelio_settings[excluded_meta]" value="%s" class="regular-text">',
		esc_attr( $value )
	);

	echo '<p class="description">' . esc_html__( 'Enter meta keys to exclude from display, separated by commas. Be aware, all other meta keys will be exposed via the plugin\'s REST API endpoint.', 'revelio' ) . '</p>';
}

/**
 * Render the settings page HTML.
 *
 * @since 1.0.0
 */
function revelio_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Revelio', 'revelio' ); ?></h1>
		<p>
			<?php esc_html_e( 'Welcome to Revelio! Use the settings below to control which user roles can view post meta and which meta keys should be excluded from display. If there\'s something missing or you\'d like to suggest a feature, feel free to leave a request in the plugin reviews on the WordPress.org repository — I\'m always listening!', 'revelio' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'revelio_settings_group' );
			do_settings_sections( 'revelio-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
