<?php 
if(!defined('ABSPATH')) {
	exit;
}

function bppaw_activity_wall_admin() {

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bppaw_activity_wall_admin') ) {
	
		$setting_options = array(
			'remove_personal_tab'		=> esc_attr__( 'Disable Personal Tab', 'bp-profile-activity-wall' ),
			'remove_favorites_tab'		=> esc_attr__( 'Disable Favorites Tab', 'bp-profile-activity-wall' )
		);
		
		if ( bp_is_active( 'groups' ) ) {
			$setting_options['remove_groups_tab'] = esc_attr__( 'Disable Groups Tab', 'bp-profile-activity-wall' );
		}

		if ( bp_is_active( 'friends' ) ) {
			$setting_options['remove_friends_tab'] = esc_attr__( 'Disable Friends Tab', 'bp-profile-activity-wall' );
		}

		if ( bp_activity_do_mentions() ) {
			$setting_options['remove_mentions_tab'] = esc_attr__( 'Disable Mentions Tab', 'bp-profile-activity-wall' );
		}

		if ( class_exists( 'BP_Follow_Component' ) ) {
			$setting_options['remove_following_tab'] = esc_attr__( 'Disable Following Tab', 'bp-profile-activity-wall' );
		}

		$new_settings = array();
		
		foreach ( $setting_options as $setting_id => $setting_name ) {
			
			$new_settings[$setting_id] = isset( $_POST[ 'bppaw_' . $setting_id ] ) ? sanitize_text_field( $_POST[ 'bppaw_' . $setting_id ] ) : 0;
			
		}
		
		$new_settings['home_redirect'] = isset( $_POST[ 'bppaw_home_redirect' ] ) ? sanitize_text_field( $_POST[ 'bppaw_home_redirect' ] ) : 0;
		
		$new_settings['login_redirect'] = isset( $_POST[ 'bppaw_login_redirect' ] ) ? sanitize_text_field( $_POST[ 'bppaw_login_redirect' ] ) : 0;
		
		$new_settings['user_control'] = isset( $_POST[ 'bppaw_user_control' ] ) ? sanitize_text_field( $_POST[ 'bppaw_user_control' ] ) : 0;
		
		update_option( 'bppaw_settings', $new_settings );
		
		$updated = true;
	}
	
	$data = maybe_unserialize( get_option( 'bppaw_settings') );
	

	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bppaw-settings' ) : admin_url( 'admin.php?page=bppaw-settings' );
	
	$setting_options = array(
		'remove_personal_tab'		=> esc_attr__( 'Disable Personal Tab', 'bp-profile-activity-wall' ),
		'remove_favorites_tab'		=> esc_attr__( 'Disable Favorites Tab', 'bp-profile-activity-wall' )
	);
	
	if ( bp_is_active( 'groups' ) ) {
		$setting_options['remove_groups_tab'] = esc_attr__( 'Disable Groups Tab', 'bp-profile-activity-wall' );
	}

	if ( bp_is_active( 'friends' ) ) {
		$setting_options['remove_friends_tab'] = esc_attr__( 'Disable Friends Tab', 'bp-profile-activity-wall' );
	}

	if ( bp_activity_do_mentions() ) {
		$setting_options['remove_mentions_tab'] = esc_attr__( 'Disable Mentions Tab', 'bp-profile-activity-wall' );
	}

	if ( class_exists( 'BP_Follow_Component' ) ) {
		$setting_options['remove_following_tab'] = esc_attr__( 'Disable Following Tab', 'bp-profile-activity-wall' );
	}

	?>
	<div class="wrap">
		<h2><?php echo esc_attr__( 'Profile Activity Wall Settings', 'bp-profile-activity-wall' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . esc_attr__( 'Settings Updated.', 'bp-profile-activity-wall' ) . "</p></div>"; endif; ?>

		<form action="<?php echo esc_attr($url_base) ?>" name="bp-profile-activity-wall-settings-form" id="bp-profile-activity-wall-settings-form" method="post">			

			<h2><?php esc_attr_e( 'Profile Activity Tabs', 'bp-profile-activity-wall' ); ?></h2>

			<div>
				<?php foreach ( $setting_options as $setting_id => $setting_title ) : ?>
				
						<?php $setting = isset( $data[$setting_id] ) ? $data[$setting_id] : 0; ?>  
						
						<input <?php if ( $setting == 1 ) { echo 'checked'; } ?> type="checkbox" name="bppaw_<?php echo esc_attr($setting_id); ?>" id="bppaw_<?php echo esc_attr($setting_id); ?>" value="1" />
						
						<label for="bppaw_<?php echo esc_attr($setting_id); ?>"><?php echo esc_attr($setting_title); ?></label><br>
						
				<?php endforeach; ?>
				
			</div>
			
			<h2><?php esc_attr_e( 'Redirection Options', 'bp-profile-activity-wall' ); ?></h2>

			<div>

				<?php $home_redirect_setting = isset( $data['home_redirect'] ) ? $data['home_redirect'] : 0; ?>  
				
				<input <?php if ( $home_redirect_setting == 1 ) { echo 'checked'; } ?> type="checkbox" name="bppaw_home_redirect" id="bppaw_home_redirect" value="1" />
				
				<label for="bppaw_home_redirect"><?php esc_attr_e( 'Redirect Home page to Profile Activity page for logged in users', 'bp-profile-activity-wall' ) ?></label><br>
			
			</div>

			<div>

				<?php $login_redirect_setting = isset( $data['login_redirect'] ) ? $data['login_redirect'] : 0; ?>  
				
				<input <?php if ( $login_redirect_setting == 1 ) { echo 'checked'; } ?> type="checkbox" name="bppaw_login_redirect" id="bppaw_login_redirect" value="1" />
				
				<label for="bppaw_login_redirect"><?php esc_attr_e( 'Redirect after login to Profile Activity page.', 'bp-profile-activity-wall' ) ?></label><br>
			
			</div>

			<h2><?php esc_attr_e( 'User Control', 'bp-profile-activity-wall' ); ?></h2>

			<div>

				<?php $user_control_setting = isset( $data['user_control'] ) ? $data['user_control'] : 0; ?>  
				
				<input <?php if ( $user_control_setting == 1 ) { echo 'checked'; } ?> type="checkbox" name="bppaw_user_control" id="bppaw_user_control" value="1" />
				
				<label for="bppaw_user_control"><?php esc_attr_e( 'Enable user control of All Activity page.', 'bp-profile-activity-wall' ) ?></label><br>
			
			</div>

			<?php wp_nonce_field( 'bppaw_activity_wall_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
			
		</form>
		
	</div>
<?php
}

?>
