<?php
function get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

function wp_js_redirect($url){
    echo'<script>location.href="'.$url.'";</script>';
}

// + GET TOTAL NUMBER OF VOTES FORM PARTICULAR CONCEPT
function get_total_votes_for_concept($concept_id){

	$args = array(
	    'post_type' => 'sv_voting',
	    'posts_per_page'=>-1,
	    'meta_query' => array(
	    'relation'=>'AND',
	       array(
	           'key' => 'post_id',
	           'value' => $concept_id,
	           'compare' => '=',
	           'type'=>'integer'
	       ),
		   array(
		   	'key' => 'sv_activate',
		   	'value' => '1',
	        'compare' => '=',
	        'type'=>'integer'
		   )
	   )
	);
	$the_query = new WP_Query( $args );
	
	if ( $the_query->have_posts() ) :
		$totalVotes=0;
		 while ( $the_query->have_posts() ) : $the_query->the_post(); 
		 	$totalVotes++;
		 endwhile; 
		wp_reset_postdata(); 
	endif;
	$block_string = '';
	if($totalVotes > 1) {
        $block_string.= "<span>" .$totalVotes . "</span> votes";
    } else {
        if ($totalVotes == 0 || $totalVotes == '') {
            $block_string.= "<span>0</span> vote";
        } else {
            $block_string.= "<span>" .$totalVotes . "</span> vote";
        }
    }
  
    return $block_string;
}


function validate_voting_for_post($username,$social_id,$post_id,$voting_type){
	
	$args = array(
	    'post_type' => 'sv_voting',
	    'meta_query' => array(
	    	'relation'=>'AND',
	       array(
	           'key' => 'user_name',
	           'value' => $username,
	           'compare' => '=' 
	       ),
	       array(
	           'key' => 'social_id',
	           'value' => $social_id,
	           'compare' => '='
	       )
		   ,
	       array(
	           'key' => 'shared_on',
	           'value' => $voting_type,
	           'compare' => '='
	       )
		   ,
	       array(
	           'key' => 'post_id',
	           'value' => $post_id,
	           'compare' => '=',
	           'type'=>'integer'
	       )
	   )
	);
	$the_query = new WP_Query( $args );
	
	if ( $the_query->have_posts() ) :
		 return true;
	else:
		return false;	
	endif;
}

function get_active_social_media($type){
   switch($type){
       case 'facebook':
           return get_option('activate_facebook_voting');
           break;
       case 'linkedin':
           return get_option('activate_linkedin_voting');
           break;
       case 'twitter':
           return get_option('activate_twitter_voting');
           break;
       case 'email':
           return get_option('activate_email_voting');
           break;
       default:
           return 0;
   }
}

function sv_voting_add( $content ) {
        if ( is_single() ) {
            $custom_content = '<div class="social_voting_add">'.sv_voting('',false).'</div>';
            if(get_option('sv_before_post')){
            	$content = $custom_content.$content;
            }
            if(get_option('sv_after_post')){
            	$content = $content.$custom_content;
            }
             
             
            return $content;
        } else {
            return $content;
        }
}
add_filter( 'the_content', 'sv_voting_add' );
?>