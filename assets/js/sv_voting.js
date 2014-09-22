jQuery( document ).ready(function() {

    // Facebook Api.
    (function(d){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = false;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);
    }(document));

    //Manage social voting tabs
   

    // Pop-up close function.
    jQuery('.popupBoxClose, #wrapper').click(function() {
        jQuery('.popupBoxClose').parents('div.overlay').css('display', 'none');
        jQuery('#wrapper').css({
            'opacity': '1'
        });
        jQuery('.email_field').val('');
        jQuery('.email_error').hide();
        jQuery('.share-button a').attr('class', 'page_url');
    });

    /* Plugin Setting form validation. */
    jQuery('.validateForm').submit(function(e) {
        //remove erorMsg tag for duplicate repeatation.
        jQuery("#svOptionForm").find('span.errorMsg').remove();
        jQuery(".requiredField").each(function(){
            if (jQuery(this).val() == '') {
                jQuery(this).css('border', '1px solid #FE1F20');
                jQuery(this).parent('td').append('<small>This field is required.</small>');
                e.preventDefault();
            }
        });
        if(jQuery("#activate_facebook_voting:checked").length > 0){
            if(jQuery("#sv_facebook_app_id").val()==''){
                jQuery("#sv_facebook_app_id").css('border', '1px solid #FE1F20');
                jQuery("#sv_facebook_app_id").parent('td').append('<small>This field is required.</small>');
                e.preventDefault();
            }
        }
        if(jQuery("#activate_linkedin_voting:checked").length > 0){
            if(jQuery("#sv_linkedin_app_id").val()==''){
                jQuery("#sv_linkedin_app_id").css('border', '1px solid #FE1F20');
                jQuery("#sv_linkedin_app_id").parent('td').append('<small>This field is required.</small>');
                e.preventDefault();
            }
        }
        if(jQuery("#activate_twitter_voting:checked").length > 0){
            if(jQuery("#sv_twitter_key").val()==''){
                jQuery("#sv_twitter_key").css('border', '1px solid #FE1F20');
                jQuery("#sv_twitter_key").parent('td').append('<small>This field is required.</small>');
                e.preventDefault();
            }
            if(jQuery("#sv_twitter_secret").val()==''){
                jQuery("#sv_twitter_secret").css('border', '1px solid #FE1F20');
                jQuery("#sv_twitter_secret").parent('td').append('<small>This field is required.</small>');
                e.preventDefault();
            }
        }
    });
    /* End of Plugin Setting form validation. */

    //Email voting.

    jQuery('.email_button').click(function(){
        var emailVal = '';
        emailVal = jQuery(this).parent('p').parent('div').children('div.form').children('.email_field').val();
        if(emailVal == '') {
           jQuery('.email_message').hide();
           jQuery('.email_error').show();
           jQuery('.email_error').text(social.email_mandatory).css('color','red');
           return false;
        }
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

        if(emailVal.match(mailformat)) {
            jQuery('.email_message').show();
            var post_id = jQuery('.post_id').val();
            var social_id = emailVal;
            var data = {
                action: 'social_voting_ajax_function',
                post_id: post_id,
                social_id : social_id,
                user_name : social_id,
                voting_type : 'email'
            };
            jQuery(".loader_image").css('display','block');
            jQuery.ajax({
                type : "post",
                url : social.ajaxurl,
                data : data,
                success: function(response) {
                    var res = response.split("__");
                    emailVal = '';
                    jQuery('.email_error').hide();
                    jQuery(".loader_image").css('display','none');
                    jQuery(".emailPopUpBox").css('display','none');
                    jQuery(".share-button").css('display','none');
                    jQuery(".email-share-button").css('display','block');
                    jQuery(".msgPopUp").css('display','block');
                    jQuery(".response-msg").text(res[0]);
                }
            });
        } else {
            jQuery(".loader_image").css('display','none');
            jQuery('.email_message').hide();
            jQuery('.email_error').show();
            jQuery('.email_error').text(social.email_mandatory).css('color','red');
            return false;
        }
    });

    //Social Voting
    jQuery('.social_button').click(function(){
        jQuery('.email_message').show();
        var post_id = jQuery('.post_id').val();
        var social_id = jQuery('.userID').val();
        var email_id = jQuery('.emailID').val();
        var socialType = jQuery('.socialType').val();
        var data = {
            action: 'social_voting_ajax_function',
            post_id: post_id,
            social_id : social_id,
            user_name : email_id,
            voting_type : socialType
        };
        jQuery.ajax({
            type : "post",
            url : social.ajaxurl,
            data : data,
            success: function(response) {
                var res = response.split("__");
                jQuery(".socialPopUp").css('display','none');
                jQuery(".msgPopUp").css('display','block');
                jQuery(".response-msg").text(res[0]);
                jQuery(".share-button").css('display','block');
                jQuery(".email-share-button").css('display','none');
                if(socialType != ''){
                    jQuery(".share").css('display','block');
                }
                if(res[1] == 1){
                    var vote = parseInt(jQuery("span.voting-link").children("span").html());
                    vote += 1;
                    jQuery("span.voting-link").children("span").html(vote);
                }
                jQuery(".social").val(socialType);
                jQuery(".page_url").addClass(socialType);
            }
        });
    });

});

/* Voting light-box at click on voting link at concept detail page */
function pop_box(){
    jQuery('.voting_popup_box').fadeIn('fast');
    jQuery('#wrapper').css({'opacity': '0.3'});    //div with id wrapper is pacity': '1'
}

function emailPopUp(){
    jQuery('.emailPopUpBox').fadeIn('fast');
    jQuery('#wrapper').css({'opacity': '0.3'});
}

function socialPopUp(userID, email, type){
    jQuery(".userID").val(userID);
    jQuery(".emailID").val(email);
    jQuery(".socialType").val(type);
    jQuery("ul.social li figure").removeClass();
    jQuery("ul.social li figure").addClass(type);
    //jQuery("span.social-voting-content").text(type);
    jQuery('.socialPopUp').fadeIn('fast');
    jQuery('#wrapper').css({'opacity': '0.3'});
}

//FOR FACEBOOK VOTING.
function fb_publish() {
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            socialPopUp(response.authResponse.userID, response.authResponse.userID, 'Facebook');
        }
        else {
            FB.login(function(response){
                if (response.status === 'connected') {
                    socialPopUp(response.authResponse.userID, response.authResponse.userID, 'Facebook');
                }
            });
        }
    });
}

//FOR LINKED-IN VOTING.
function onLinkedInAuth() {
        IN.API.Profile("me")
            .fields("id", "firstName", "lastName", "industry", "location:(name)", "picture-url", "headline", "summary", "num-connections", "public-profile-url", "distance", "positions", "email-address", "educations", "date-of-birth")
            .result(displayProfiles);
}

function displayProfiles(profiles) {
    member = profiles.values[0];
    var email = member.emailAddress;
    var social_id = member.id;
    socialPopUp(social_id, email, 'LinkedIn');
}

function linkdinlog() {
    IN.User.refresh();
    if (IN.User.isAuthorized()) {
        onLinkedInAuth();
    } else {
        IN.User.authorize(function () {
            onLinkedInAuth();
        });
    }

    window.setTimeout(function () {
        if (!IN.User.isAuthorized()) {
            IN.User.refresh();
            if (IN.User.isAuthorized()) {
                onLinkedInAuth();
            }
        }
    }, 1000);

   // IN.User.authorize(onLinkedInAuth);
}

// FOR TWITTER VOTING.
function twitterPopup() {
    var data = {
        action: 'twitter_voting_popup',
        type: 'twitter'
    };
    jQuery.ajax({
        type : "post",
        url : social.ajaxurl,
        data : data,
        success: function(response) {
            newWinName = window.open(response, "newWinName", "width=500, height=500");
        }
    });
}

function get_twitter_id(){
    var data = {
        action: 'voting_id',
       type: 'twitter'
    };
    jQuery.ajax({
        type : "post",
        url : social.ajaxurl,
        data : data,
        success: function(response) {
            socialPopUp(response, response, 'Twitter');
        }
    });
}

// SOCIAL SHARING.
function social_sharing(){
    var socialType = jQuery(".social").val();
    var link = jQuery(".page_url").attr('data-link');
    var title = jQuery(".page_url").attr('data-title');
    title = encodeURIComponent(title.replace(/&amp;/g, "&"));
    if(socialType == 'Facebook'){
        window.open('https://www.facebook.com/sharer.php?&u='+link+'&t='+title,'sharer','status=0,width=626,height=436, top='+(jQuery(window).height()/2 - 225) +', left='+(jQuery(window).width()/2 - 313 ) +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
    }
    if(socialType == 'LinkedIn'){
        window.open('https://linkedin.com/shareArticle?mini=true&url=' + link + '&title= '+ title +'&summary='+social.linkedin_share_text+'&', 'twitterwindow', 'height=450, width=550, top='+(jQuery(window).height()/2 - 225) +', left='+jQuery(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
    }
    if(socialType == 'Twitter'){
        window.open('https://twitter.com/share?url=' + link + '&hashtags='+social.twitter_hashtag+'&text='+social.twitter_share_text+'&', 'twitterwindow', 'height=450, width=550, top='+(jQuery(window).height()/2 - 225) +', left='+jQuery(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
    }
}