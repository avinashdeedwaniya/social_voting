<?php
/**
 * @file
 * A single location to store configuration.
 */
$key = get_option("sv_twitter_key");
$secret = get_option("sv_twitter_secret");
define('OAUTH_CALLBACK', home_url('sv_callback')); 
