=== HTML Form Validator for PMPro ===
Contributors: membershipslab, ipokkel
Tags: paid-memberships-pro, pmpro, user fields, form validation, required fields
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Requires Plugins: paid-memberships-pro
Stable tag: 0.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add native HTML5 validation to Paid Memberships Pro custom user fields at checkout and profile edit screens.

== Description ==

HTML Form Validator for PMPro adds native browser validation (required, accessible ARIA, and helpful messages) to Paid Memberships Pro custom user fields. This reduces friction at checkout by letting members fix missing fields instantly without a full page refresh.

Features:
- Adds `required` and `aria-required` attributes to required PMPro fields.
- Provides a clear, translatable validation message per field.
- Filterable message template for full control of wording per field/context.
- Works on the PMPro checkout and member profile edit screens.
- Fully internationalized with a `.pot` file and several translations included.

Works best with:
- Paid Memberships Pro (required)

= Why native HTML5 validation? =
Native browser validation is fast and accessible, giving your members immediate feedback before a form submit. It pairs well with server-side validation for the best UX.

= Developer Notes =
- Text domain: `msl-form-validator`
- Translation files in `/languages`
- Filters:
  - `msl_pmpro_required_field_message` — customize the checkout/browser validation message template
  - `msl_pmpro_profile_edit_error_message` — customize the profile edit error message template

== Installation ==

1. Install and activate Paid Memberships Pro.
2. Upload the plugin files to `/wp-content/plugins/msl-form-validator/` or install via the Plugins screen.
3. Activate “HTML Form Validator for PMPro” through the “Plugins” screen in WordPress.

== Frequently Asked Questions ==

= Can I customize the validation message? =
Yes. Use the `msl_pmpro_required_field_message` filter to change the template globally or per field:

Example:
- Return a template string containing a single “%s” placeholder for the field label.
- Example default: "Please fill out the %s required field."

= Does this replace server-side validation? =
No. It augments it. You should still keep your server-side checks.

= Does it work on the member profile edit screen? =
Yes. It validates required fields and shows an admin-facing error if a required field is empty.

== Screenshots ==

1. Example browser validation message at checkout.
2. Required PMPro custom user field with native HTML5 attributes.
3. Member Profile Edit shows a clear error when a required field is missing.
4. Custom validation message via filter.
5. Localized validation message.

== Contribute ==

Development, issue tracking, and pull requests are on GitHub:
https://github.com/membershipslab/msl-form-validator

== Changelog ==

= 0.1.2 =
- Address Plugin Check compliance warnings.
- Remove discouraged load_plugin_textdomain usage (WP 4.6+).
- Align plugin header name with readme title.
- Sanitize and unslash request input for profile edit validation.
- Documentation/readme tweaks.

= 0.1.1 =
- Bump plugin header version to 0.1.1.
- Update translations and .pot file.
- Improve dependency notice UX with dismiss and nonce handling.
- Confirm compatibility with WordPress 6.8.
- Documentation/readme tweaks.

= 0.1.0 =
- Initial public release.
- Adds HTML5 required attributes and validation messages for PMPro custom fields.
- Adds profile edit required field checks with filterable messages.

== Upgrade Notice ==

= 0.1.2 =
Compliance fixes for WordPress Plugin Check. No breaking changes.

= 0.1.1 =
Minor improvements and translation updates. No breaking changes.

= 0.1.0 =
Initial public release with HTML5 validation and profile edit checks.
