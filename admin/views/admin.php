<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Ifttt_Bridge_Admin
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div>
		<h3><?php _ex( 'Logging', 'Heading', $this->plugin_slug ) ?></h3>
		<p class="description"><?php _e( 'Logging is recommended when you setup a new process based on the IFTTT Bridge for WordPress. In the field below you can see helpful information about how the IFTTT request is processed.', $this->plugin_slug ); ?></p>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'ifttt_bridge_options_group' );
			do_settings_sections( 'ifttt_bridge_options_group' );
		?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="ifttt_bridge_options_log_level"><?php _ex( 'Log level', 'Form label', $this->plugin_slug ); ?></label></th>
						<td>
							<select name="ifttt_bridge_options[log_level]" id="ifttt_bridge_options_log_level">
								<?php foreach ( array( 'off', 'error', 'warn', 'info', 'debug' ) as $level ) { ?>
									<option value="<?php echo $level; ?>" <?php selected( $this->log_level, $level ); ?>><?php echo $level; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<div style="margin-bottom: 40px;">
		<h3><?php _ex( 'Log (max. 30 entries)', 'Heading', $this->plugin_slug ) ?></h3>
		<?php if ( count( $this->log_entries ) > 0 ) : ?>
		<table class="widefat log-messages">
			<thead>
				<tr>
					<th>Time</th>
					<th>Level</th>
					<th>Message</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $this->log_entries as $log_entry ) { ?>
				<tr>
					<td><pre><?php echo $log_entry['time'] ?></pre></td>
					<td><pre><?php echo $log_entry['level'] ?></pre></td>
					<td><pre><?php echo htmlspecialchars( $log_entry['message'] ) ?></pre></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php else : ?>
		<p class="description no-log-messages"><?php _ex( 'No entries found', 'Message instead of log table', $this->plugin_slug ); ?></p>
		<?php endif ?>
	</div>
	<div>
		<h3><?php _ex( 'Send test request', 'Heading', $this->plugin_slug ) ?></h3>
		<p class="description"><?php _e( 'Send a test request if you want to make sure that your WordPress installation is ready for IFTTT. Use the form below to send a request which is identical to the ones sent by IFTTT.', $this->plugin_slug ) ?></p>
		<form action="admin-post.php" method="post">
		  <input type="hidden" name="action" value="sent_post_request">
		  <input type="hidden" name="redirect_url" value="sent_post_request">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="test-request-username"><?php _ex( 'Username', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input type="text" class="regular-text" id="test-request-username" name="test-request-username"></td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-password"><?php _ex( 'Password', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input type="password" class="regular-text" id="test-request-password" name="test-request-password"></td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-title"><?php _ex( 'Title', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input type="text" class="regular-text" id="test-request-title" name="test-request-title"></td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-body"><?php _ex( 'Body', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><textarea style="width: 25em; height: 5em;" id="test-request-body" name="test-request-body"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-categories"><?php _ex( 'Categories', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input type="text" class="regular-text" id="test-request-categories" name="test-request-categories">
						<p class="description"><?php _ex( 'Comma-separated list', 'Test request form description', $this->plugin_slug ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-tags"><?php _ex( 'Tags', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input type="text" class="regular-text" id="test-request-tags" name="test-request-tags">
						<p class="description"><?php _ex( "Comma-separated list. The tag 'ifttt_bridge' will be used automatically.", 'Test request form description', $this->plugin_slug ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="test-request-draft"><?php _ex( 'Draft', 'Test request form label', $this->plugin_slug ); ?></label></th>
						<td><input name="test-request-draft" type="checkbox" id="test-request-draft" value="1" /></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( _x( 'Send request', 'Button label', $this->plugin_slug ), 'primary', 'send-test-request' ); ?>
		</form>
	</div>
</div>
