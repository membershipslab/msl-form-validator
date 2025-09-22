<?php
/**
 * Plugin Name: HTML Form Validator for PMPro User Fields.
 * Plugin URI: https://membershipslab.com/plugins/msl-form-validator/
 * Description: Add HTML form validation for PMPro custom user fields.
 * Version: 0.1.1
 * Author: Memberships Lab
 * Author URI: https://membershipslab.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: msl-form-validator
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 *
 * @package MSL_Form_Validator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include plugin functions to check dependencies.
if ( is_admin() ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Prevent activation if Paid Memberships Pro is not active.
 */
function msl_form_validator_activation_check() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( ! is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ) {
		// Deactivate self and show a message.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			sprintf(
				/* translators: 1: opening strong tag, 2: closing strong tag */
				esc_html__( '%1$sHTML Form Validator for PMPro%2$s requires the Paid Memberships Pro plugin to be installed and active.', 'msl-form-validator' ),
				'<strong>',
				'</strong>'
			) .
			' ' .
			sprintf(
				/* translators: 1: opening anchor tag, 2: closing anchor tag */
				esc_html__( 'Please %1$sinstall/activate Paid Memberships Pro%2$s and try again.', 'msl-form-validator' ),
				'<a href="' . esc_url( admin_url( 'plugin-install.php?s=paid%20memberships%20pro&tab=search&type=term' ) ) . '">',
				'</a>'
			),
			esc_html__( 'Dependency missing', 'msl-form-validator' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( __FILE__, 'msl_form_validator_activation_check' );

/**
 * Show admin notice if PMPro is not active (for older sites without Requires Plugins handling).
 */
function msl_form_validator_admin_dependency_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ) {
		return;
	}
	// Respect per-user dismissal of this notice.
	if ( get_user_meta( get_current_user_id(), 'msl_fv_dismiss_pmpro_notice', true ) ) {
		return;
	}

	// Build a nonce-protected dismiss URL.
	$dismiss_url = wp_nonce_url( add_query_arg( 'msl_fv_dismiss_pmpro_notice', '1' ), 'msl_fv_dismiss_pmpro_notice' );

	echo '<div class="notice notice-error is-dismissible"><p>' .
		sprintf(
			/* translators: 1: opening strong tag, 2: closing strong tag */
			esc_html__( '%1$sHTML Form Validator for PMPro%2$s requires the Paid Memberships Pro plugin to be installed and active.', 'msl-form-validator' ),
			'<strong>',
			'</strong>'
		) .
		' ' .
		'<a href="' . esc_url( admin_url( 'plugin-install.php?s=paid%20memberships%20pro&tab=search&type=term' ) ) . '">' . esc_html__( 'Install PMPro', 'msl-form-validator' ) . '</a>' .
		' &middot; ' .
		'<a href="' . esc_url( $dismiss_url ) . '" class="button-link">' . esc_html__( 'Dismiss', 'msl-form-validator' ) . '</a>' .
		'</p></div>';
}

add_action( 'admin_notices', 'msl_form_validator_admin_dependency_notice' );

/**
 * Handle dismissal of the PMPro dependency admin notice.
 */
function msl_form_validator_handle_dismiss_notice() {
	// Only proceed for users who can activate plugins.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	// Check for dismissal request and verify nonce.
	if ( isset( $_GET['msl_fv_dismiss_pmpro_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'msl_fv_dismiss_pmpro_notice' );
		update_user_meta( get_current_user_id(), 'msl_fv_dismiss_pmpro_notice', 1 );
		// Redirect to remove query args.
		wp_safe_redirect( remove_query_arg( array( 'msl_fv_dismiss_pmpro_notice', '_wpnonce' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'msl_form_validator_handle_dismiss_notice' );

/**
 * Load plugin textdomain for translations.
 */
function msl_form_validator_load_textdomain() {
	load_plugin_textdomain( 'msl-form-validator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'msl_form_validator_load_textdomain' );

/**
 * Add HTML5 validation attributes to required PMPro custom user fields on the checkout page.
 *
 * - Adds required and aria-required attributes.
 * - Sets a custom browser validation message via oninvalid and clears it via oninput.
 * - Allows per-field customization of the message via the 'msl_pmpro_required_field_message' filter.
 *
 * @param PMPro_Field $field The PMPro field object.
 * @param string      $where The screen/context where the field is being added (e.g. checkout, profile, etc.).
 *
 * @return PMPro_Field with required HTML attributes added conditionally.
 */
function msl_pmpro_add_user_field_required_attribute( $field, $where ) {
	// Only act on required fields.
	if ( ! empty( $field->required ) ) {
		// Ensure html_attributes is an array we can work with.
		if ( empty( $field->html_attributes ) || ! is_array( $field->html_attributes ) ) {
			$field->html_attributes = array();
		}

		// Base attributes for native validation and accessibility.
		$field->html_attributes['required']      = 'required';
		$field->html_attributes['aria-required'] = 'true';

		// Build a default, translatable message TEMPLATE. We'll sprintf the label later.
		$field_label = ! empty( $field->label ) ? $field->label : __( 'this field', 'msl-form-validator' );
		// Ensure the label is safe/plain text for interpolation into JS strings.
		$field_label = wp_strip_all_tags( (string) $field_label );
		/* translators: %s: field label */
		$default_template = __( 'Please fill out the %s required field.', 'msl-form-validator' );

		/**
		 * Filter the browser validation message shown for a required PMPro field.
		 *
		 * Use this to customize messages globally or per field name.
		 *
		 * @param string      $message    The message to show when the field is invalid.
		 * @param string|null $field_name The field name/key (if available).
		 * @param PMPro_Field $field      The PMPro field object.
		 * @param string      $where      The screen/context where the field is rendered.
		 */
		$template = apply_filters(
			'msl_pmpro_required_field_message',
			$default_template,
			isset( $field->name ) ? $field->name : null,
			$field,
			$where
		);

		// Interpolate the field label into the template.
		$message = sprintf( $template, $field_label );

		// Set and clear the custom validity message using inline handlers.
		$field->html_attributes['oninvalid'] = "this.setCustomValidity('" . esc_js( $message ) . "')";
		$field->html_attributes['oninput']   = "this.setCustomValidity('')";
	}
	return $field;
}
add_filter( 'pmpro_add_user_field', 'msl_pmpro_add_user_field_required_attribute', 10, 2 );

/**
 * Check that required fields on the Member Profile Edit page were filled in.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 * @param array      $errors Array of error messages to be shown.
 * @param mixed|null $update Whether this is a user update.
 * @param WP_User    $user   The user object being edited.
 */
function msl_pmpro_check_required_profile_fields( &$errors, $update = null, &$user = null ) {

	global $pmpro_user_fields;

	$default_required_fields = array(
		'first_name'   => array( __( 'First Name', 'msl-form-validator' ) ),
		'last_name'    => array( __( 'Last Name', 'msl-form-validator' ) ),
		'display_name' => array( __( 'Display Name', 'msl-form-validator' ) ),
		'user_email'   => array( __( 'Email', 'msl-form-validator' ) ),
	);

	$required_user_fields = array();

	// Get the fields to check.
	if ( ! empty( $pmpro_user_fields ) && is_array( $pmpro_user_fields ) ) {
		foreach ( $pmpro_user_fields as $field_group => $fields ) {
			foreach ( $fields as $field ) {
				if ( $field->profile && 'only_admin' !== $field->profile && $field->required ) {
					$required_user_fields[ $field->name ] = array( $field->label, $field->levels );
				}
			}
		}
	}

	$user_levels = pmpro_getMembershipLevelsForUser( $user->ID );

	// Remove fields that are not required for the user's level.
	foreach ( $required_user_fields as $field_name => $field ) {
		if ( ! empty( $field[1] ) ) {
			$required = false;
			foreach ( $user_levels as $level ) {
				if ( in_array( $level->id, $field[1], true ) ) {
					$required = true;
				}
			}
			if ( ! $required ) {
				unset( $required_user_fields[ $field_name ] );
			}
		}
	}

	// Merge the default fields with the required fields.
	$required_user_fields = array_merge( $default_required_fields, $required_user_fields );

	// Add an error message for required fields that are empty.
	foreach ( $required_user_fields as $field_name => $field ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- PMPro handles nonce on the profile form; this plugin only reads values for validation.
		$request_value_raw = isset( $_REQUEST[ $field_name ] ) ? $_REQUEST[ $field_name ] : '';
		$request_value     = is_string( $request_value_raw ) ? sanitize_text_field( wp_unslash( $request_value_raw ) ) : '';
		if ( empty( $user->{$field_name} ) || empty( $request_value ) ) {
			// Base default comes from the same filter used for checkout/browser validation.
			// We pass a sensible default template here and indicate the context as 'profile-edit'.
			$base_default = apply_filters(
				'msl_pmpro_required_field_message',
				/* translators: %s: field label */
				__( 'Please fill out the %s required field.', 'msl-form-validator' ),
				$field_name,
				null,
				'profile-edit'
			);

			/**
			 * Filter the error message template for required fields on the Member Profile Edit screen.
			 *
			 * This filter receives the base default message template as already processed by
			 * the 'msl_pmpro_required_field_message' filter, ensuring consistent defaults
			 * across checkout/browser validation and profile edit validation. Use this hook
			 * to customize or localize messages specifically for the profile edit context
			 * and/or per field name.

			 * Important: The returned value should be a template string containing a single
			 * "%s" placeholder for the human-readable field label (e.g. "First Name").
			 *
			 * @since 0.0.3alpha
			 *
			 * @param string       $template   The message template to use (with a "%s" placeholder for the label).
			 * @param string       $field_name The field key/name being validated.
			 * @param array        $field      Field details array in the form [ label, levels ].
			 * @param WP_User|null $user       The user object being edited, if available.
			 *
			 * @return string Filtered message template used to build the final error message.
			 */
			$template = apply_filters( 'msl_pmpro_profile_edit_error_message', $base_default, $field_name, $field, $user );
			$message  = sprintf( $template, esc_html( $field[0] ) );
			// Ensure the error message is plain text.
			$message  = wp_strip_all_tags( $message );
			$errors[] = $message;
		}
	}
}
add_action( 'pmpro_user_profile_update_errors', 'msl_pmpro_check_required_profile_fields', 10, 3 );
