<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Pilau_Repair_Meta
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2013 Public Life
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
