# HTML Form Validator for PMPro

Add native HTML5 validation and consistent, localized messages to Paid Memberships Pro (PMPro) custom user fields.

Important: This plugin targets PMPro Custom User Fields only. It does not alter or validate PMPro’s default checkout fields like username, email, password, etc.

## What it does

- Adds HTML5 validation attributes to required PMPro custom user fields on checkout:
  - `required` and `aria-required="true"`
  - Custom browser message via `oninvalid` and cleared on `oninput`
- Validates required custom user fields on the PMPro Member Profile Edit screen and returns a plain-text error message if empty.
- Provides filters to customize the validation message globally, per field, and per context.
- Fully translatable (text domain: `msl-form-validator`).

## Scope and limitations

- Applies only to PMPro Custom User Fields (created via `pmpro_add_user_field` or by PMPro Add Ons that use the same API).
- Does not change validation for core PMPro fields such as username, email, password, etc.

## Installation

1. Copy or install the plugin into:
   `wp-content/plugins/msl-form-validator/`
2. Activate the plugin from WordPress Admin → Plugins.
3. If you use translations, place your MO/PO files in:
   `wp-content/plugins/msl-form-validator/languages/`

## Usage

- Mark your PMPro custom user fields as required using the PMPro field API. This plugin will:
  - Add native HTML5 validation on the checkout form.
  - Validate required custom fields on the Member Profile Edit page and surface error messages.
- Default messages are provided, but you can customize them via the filters below.

## Filters

### 1) msl_pmpro_required_field_message

Filter the browser validation message shown for a required PMPro custom field (e.g., on checkout). Also used as the base default for the profile edit message.

Signature:

```php
apply_filters(
    'msl_pmpro_required_field_message',
    string $message_template,
    ?string $field_name,
    PMPro_Field $field,
    string $where
)
```

- `$message_template` Default template with a single "%s" placeholder for the human-readable label.
- `$field_name` The field key (may be null if not available).
- `$field` The `PMPro_Field` object.
- `$where` Context, e.g., `checkout`, `profile-edit`.

Example (from the plugin, simplified for docs):

```php
add_filter( 'msl_pmpro_required_field_message', function( $message, $field_name, $field, $where ) {
    /* translators: %s: field label */
    return __( 'Kindly complete the required %s field.', 'msl-form-validator' );
}, 10, 4 );
```

### 2) msl_pmpro_profile_edit_error_message

Filter the error message template for required custom user fields on the Member Profile Edit screen. Receives the base default from `msl_pmpro_required_field_message`, allowing you to tailor messages specifically for profile edit.

Signature:

```php
apply_filters(
    'msl_pmpro_profile_edit_error_message',
    string $template,
    string $field_name,
    array $field,     // [ label, levels ]
    ?WP_User $user
)
```

- `$template` Message template with a single "%s" placeholder for the human-readable label.
- `$field_name` The field key/name being validated.
- `$field` An array with two items: `[ label, levels ]`.
- `$user` The `WP_User` being updated (if available).

Example (based on the plugin’s example snippet):

```php
add_filter( 'msl_pmpro_profile_edit_error_message', function( $template, $field_name, $field, $user ) {
    /* translators: %s: field label */
    return __( 'You forgot to fill in the required %s field.', 'msl-form-validator' );
}, 10, 4 );
```

## Internationalization (i18n)

- Text domain: `msl-form-validator`
- Languages directory: `wp-content/plugins/msl-form-validator/languages/`
- The plugin loads translations on `plugins_loaded` using `load_plugin_textdomain()`.
- A POT file is provided: `languages/msl-form-validator.pot`

To add translations:

1. Create a PO from the POT for your locale (e.g., `msl-form-validator-af_ZA.po`).
2. Compile to MO (`msl-form-validator-af_ZA.mo`).
3. Place both in the `languages/` directory.

## Notes and best practices

- Always include translator comments for strings with placeholders, e.g., `/* translators: %s: field label */`.
- Prefer simple, clear, and concise messages for better UX and translation quality.
- For per-field customization, inspect `$field_name` inside your filter callback and return different templates based on the field key.

Example per-field logic:

```php
add_filter( 'msl_pmpro_required_field_message', function( $message, $field_name, $field, $where ) {
    if ( $field_name === 'company_name' ) {
        /* translators: %s: field label */
        return __( 'Please provide your %s to continue.', 'msl-form-validator' );
    }
    return $message; // fall back to the default/base message
}, 10, 4 );
```
