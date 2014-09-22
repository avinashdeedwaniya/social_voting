<?php

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'sv_install');
function sv_install(){
	if (!is_page('sv_redirect')):
	// Create post object
	$my_post = array(
	'post_title' => 'Social Voting Redirect Page',
	'post_content' => '[sv_redirect]',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_name' => 'sv_redirect',
	'post_type' => 'page'
	);
	
	// Insert the post into the database
	wp_insert_post($my_post);
	endif;
	if (!is_page('sv_callback')):
	$my_post = array(
	'post_title' => 'Social Voting Callback Page',
	'post_content' => '[sv_callback]',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_name' => 'sv_callback',
	'post_type' => 'page'
	);
	
	// Insert the post into the database
	wp_insert_post($my_post);
	endif;
	if (!is_page('sv_email_voting_varify')):
		$my_post = array(
		'post_title' => 'Social Voting Email Varification',
		'post_content' => '[sv_email_voting_varify]',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_name' => 'sv_email_voting_varify',
		'post_type' => 'page'
		);
		
		// Insert the post into the database
		wp_insert_post($my_post);
		/*     * **********END****************************** */
	endif;
}
add_action('init', 'sv_init');

function sv_init(){

     $labels = array(
    'name'               => _x( 'Social Voting', 'post type general name' ),
    'singular_name'      => _x( 'Social Voting', 'post type singular name' ),
   
    'all_items'          => __( 'All Social Voting' ),

    'search_items'       => __( 'Search Social Voting' ),
    'not_found'          => __( 'No Social Voting found' ),
    'not_found_in_trash' => __( 'No Social Voting found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Social Voting'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Holds our Social Voting',
    'public'        => true,
    'menu_position' => 5,
    'supports'      => array( 'title'),
    'has_archive'   => false,
    'capabilities' => array(
        'create_posts' => false,  
    ),
    'menu_icon' => plugins_url('socialvoting/assets/images/sv_voting.png'),
    'map_meta_cap' => true,
  );
  register_post_type( 'sv_voting', $args ); 


}

function sv_meta_box() {
    $screens = array( 'sv_voting');
    foreach ( $screens as $screen ) {
        add_meta_box(
            'sv_sectionid',
            __( 'Voting Status', 'sv_textdomain' ),
            'sv_meta_box_callback',
            $screen
        );
    }
}
add_action( 'add_meta_boxes', 'sv_meta_box' );
function sv_meta_box_callback( $post ) {
   wp_nonce_field( 'sv_meta_box', 'sv_meta_box_nonce' );
   $value = get_post_meta( $post->ID, 'sv_activate', true );
    echo '<label for="sv_activate">';
    _e( 'Check to approve it', 'sv_textdomain' );
    echo '</label> ';
     ?>
     <input type="checkbox" id="sv_activate" name="sv_activate" value="1" <?php checked($value,1);?>  />
     <?php
}
function sv_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['sv_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['sv_meta_box_nonce'], 'sv_meta_box' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
 
    $my_data = ( $_POST['sv_activate'] );
    update_post_meta( $post_id, 'sv_activate', $my_data );
}
add_action( 'save_post', 'sv_save_meta_box_data' );

add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );

function remove_row_actions( $actions, $post )
{
    if( get_post_type() === 'sv_voting' ){
        unset( $actions['edit'] );
        unset( $actions['inline hide-if-no-js'] );
        unset( $actions['view'] );

        return $actions;
    }
    return $actions;
}

// Add custom column in listing of camp in admin 
add_filter('manage_sv_voting_posts_columns', 'total_sv_column');
add_action('manage_sv_voting_posts_custom_column', 'total_sv_column_data', 10, 2);

// ADD NEW COLUMN  
function total_sv_column($defaults) {
	unset($defaults['date']);
    $defaults['user_name'] = 'Username';
    $defaults['social_id'] = 'Social ID';
    $defaults['post_id'] = 'Topic';
    $defaults['ip_track'] = 'System IP';
    $defaults['date']='Voting Date';
    return $defaults;
}

// SHOW THE FEATURED IMAGE  
function total_sv_column_data($column_name, $post_ID) {
if ($column_name == 'user_name') {

echo get_post_meta($post_ID, 'user_name', true);
 
}

if ($column_name == 'post_id') {
    $title=  get_post_meta($post_ID, 'post_id', true) ;
   echo '<a href="'.get_permalink($title).'">'.get_the_title($title).'</a>';
}

if ($column_name == 'social_id') {

echo  get_post_meta($post_ID, 'social_id', true) ;
}

if ($column_name == 'ip_track') {

echo get_post_meta($post_ID, 'ip_track', true) ;
}
}
?>