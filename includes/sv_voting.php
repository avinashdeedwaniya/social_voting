<?php

function sv_voting($content=null,$echo=true){
    global $post;
	
    $sv_post_type = array();
    if (get_option('sv_post_type')) {
        $sv_post_type = get_option('sv_post_type');
    }

    if (!in_array(get_post_type(get_the_ID()), $sv_post_type)){
        return $content;
    }
    $thumb_id = get_post_thumbnail_id($post->ID);
    //$thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
    $concept_slide_logo_image  = wp_get_attachment_image_src($thumb_id, 'thumbnail');
    if ( !empty($concept_slide_logo_image )) {
        $thumb_url = $concept_slide_logo_image[0];
    } else {
        $thumb_url = '/wp-includes/images/crystal/default.png';
    }

    $social_string = '';

    // SOCIAL VOTING BUTTON
    //$voting_year = get_post_meta(get_the_ID(), '_cmb_concept_participation_year', true) ? get_post_meta(get_the_ID(), '_cmb_concept_participation_year', true) : '';
    //if ($voting_year > date("Y")-1) {
        // CHECK VOTING IS ON.
       // if (get_option('voting_status') == 1) {
            //CHECK IN WHICH POSTS SOCIAL VOTING BUTTON APPEAR.
            if (is_array(get_option('sv_post_type'))) {
                if (in_array(get_post_type(get_the_ID()), get_option('sv_post_type')) && is_single()) {
                $social_string.='<p><button class="btn-default full-width" onclick="pop_box()" >'.get_option('sv_voting_button_text').'</button></p>
                    <p><span class="btn white block voting-link">'.get_total_votes_for_concept(get_the_ID()).'</span></p>';
                }
            }
        //}
    //}
    // POP-UP BOX WHEN SOCIAL VOTING BUTTONS APPEAR.
    $social_string .= '<div class="overlay voting_popup_box"><div class="overlay-inner social-content"><a class="popupBoxClose"></a>';
    $social_string .= '<div class="ajax_content">'.get_option('sv_voting_popup_message').'<br/><ul class="social">';

    $title = "'".$post->post_title."'";
    $link = get_permalink( get_the_ID() );
    // CHECK FACABOOK VOTING ON.
    if (get_active_social_media('facebook')) {
        $social_string.='<div id="fb-root"></div>
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    appId: "'.get_option("sv_facebook_app_id").'",
                    status: true,
                    cookie: true,
                    xfbml: true,
                    oauth: true
                });
            };
        </script>';

        // FACEBOOK VOTING BUTTON.
        $social_string .= '<li>
                <a class="facebook facebook_button" href="javascript:;"  onclick="fb_publish()"></a>
                <span>Facebook</span>
            </li>';
    }

    // CHECK LINKEDIN VOTING ON.
    if (get_active_social_media('linkedin')) {
        $social_string.=
            '<script type="text/javascript" src="//platform.linkedin.com/in.js">
                api_key:'.get_option("sv_linkedin_app_id").'
                scope: r_basicprofile r_emailaddress r_fullprofile
                authorize: true
            </script>';

        // LINKEDIN VOTING BUTTON.
        $social_string .='
            <li>
                <a class="linked-in" href="javascript:;" onclick="linkdinlog()"></a>
                <span>LinkedIn</span>
            </li>';
    }

    // CHECK TWITTER VOTING ON.
    if (get_active_social_media('twitter')) {
        if(session_id() ) {
            session_start();
        }
        // Get auth key and secret key from settings.
        $key = get_option("sv_twitter_key");
        $secret = get_option("sv_twitter_secret");

        /* If access tokens are not available redirect to connect page. */
        if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) ||
            empty($_SESSION['access_token']['oauth_token_secret'])) {
            // destroy existing sessions.
            if(session_id() ) {
                session_destroy();
            }

            if ($key === '' || $secret === '' || $key === 'CONSUMER_KEY_HERE' || $secret === 'CONSUMER_SECRET_HERE') {
                echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://dev.twitter.com/apps">dev.twitter.com/apps</a>';
                exit;
            }
        }
        $access_token = $_SESSION['access_token'];
        $connection = new TwitterOAuth($key, $secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $content = $connection->get('account/verify_credentials');


        if(isset($content->id)) {
            $social_string .= ' <li>
                <a class="twitter" href="javascript:;" onclick="socialPopUp('.$content->id.','.$content->id.',\'Twitter\')"></a>
                <span>Twitter</span>
            </li>';
        } else {
            $social_string .= ' <li>
                <a class = "twitter" href="'.add_query_arg('postid' , $post->ID, get_permalink(get_page_by_path('sv_redirect'))).'"></a>
                <span>Twitter</span>
            </li>';
        }
        if(isset($_REQUEST['twitter'])){
            $social_string .= '<script type="text/javascript">
        jQuery(document).ready(function(){
            socialPopUp('.$content->id.','.$content->id.', "Twitter");
        });
            </script>';
        }
    }

    // CHECK EMAIL VOTING ON.
    if (get_active_social_media('email')) {

        // EMAIL VOTING BUTTON.
        $social_string .='<li>
                <a class="email" href="javascript:;" onclick="emailPopUp()"></a>
                <span>E-mail</span>
            </li>';
    }
        $social_string .='</ul></div></div></div>';

        // EMAIL VOTING POP-UP
        $social_string.=
            '<div class="overlay emailPopUpBox">
             <div class="overlay-inner mail-content">
                <a class="popupBoxClose"></a>
                <div class="row">
                    <figure><img src="'.$thumb_url.'" alt=""></figure>
                    <section>
                        <h3>'.$title.'</h3>
                    </section>
                </div>
                <p>'.get_option("sv_voting_email_popup_message").'</p>
                <div class="row form">
                    <label>e-mailadres</label>
                    <input type="text" class="email_field" name="EmailID" value="">
                    <div class="loader_image" style="display:none"><img src="'.plugins_url() . '/socialvoting/assets/images/loader.gif" alt="lader.."></div>
                    <span class="email_error"></span>
                    <div class="email_message"></div>
                </div>
                <input type="hidden" name="Email" value="1">
                <input class="post_id" type="hidden" name="post_id" value="'.$post->ID.'">
                <p><a class="btn email_button">'.get_option('sv_email_submit_button_text').'</a></p>
            </div></div>';

    // Social Voting pop-up
    $social_string.=
        '<div class="overlay socialPopUp">
             <div class="overlay-inner mail-content">
                <a class="popupBoxClose"></a>
                <div class="row">

                    <figure><img src="'.$thumb_url.'" alt=""></figure>
                    <section>
                        <h3>'.$title.'</h3>
                    </section>
                </div>
                <input class="post_id" type="hidden" name="post_id" value="'.$post->ID.'">
                <input class="userID" type="hidden" name="user_id" value="">
                <input class="socialType" type="hidden" name="user_id" value="">
                <input class="emailID" type="hidden" name="user_id" value="">
                <div class="voteWithUs">
                    <span>Stem Via</span>
                    <ul class="social">
                        <li>
                            <figure class=""></figure>
                        </li>
                    </ul>
                    <a class="btn social_button" href="javascript:;">Stem</a>
                </div>
             </div>
        </div>';
    // Message pop-up
    $social_string.=
        '<div class="overlay msgPopUp">
             <div class="overlay-inner msg-content">
                <a class="popupBoxClose"></a>
                <h1 class="caption-heading">Bedankt</h1>
                <p class= "response-msg"></p>
                <input class="social" type="hidden" value="">
                <div class="share-button">
                    <a href="javascript:;" onclick="social_sharing()" class="page_url" data-link='.$link.' data-title='.$title.'></a>
        </div>
        </div></div>';
    $content    =   $social_string;
	
    if($echo){  
        print_r($content);
    }else{
        return $content;
    }
}

add_shortcode('sv_voting','sv_voting');
?>