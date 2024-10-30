<?php
/**
 * Plugin Name: BP Profile Activity Wall
 * Plugin URI: http://buddyuser.com/plugin-bp-profile-activity-wall
 * Author URI: http://buddyuser.com/
 * Description: Adds a new default profile activity tab of All which combines all users activity feeds into one, just like Facebook.
 * Author: Venutius
 * Text Domain: bp-profile-activity-wall
 * Domain Path: /languages
 * Version: 1.0.0
 * License: http://www.gnu.org/copyleft/gpl.html
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bppaw_check_buddypress() {
    
	if ( ! class_exists( 'buddypress' ) ) {
        
		add_action( 'admin_notices', 'bppaw_no_bp_admin_notice' );
    
		return;
	
	}

}

add_action( 'plugins_loaded', 'bppaw_check_buddypress' );


function bppaw_no_bp_admin_notice() {
    ?>

    <div class="error fade notice-error6 is-dismissible">

		<p><?php esc_textarea(_e( 'The BuddyPress needs to be active for BP Profile Activity Wall to work.', 'bp-profile-activity-wall' ) ); ?></p>
    
	</div>

	<?php
	return;
}



function bppaw_load_files() {

	if ( class_exists( 'buddypress' ) && bp_is_active( 'activity' ) ) {
		
		include_once("inc/bp-profile-activity-wall.php");
		
	}

}

add_action( 'bp_setup_nav', 'bppaw_load_files' );

if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-profile-activity-wall', dirname( __FILE__ ) . '/bp-profile-activity-wall/languages/' . get_locale() . '.mo' );
