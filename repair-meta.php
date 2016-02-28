<?php

/**
 * Pilau Repair Meta
 *
 * @package   Pilau_Repair_Meta
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2016 Steve Taylor
 *
 * @wordpress-plugin
 * Plugin Name:			Pilau Repair Meta
 * Description:			Repair corrupted serialized data in metadata.
 * Version:				0.1
 * Author:				Steve Taylor
 * Text Domain:			repair-meta-locale
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:			/lang
 * GitHub Plugin URI:	https://github.com/pilau/repair-meta
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-repair-meta.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Pilau_Repair_Meta', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Pilau_Repair_Meta', 'deactivate' ) );

Pilau_Repair_Meta::get_instance();
