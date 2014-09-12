<?php

function sv_redirect(){
    if(!session_id() ) {
        ob_flush();
        session_start();
    }
  $key = get_option("sv_twitter_key");
    $secret = get_option("sv_twitter_secret");
    require_once($dir.'twitteroauth/twitteroauth.php');

    $postid = (isset($_GET['postid']) && is_numeric($_GET['postid'])) ? $_GET['postid'] : '';
    $_SESSION['concept_id'] = $postid;
    define('OAUTH_CALLBACK', get_permalink(get_page_by_path('sv_callback')));

    /* Build TwitterOAuth object with client credentials. */
    $connection = new TwitterOAuth($key, $secret);
     
    /* Get temporary credentials. */
    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

    /* Save temporary credentials to session. */
    $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
     
    /* If last connection failed don't display authorization link. */
    switch ($connection->http_code) {
      case 200:
        /* Build authorize URL and redirect user to Twitter. */
        $url = $connection->getAuthorizeURL($token); 
        ob_flush();
        wp_js_redirect($url); 
        break;
      default:
        /* Show notification if something went wrong. */
        echo 'Could not connect to Twitter. Refresh the page or try again later.';
    }
}
add_shortcode('sv_redirect','sv_redirect');


function sv_callback(){
    if(!session_id() ) {
         ob_flush();
        session_start();
    }
    $key = get_option("sv_twitter_key");
    $secret = get_option("sv_twitter_secret");
    require_once($dir.'twitteroauth/twitteroauth.php');

    /* If the oauth_token is old redirect to the connect page. */
    if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
      $_SESSION['oauth_status'] = 'oldtoken';
      if(!session_id() ) {
        session_start();
        }
        session_destroy();
        wp_js_redirect(get_permalink($_SESSION['concept_id'] ));
    }

    /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
    $connection = new TwitterOAuth($key, $secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

    /* Request access tokens from twitter */
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

    /* Save the access tokens. Normally these would be saved in a database for future use. */
    $_SESSION['access_token'] = $access_token;

    /* Remove no longer needed request tokens */
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);

    /* If HTTP response is 200 continue otherwise send to connect page to retry */
    if (200 == $connection->http_code) {
      /* The user has been verified and the access tokens can be saved for future use */
      $_SESSION['status'] = 'verified';
      wp_js_redirect(get_permalink( $_SESSION['concept_id'] ).'?twitter');

    } else {
      /* Save HTTP status for error dialog on connnect page.*/
      if(!session_id() ) {
        session_start();
        }
        session_destroy();
        wp_js_redirect(get_permalink($_SESSION['concept_id'] ));
    }
}
add_shortcode('sv_callback','sv_callback');

function sv_email_voting_varify(){
	$email=base64_decode($_REQUEST['emailID']);
	$postID=base64_decode($_REQUEST['postid']);
	
	if(trim($email)=='' || trim($postID)==''){
		return get_option('sv_error_message');
	}
	else{
		$args = array(
	    'post_type' => 'sv_voting',
	    'meta_query' => array(
	    	'relation'=>'AND',
		       array(
		           'key' => 'user_name',
		           'value' => $email,
		           'compare' => '=' 
		       ),
		       array(
		           'key' => 'social_id',
		           'value' => $email,
		           'compare' => '='
		       )
			   ,
		       array(
		           'key' => 'shared_on',
		           'value' => 'email',
		           'compare' => '='
		       )
			   ,
		       array(
		           'key' => 'post_id',
		           'value' => $postID,
		           'compare' => '=',
		           'type'=>'integer'
		       )
		   )
		);
		 
		$the_query = new WP_Query( $args );
		
		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) : $the_query->the_post(); 
				if(!get_post_meta(get_the_ID(),'sv_activate',true)){
					update_post_meta(get_the_ID(), 'sv_activate', 1);
					return 'Thank you for voting.';
				}
				else{
					return get_option('sv_already_voted_message');
				}
			endwhile; 
		else:
			return get_option('sv_error_message');	
		endif;
	}
}
add_shortcode('sv_email_voting_varify','sv_email_voting_varify');
?>