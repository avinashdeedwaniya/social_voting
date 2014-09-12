<?php
/**
 *  Social voting setting page
 */

// create custom plugin settings menu
add_action('admin_menu', 'sv_create_menu');
function sv_create_menu() {
    //create new top-level menu
    add_menu_page('Social Voting Plugin Settings', 'Social Voting Settings', 'administrator', __FILE__, 'sv_settings_page',plugins_url('assets/images/sv_voting.png', __FILE__));
    //call register settings function
    add_action( 'admin_init', 'register_svsettings' );
}

//add admin.css to plugin option page
add_action( 'admin_init', 'sv_admin_init' );
function sv_admin_init() {
    wp_register_style('svAdminCss', plugins_url('assets/css/admin.css', __FILE__));
    wp_enqueue_style( 'svAdminCss' );
}

function register_svsettings() {
    //register our settings
    register_setting( 'sv-settings-group', 'activate_facebook_voting' );
	register_setting( 'sv-settings-group', 'sv_error_message' );
	register_setting( 'sv-settings-group', 'sv_already_voted_message' );
	register_setting( 'sv-settings-group', 'sv_email_voting_success_message' );
	register_setting( 'sv-settings-group', 'sv_voting_success_message' );
	register_setting( 'sv-settings-group', 'sv_facebook_app_id' );
    register_setting( 'sv-settings-group', 'activate_linkedin_voting' );
    register_setting( 'sv-settings-group', 'sv_linkedin_app_id' );
    register_setting( 'sv-settings-group', 'sv_post_type' );
    register_setting( 'sv-settings-group', 'activate_email_voting' );
    register_setting( 'sv-settings-group', 'activate_twitter_voting' );
    register_setting( 'sv-settings-group', 'sv_twitter_secret' );
    register_setting( 'sv-settings-group', 'sv_twitter_key' );
	register_setting( 'sv-settings-group', 'sv_voting_email_popup_message' );
	register_setting( 'sv-settings-group', 'sv_voting_popup_message' );
	register_setting( 'sv-settings-group', 'sv_voting_button_text' );
	register_setting( 'sv-settings-group', 'sv_email_submit_button_text' );
	
}

function sv_settings_page() { 
    ?>
    <style type="text/css">
        frameset{border: 1px solid #ccc; font-size: 14px;}
        legend{font-size: 16px; font-weight: bold; text-decoration: underline; }
    </style>
    <div class="wrap">
        

        <h2>Social Voting Settings</h2>
        <form method="post" action="options.php" class="validateForm" id="svOptionForm">
            <?php settings_fields( 'sv-settings-group' ); ?>
            <?php do_settings_sections( 'sv-settings-group' ); ?>

            <!-- SOCAIL VOTING TABS-->
            <div id="svSettingsTabs">
                <h2 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" href="#svGeneralTab">General</a>
                    <a class="nav-tab " href="#svFacebookTab">Facebook</a>
                    <a class="nav-tab " href="#svLinkedInTab">LinkedIn</a>
                    <a class="nav-tab " href="#svTwitterTab">Twitter</a>
                    <a class="nav-tab " href="#svEmailTab">E-Mail</a>
                </h2>
                <!-- GENERAL SOCIAL VOTING TAB-->
                <div id="svGeneralTab" style="display:block">
                    
                    <table class="form-table">
                        <tr valign="top" class="customPostTr">
                            <th scope="row" colspan="2">Please select content type where you want to show Social Voting button</th></tr>
                            <tr valign="top" class="customPostTr">
                            <td colspan="2">
                                <?php
                                if( is_array(get_option('sv_post_type')) && in_array('post', get_option('sv_post_type'))){ ?>
                                    <input type="checkbox" name="sv_post_type['post']" value="post" checked="checked"> Post
                                <?php } else { ?>
                                    <input type="checkbox" name="sv_post_type['post']" value="post"> Post
                                <?php
                                }
                                ?>
                            </td>
                            <?php
                            $args = array(
                                'public'   => true,
                                '_builtin' => false
                            );
                            $post_types = get_post_types( $args);
                            
                            if(is_array($post_types) && !empty($post_types)){
                                foreach ( $post_types  as $post_type ) {
                                    if( is_array(get_option('sv_post_type')) && in_array($post_type, get_option('sv_post_type'))){ ?>
                                        <td>
                                            <input type="checkbox" name="sv_post_type['<?php echo $post_type ?>']" value="<?php echo $post_type ?>" checked="checked" /> <?php echo ucfirst( str_replace('_',' ',$post_type)); ?>
                                        </td>

                                    <?php } else { ?>
                                        <td>
                                            <input type="checkbox" name="sv_post_type['<?php echo $post_type ?>']" value="<?php echo $post_type ?>" /> <?php echo ucfirst( str_replace('_',' ',$post_type)); ?>
                                        </td>
                                    <?php
                                    }
                                }
                            }
                              ?>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Social Voting Button Text</th>
                            	<td>
                            		<input type="text" size="100" name="sv_voting_button_text" id="sv_voting_button_text" value="<?php echo get_option('sv_voting_button_text'); ?>" />
                            		<br /><small><i>(Vote for this post.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Email Voting Button Text</th>
                            	<td>
                            		<input type="text" size="100" name="sv_email_submit_button_text" id="sv_email_submit_button_text" value="<?php echo get_option('sv_email_submit_button_text'); ?>" />
                            		<br /><small><i>(Vote this.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Error Message</th>
                            	<td>
                            		<input type="text" size="100" name="sv_error_message" id="sv_error_message" value="<?php echo get_option('sv_error_message'); ?>" />
                            		<br /><small><i>(Sorry you are done something wrong.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Already Voted Message</th>
                            	<td>
                            		<input type="text" size="100" name="sv_already_voted_message" id="sv_already_voted_message" value="<?php echo get_option('sv_already_voted_message'); ?>" />
                            		<br><small><i>(Sorry, you have already voted on this post through this channel. Voting also through other channels.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Success Message for Email Voting</th>
                            	<td>
                            		<input type="text" size="100" name="sv_email_voting_success_message" id="sv_email_voting_success_message" value="<?php echo get_option('sv_email_voting_success_message'); ?>" />
                            		<br/><small><i>(Thank you for voting. Within minutes you will receive an email to confirm your vote.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Success Message for Voting</th>
                            	<td>
                            		<input type="text" size="100" name="sv_voting_success_message" id="sv_voting_success_message" value="<?php echo get_option('sv_voting_success_message'); ?>" />
                            		<br /><small><i>(Thank you for voting. Let others know that you voted. This post Share your voice:
)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Email Voting Popup Message</th>
                            	<td>
                            		<input type="text" size="100" name="sv_voting_email_popup_message"
                            		 id="sv_voting_email_popup_message" value="<?php echo get_option('sv_voting_email_popup_message'); ?>" />
                            		 <br /><small><i>(Please enter your email address and cast your vote.)</i></small>
                            	</td>
                        	</th>
                        </tr>
                        
                        <tr valign="top" class="customPostTr">
                            <th scope="row">Voting Popup Message</th>
                            	<td>
                            		<textarea name="sv_voting_popup_message" rows="5" cols="95"
                            		 id="sv_voting_popup_message"> <?php echo get_option('sv_voting_popup_message'); ?> </textarea>
                            		 <br/><small><i>  <ul><li>Vote for as many post as you want</li>
                            		 	<li>You can vote through the following social media channels and email. One vote per channel.</li>
                            		 </ul></i></small>
                            	</td>
                        	</th>
                        </tr>
                            	
                    </table>
                </div>

                <!-- FACEBOOK SOCIAL VOTING TAB-->
                <div id="svFacebookTab"  style="display:none">
                     
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activate Facebook Voting</th>
                            <td>
                                <input type="checkbox" value="1" name="activate_facebook_voting" id="activate_facebook_voting" <?php checked( get_option('activate_facebook_voting'), 1 ); ?>>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Facebook App ID</th>
                            <td><input type="text" name="sv_facebook_app_id" id="sv_facebook_app_id" value="<?php echo get_option('sv_facebook_app_id'); ?>"   />
                            </td>
                        </tr>
                    </table>
                    
                </div>

                <!-- LINKEDIN SOCIAL VOTING TAB-->
                <div id="svLinkedInTab" style="display:none">
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activate Linkedin Voting</th>
                            <td>
                                <input type="checkbox" value="1" name="activate_linkedin_voting" id="activate_linkedin_voting" <?php checked( get_option('activate_linkedin_voting'), 1 ); ?>>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">LinkedIn App ID</th>
                            <td><input type="text" name="sv_linkedin_app_id" id="sv_linkedin_app_id" value="<?php echo get_option('sv_linkedin_app_id'); ?>" />
                            </td>
                        </tr>
                    </table>
                     
                </div>

                <!-- TWITTER SOCIAL VOTING TAB-->
                <div id="svTwitterTab" style="display:none">
                    
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activate Twitter Voting</th>
                            <td>
                                <input type="checkbox" value="1" name="activate_twitter_voting" id="activate_twitter_voting" <?php checked( get_option('activate_twitter_voting'), 1 ); ?>>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Twiiter Key</th>
                            <td><input type="text" name="sv_twitter_key" id="sv_twitter_key" value="<?php echo get_option('sv_twitter_key'); ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Twitter Secret</th>
                            <td><input type="text" name="sv_twitter_secret" id="sv_twitter_secret" value="<?php echo get_option('sv_twitter_secret'); ?>" />
                            </td>
                        </tr>
                    </table>
                     
                </div>

                <!-- EMAIL SOCIAL VOTING TAB-->
                <div id="svEmailTab" style="display:none">
                     
                  
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Activate e-mail Voting</th>
                            <td>
                                <input type="checkbox" value="1" name="activate_email_voting" <?php checked( get_option('activate_email_voting'), 1 ); ?>>
                            </td>
                        </tr>
                    </table>
                    
                </div>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }
