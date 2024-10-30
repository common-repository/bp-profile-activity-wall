<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bppaw_set_member_default_nav() {
 
		if ( ! bp_is_user_profile() && ! bp_is_user_activity() && ! bp_is_user() ) {
		    return;
	         }

                bp_core_new_nav_default(
		array(
			'parent_slug'       => buddypress()->activity->id,
		// other "activity" sub_nav slugs : personal favorites friends groups mentons
			'subnav_slug'       => 'all-activity',
			'screen_function'   => 'bp_activity_screen_all_activity'
		)
	);
}
add_action( 'bp_setup_nav', 'bppaw_set_member_default_nav', 20 );

add_action( 'bp_setup_nav', 'bppaw_just_me_tab', 50 );
add_action( 'bp_setup_admin_bar', 'bppaw_admin_bar_add', 50 );

function bppaw_just_me_tab() {
	
	if ( ! bp_is_user_profile() && ! bp_is_user_activity() && ! bp_is_user() ) {
		return;
	}
	
	global $bp;
	$user_id = bp_displayed_user_id();
	
		bp_core_new_subnav_item( array(
			'name'            => sanitize_text_field( __( 'All', 'bp-profile-activity-wall' ) ),
			'slug'            => 'all-activity',
			'parent_url'      => bp_core_get_user_domain( $user_id ) . 'activity/',
			'parent_slug'     => 'activity',
			'screen_function' => 'bp_activity_screen_all_activity',
			'position'        => 10
		) );

}

function bppaw_admin_bar_add() {
	
	global $wp_admin_bar, $bp;

	if ( ! bp_use_wp_admin_bar() || defined( 'DOING_AJAX' ) ) {
		return false;
	}

	$user_id = get_current_user_id();
	
	if ( ! $user_id || $user_id == 0 || ! is_numeric( $user_id ) ) {
		return;
	}
	
	// Personal.
	//$wp_admin_bar->remove_menu( 'my-account-activity-personal', 'my-account-activity' );

	$wp_admin_bar->add_menu( array(
		'parent'   => 'my-account-activity',
		'id'       => 'my-account-activity-all-activity',
		'title'    => sanitize_text_field( __( 'All', 'bp-profile-activity-wall' ) ),
		'href'     => bp_core_get_user_domain( $user_id ) . 'activity/all-activity/',
		'position' => 10
	) );
}
function bp_activity_screen_all_activity() {

	do_action( 'bp_activity_screen_all_activity' );

	bp_core_load_template( apply_filters( 'bp_activity_template_all_activity', 'members/single/home' ) );
}

function bp_activity_filter_all_activity_scope( $retval = array(), $filter = array() ) {

	// Determine the user_id.
	if ( ! empty( $filter['user_id'] ) ) {
		$user_id = $filter['user_id'];
	} else {
		$user_id = bp_displayed_user_id()
			? bp_displayed_user_id()
			: bp_loggedin_user_id();
	}

	// Should we show all items regardless of sitewide visibility?
	$show_hidden = array();
	if ( ! empty( $user_id ) && $user_id !== bp_loggedin_user_id() ) {
		$show_hidden = array(
			'column' => 'hide_sitewide',
			'value'  => 0
		);
	}

	// Determine groups of user.
	if ( bp_is_active( 'groups' ) ) {
		$groups = groups_get_user_groups( $user_id );
		if ( empty( $groups['groups'] ) ) {
			$groups = array( 'groups' => 0 );
		}
	}

	// Determine the favorites.
	$favs = bp_activity_get_user_favorites( $user_id );
	if ( empty( $favs ) ) {
		$favs = array( 0 );
	}

	// Determine friends of user.
	if ( bp_is_active( 'friends' ) ) {
		$friends = friends_get_friend_user_ids( $user_id );
		if ( empty( $friends ) ) {
			$friends = array( 0 );
		}
	}

	// Determine who the user is following
	if ( class_exists( 'BP_Follow_Component' ) ) {
		$following_ids = bp_get_following_ids( array( 'user_id' => $user_id ) );
		$following_ids = empty( $following_ids ) ? array() : $following_ids;
	}
	
	$retval = array(
		'relation' => 'OR',
		array(
			'relation' => 'AND',
			array(
				'column' => 'user_id',
				'value'  => $user_id
			)
		),
		array(
			'relation' => 'AND',
			array(
				'column'  => 'id',
				'compare' => 'IN',
				'value'   => (array) $favs
			)
		),
		array(
			'relation' => 'AND',
			array(
				'column'  => 'content',
				'compare' => 'LIKE',

				// Start search at @ symbol and stop search at closing tag delimiter.
				'value'   => '@' . bp_activity_get_user_mentionname( $user_id ) . '<'
			)
		),
		// We should only be able to view sitewide activity content for friends.
		array(
			'column' => 'hide_sitewide',
			'value'  => 0
		),

		// Overrides.
		'override' => array(
			'filter'      => array( 'user_id' => 0 ),
			'show_hidden' => true
		),
	);
	
	if ( bp_is_active( 'friends' ) && class_exists( 'BP_Follow_Component' ) ) {
		$merged_ids = array_merge( $friends, $following_ids );
		$retval[] = array(
			'relation' => 'AND',
			array(
				'column'  => 'user_id',
				'compare' => 'IN',
				'value'   => (array) $merged_ids
			)
		);
	} else if ( bp_is_active( 'friends' ) ) {
		$retval[] = array(
			'relation' => 'AND',
			array(
				'column'  => 'user_id',
				'compare' => 'IN',
				'value'   => (array) $friends
			)
		);
		
	} else if ( class_exists( 'BP_Follow_Component' ) ) {
		$retval[] = array(
			'relation' => 'AND',
			array(
				'column'  => 'user_id',
				'compare' => 'IN',
				'value'   => (array) $following_ids
			)
		);
		
	}

	if ( bp_is_active( 'groups' ) ) {
		$retval[] = array(
			'relation' => 'AND',
			array(
				'column' => 'component',
				'value'  => buddypress()->groups->id
			),
			array(
				'column'  => 'item_id',
				'compare' => 'IN',
				'value'   => (array) $groups['groups']
			)
		);
		
	}

	return $retval;
}
add_filter( 'bp_activity_set_all-activity_scope_args', 'bp_activity_filter_all_activity_scope', 10, 2 );

function bppaw_load_mention_me() {
	
	$currentpage = $_SERVER['REQUEST_URI'];
	
	if ( strpos( $currentpage, 'all-activity' ) !== false ) {
		if ( get_current_user_id() != bp_displayed_user_id() ) {
			$_GET['r'] = bp_activity_get_user_mentionname( bp_displayed_user_id() );	
		}
		bp_get_template_part( 'activity/post-form' );
	}
}

add_action( 'bp_before_member_body', 'bppaw_load_mention_me' );