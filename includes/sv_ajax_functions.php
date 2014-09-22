<?php
add_action( 'wp_ajax_voting_id', 'voting_id' );
add_action( 'wp_ajax_nopriv_voting_id', 'voting_id' );
function voting_id(){
    $key = get_option("sv_twitter_key");
    $secret = get_option("sv_twitter_secret");

    if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
        session_destroy();
        if ($key === '' || $secret === '') {
            echo 'You need a consumer key and secret to test the sample code. Get one from <a href="//dev.twitter.com/apps">dev.twitter.com/apps</a>';
            exit;
        }

    }
    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth($key, $secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $content = $connection->get('account/verify_credentials');
    echo $content->id;
    die;
}

add_action( 'wp_ajax_twitter_voting_popup', 'twitter_voting_popup' );
add_action( 'wp_ajax_nopriv_twitter_voting_popup', 'twitter_voting_popup' );
function twitter_voting_popup() {
    $key = get_option("sv_twitter_key");
    $secret = get_option("sv_twitter_secret");

    $connection = new TwitterOAuth($key, $secret);
    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);
    $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    /* If last connection failed don't display authorization link. */
    switch ($connection->http_code) {
        case 200:
            /* Build authorize URL and redirect user to Twitter. */
            $url = $connection->getAuthorizeURL($token);
            echo $url;
            break;
    }
    die;
}

add_action( 'wp_ajax_social_voting_ajax_function', 'social_voting_ajax_function' );
add_action( 'wp_ajax_nopriv_social_voting_ajax_function', 'social_voting_ajax_function' );

function social_voting_ajax_function() {
    $msg =  get_option('sv_error_message');
    if ($_REQUEST['user_name'] != '') {
        global $wpdb;
		
        //CHECK VALIDATION OF SAME VOTE ON SAME POST BY SAME USER.
        $result	=	validate_voting_for_post($_REQUEST['user_name'],$_REQUEST['social_id'],$_REQUEST["post_id"],$_REQUEST['voting_type']);
		$seprator = "__0";
        if(!$result){
            // Create post object
            $my_post = array(
            'post_title' => $_REQUEST['voting_type'],
            'post_content' => '',
            'post_author' => 1,
            'post_status' => 'publish',
            'post_name' => $_REQUEST['voting_type'],
            'post_type' => 'sv_voting'
            );

            // Insert the post into the database
            $newpost_id = wp_insert_post($my_post);

            if($newpost_id){
                update_post_meta($newpost_id, 'user_name', $_REQUEST['user_name']);
                update_post_meta($newpost_id, 'social_id', $_REQUEST['social_id']);
                update_post_meta($newpost_id, 'post_id', $_REQUEST["post_id"]);
                update_post_meta($newpost_id, 'ip_track', $_SERVER['REMOTE_ADDR']);
                update_post_meta($newpost_id, 'shared_on', $_REQUEST['voting_type']);
				if($_REQUEST['voting_type']=='email'){
					update_post_meta($newpost_id, 'sv_activate', '0');
					$to=$_REQUEST['user_name'];
					$subject='Email varification for your voting';
					$varify_url=add_query_arg( 
					array(
						'emailID' => base64_encode($_REQUEST['user_name']),
						'postid' => base64_encode($_REQUEST['post_id'])
					), get_permalink(get_ID_by_slug('sv_email_voting_varify')));
					$body='Dear '.$_REQUEST['user_name'].'<br> Please click on below link to varify your email 
					voting.<br><strong><a href="'.$varify_url.'">CLICK HERE</a></strong><br><br>Thanks<br>'.get_option('blogname').'<br>'.get_option('blogdescription');
					$headers = 'From: '.get_option('blogname').' <'.get_option('admin_email').'>' . "\r\n";
					wp_mail($to, $subject, $body, $headers);
				}
				else{
					update_post_meta($newpost_id, 'sv_activate', '1');
				}
            }

             
            if($_REQUEST['voting_type'] != 'email'){
                $seprator = "__1";
                $msg = get_option('sv_voting_success_message').$seprator;
            } else {
                $msg = get_option('sv_email_voting_success_message').$seprator;
            }
        }
        else{
            $msg= get_option('sv_already_voted_message') .$seprator;
        }
    }
    echo $msg;
    die;
}
?>