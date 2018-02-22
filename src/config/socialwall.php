<?php 
/**
 * Socialwall Confgiuration
 * Socialwall Configuration entries.
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
return [
    'socialwall_dev_mode' => env('SOCIALWALL_DEV_MODE', false),
    'twitter_consumer_key' => env('TWITTER_CONSUMER_KEY', ''),
    'twitter_consumer_secret' => env('TWITTER_CONSUMER_SECRET', ''),
    'twitter_oauth_access_token' => env('TWITTER_OAUTH_ACCESS_TOKEN', ''),
    'twitter_oauth_access_token_secret' => env('TWITTER_OAUTH_ACCESS_TOKEN_SECRET', ''),
    'twitter_api_url' => env('TWITTER_API_URL', 'statuses/user_timeline'),
    'twitter_number_tweets' => env('TWITTER_NUMBER_TWEETS', '10'),
    'facebook_app_id' => env('FACEBOOK_APP_ID', ''),
    'facebook_app_secret' => env('FACEBOOK_APP_SECRET', ''),
    'facebook_page_id' => env('FACEBOOK_PAGE_ID', ''),
    'facebook_redirect_uri' => env('FACEBOOK_REDIRECT_URI', ''),
    'facebook_number_posts' => env('FACEBOOK_NUMBER_POSTS', '10'),
    'instagram_access_token' => env('INSTAGRAM_ACCESS_TOKEN', ''),
    'instagram_number_photos' => env('INSTAGRAN_NUMBER_PHOTOS', '10')
];
