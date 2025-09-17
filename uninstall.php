<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// This plugin stores no options or custom tables.
// Nothing to clean up on uninstall at this time.
