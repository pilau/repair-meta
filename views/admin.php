<?php

/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Pilau_Repair_Meta
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2013 Public Life
 */

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( isset( $_GET['done'] ) ) { ?>
		<div class="updated"><p><strong><?php _e( 'Settings updated successfully.' ); ?></strong></p></div>
	<?php } ?>

	<form method="post" action="">

		<?php wp_nonce_field( $this->plugin_slug . '_settings', $this->plugin_slug . '_settings_admin_nonce' ); ?>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-field'; ?>"><?php _e( 'Sample field' ); ?></label></th>
					<td><input type="text" name="ga-id" id="<?php echo $this->plugin_slug . '-field'; ?>" value="<?php esc_attr_e( $this->settings['field'] ); ?>" class="regular-text"></td>
				</tr>
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save settings"></p>

	</form>

</div>
