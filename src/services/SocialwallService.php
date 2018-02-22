<?php
/**
 * SocialwallService
 *
 * Service Class that provides all the functionality for the package.
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
namespace Geonetsolutions\Socialwall\Services;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Exception;
use Response;
use Throwable;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SocialWallService
 * SocialWallService Class.
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
class SocialWallService
{
    private $_devMode;
    
    private $_twClient;
    private $_fbClient;
    private $_instaClient;

    public $fbPageId;
    public $fbNumberPosts;
    private $_fbAccessToken;

    public $twNumberPosts;
    
    public $inNumberPosts;
    
    public $wall;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->devMode       = (config('socialwall.socialwall_dev_mode')) ? config('socialwall.socialwall_dev_mode') : false;

        $this->fbPageId      = config('socialwall.facebook_page_id');
        $this->fbNumberPosts = (config('socialwall.facebook_number_posts')) ? config('socialwall.facebook_number_posts') : 10;
        $this->fbClient      = $this->connectFacebook();
        $this->getFbProfile  = $this->getFbProfile();

        $this->twNumberPosts = (config('socialwall.twitter_number_tweets')) ? config('socialwall.twitter_number_tweets') : 10;

        $this->instaClient   = $this->instaClient();
        $this->inNumberPosts = (config('socialwall.instagram_number_photos')) ? config('socialwall.instagram_number_photos') : 10;

        $this->wall          = collect();
    }

    /**
     * Gets the wall organised by date.
     *
     * @param  String $format Denotes response type.
     * @return void
     */
    public function getByDate($format = 'collection')
    {
        $sorted = $this->wall->flatten(1)->sortByDesc('backend_timestamp')->values();
        $response = collect($sorted->all());

        switch ($format) {
            case 'json':
                return $response->toJson();
                break;
            case 'array':
                return $response->toArray();
                break;
            default:
                return $response;
        }
    }

    /**
     * Returns the wall entries shufffled.
     *
     * @return void
     */
    public function shuffle()
    {
        $this->wall = $this->wall->flatten(1)->shuffle();
        return $this;
    }

    /**
     * Authenticate with facebook using their api and with the details from the config/env file
     * Get an access token from facebook so we can authenticate and get the posts
     *
     * @return void
     */
    protected function connectFacebook()
    {
        try {
            $connectFacebook = new Facebook([
                'app_id'                => config('socialwall.facebook_app_id'),
                'app_secret'            => config('socialwall.facebook_app_secret'),
                'default_graph_version' => 'v2.12',
            ]);

            $tokenQuery = http_build_query([
                'client_id'         => config('socialwall.facebook_app_id'),
                'client_secret'     => config('socialwall.facebook_app_secret'),
                'grant_type'        => 'client_credentials',
                'redirect_uri'      => config('socialwall.facebook_redirect_uri')
            ]);

            $r_token = $connectFacebook->get('/oauth/access_token?' . $tokenQuery, $connectFacebook->getApp()->getAccessToken());
            $graph   = $r_token->getGraphObject();
            $a_token = $graph->getProperty('access_token');
            $connectFacebook->setDefaultAccessToken($a_token);
            $this->fbAccessToken = $connectFacebook->getDefaultAccessToken();
            return $connectFacebook;
        } catch (Exception $e) {
            if ($this->devMode) {
                switch (true) {
                    case $e instanceof FacebookResponseException:
                        Log::error('Facebook Graph Returned an Error:' . $e->getMessage());
                        break;
                    case $e instanceof FacebookSDKException:
                        Log::error('Facebook SDK Returned an Error:' . $e->getMessage());
                        break;
                    default:
                        Log::error('Facebook Returned an Error:' . $e->getMessage());
                }
            }
            return response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get the users facebook name and profile picture
     * This has to be a seperate request as you cannot get it in the
     * post request below.
     * We will save them into an array here for use later on.
     *
     * @return void
     */
    protected function getFbProfile()
    {
        try {
            $build_link   = $this->fbClient->get($this->fbPageId . '?fields=name,picture.width(720).height(720)', $this->fbAccessToken);
            $graph        = json_decode($build_link->getBody());
            $profile_info = array();
            $profile_info['name']    = $graph->name;
            $profile_info['picture'] = $graph->picture->data->url;
            return $profile_info;
        } catch (Exception $e) {
            if ($this->devMode) {
                switch (true) {
                    case $e instanceof FacebookResponseException:
                        Log::error('Facebook Graph Returned an Error:' . $e->getMessage());
                        break;
                    case $e instanceof FacebookSDKException:
                        Log::error('Facebook SDK Returned an Error:' . $e->getMessage());
                        break;
                    default:
                        Log::error('There was an Error:' . $e->getMessage());
                }
            }
            return response($e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * Authenticate with facebook using their api and with the details from the config/env file
     * Get an access token from facebook so we can authenticate and get the posts
     *
     * @return void
     */
    public function getFbPosts()
    {
        try {
            $facebookFieldQuery = http_build_query([
                'fields' => 'id,message,created_time,full_picture',
                'limit'  => $this->fbNumberPosts
            ]);

            $build_link = $this->fbClient->get($this->fbPageId . '/posts?' . $facebookFieldQuery, $this->fbAccessToken);
            $graph      = json_decode($build_link->getBody());

            $posts = array();

            foreach ($graph->data as $index => $post) {
                if (array_key_exists("message", $post)) {
                    $strtotime = strtotime($post->created_time);
                    $backend_time = $strtotime;

                    $fb_name = "";
                    $fb_pic = "";

                    $frontend_time = date('d F, Y \a\t H:i:s', $strtotime);

                    $posts[$index]['social_type']            = 'facebook';
                    $posts[$index]['id']                     = $post->id;
                    $posts[$index]['message']                = $post->message;
                    $posts[$index]['link']                   = "https://facebook.com/" . $post->id;
                    $posts[$index]['backend_timestamp']      = $backend_time;
                    $posts[$index]['frontend_timestamp']     = $frontend_time;

                    if (isset($post->full_picture)) {
                        $posts[$index]['media'] =  $post->full_picture;
                    }

                    $posts[$index]['name']           = $this->getFbProfile['name'];
                    $posts[$index]['profile_pic']    = $this->getFbProfile['picture'];
                }
            }

            $this->wall->push($posts);
            return $this;
        } catch (Exception $e) {
            if ($this->devMode) {
                switch (true) {
                    case $e instanceof FacebookResponseException:
                        Log::error('Facebook Graph Returned an Error:' . $e->getMessage());
                        break;
                    case $e instanceof FacebookSDKException:
                        Log::error('Facebook SDK Returned an Error:' . $e->getMessage());
                        break;
                    default:
                        Log::error('There was an Error:' . $e->getMessage());
                }
            }
            return response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Authenticate with twitter using their api and with the details from the config/env file
     *
     * @return void
     */
    protected function connectTwitter()
    {
        $connectTwitter = new TwitterOAuth(
            config('socialwall.twitter_consumer_key'),
            config('socialwall.twitter_consumer_secret'),
            config('socialwall.twitter_oauth_access_token'),
            config('socialwall.twitter_oauth_access_token_secret')
        );

        return $connectTwitter;
    }


    /**
     * Get the tweets with the supplied number from the config/env file
     * user_id can be added to the return parameters below
     *
     * @return void
     */
    public function getTweets()
    {
        $getTweets = $this->connectTwitter()->get(config('socialwall.twitter_api_url'), [ "count" => $this->twNumberPosts, "exclude_replies" => true]);
        $tweets = array();

        if (!isset($getTweets->errors)) {
            foreach ($getTweets as $index => $tweet) {
                $strtotime = strtotime($tweet->created_at);
                $backend_time = $strtotime;
                $frontend_time = DateTime::createFromFormat('D M d G:i:s O Y', $tweet->created_at)->format('d F, Y \a\t H:i:s');

                $tweets[$index]['social_type']          = 'twitter';
                $tweets[$index]['id']                   = $tweet->id;
                $tweets[$index]['message']              = $tweet->text;
                $tweets[$index]['link']                 = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id;
                $tweets[$index]['backend_timestamp']    = $backend_time;
                $tweets[$index]['frontend_timestamp']   = $frontend_time;
                

                if (isset($tweet->entities->media[0]->media_url_https)) {
                    $tweets[$index]['media'] = $tweet->entities->media[0]->media_url_https;
                }

                $tweets[$index]['name']         = '@' . $tweet->user->screen_name;
                $tweets[$index]['profile_pic']  = $tweet->user->profile_image_url_https;
            }
            $this->wall->push($tweets);
        }
        return $this;
    }

    /**
     * Get the instagram posts. Using Instagrams old method, will need to be updated when instagram release their new API
     *
     * @return void
     */
    protected function instaClient()
    {
        try {
            $instaClient = new Client();
            $instaConnect = $instaClient->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/?access_token='. config('socialwall.instagram_access_token'));
            if ($instaConnect->getStatusCode() == 200) {
                $graph = json_decode($instaConnect->getBody());
                return $graph->data;
            }
            return $this;
        } catch (ClientException $e) {
            if ($this->devMode === true) {
                Log::error('There was an Error Connecting to Instagram: ' . $e->getMessage());
            }
            return response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get the instagram posts. Using Instagrams old method, will need to be updated when instagram release their new API
     *
     * @param String $hastag Passes in the Hastag ot Query
     *
     * @return void
     */
    public function getInstaPhotos($hastag = null)
    {
        try {
            $instaFeed = $this->instaClient;
            $instaNumOfPhotos = $this->inNumberPosts;
            $photos = array();
            $used_index = array();
            $hash_count = 0;


            if (!empty($instaFeed)) {
                foreach ($instaFeed as $index => $photo) {
                    $frontend_time = DateTime::createFromFormat('U', $photo->created_time)->format('d F, Y \a\t H:i:s');

                    if (!empty($hastag)) {
                        if (in_array($hastag, $photo->tags)) {
                            $photos[$index]['social_type']          = 'instagram';
                            $photos[$index]['id']                   = $photo->id;
                            $photos[$index]['message']              = $photo->caption->text;
                            $photos[$index]['link']                 = $photo->link;
                            $photos[$index]['backend_timestamp']    = $photo->created_time;
                            $photos[$index]['frontend_timestamp']   = $frontend_time;
                            $photos[$index]['media']                = $photo->images->standard_resolution->url;
                            $photos[$index]['name']                 = $photo->user->username;
                            $photos[$index]['profile_pic']          = $photo->user->profile_picture;

                            array_push($used_index, $photo->id);

                            $hash_count++;
                            if ($hash_count == $instaNumOfPhotos) {
                                break;
                            }
                        }
                    } else {
                        $photos[$index]['social_type']          = 'instagram';
                        $photos[$index]['id']                   = $photo->id;
                        $photos[$index]['message']              = $photo->caption->text;
                        $photos[$index]['link']                 = $photo->link;
                        $photos[$index]['backend_timestamp']    = $photo->created_time;
                        $photos[$index]['frontend_timestamp']   = $frontend_time;
                        $photos[$index]['media']                = $photo->images->standard_resolution->url;
                        $photos[$index]['name']                 = $photo->user->username;
                        $photos[$index]['profile_pic']          = $photo->user->profile_picture;

                        if ($index == $instaNumOfPhotos) {
                            break;
                        }
                    }
                }
                
                if (!empty($hastag) && $hash_count < $instaNumOfPhotos) {
                    foreach ($feed->data as $index => $photo) {
                        $frontend_time = DateTime::createFromFormat('U', $photo->created_time)->format('d F, Y \a\t H:i:s');

                        if (!in_array($photo->id, $used_index)) {
                            $photos[$index]['social_type']          = 'instagram';
                            $photos[$index]['id']                   = $photo->id;
                            $photos[$index]['message']              = $photo->caption->text;
                            $photos[$index]['link']                 = $photo->link;
                            $photos[$index]['backend_timestamp']    = $photo->created_time;
                            $photos[$index]['frontend_timestamp']   = $frontend_time;
                            $photos[$index]['media']                = $photo->images->standard_resolution->url;
                            $photos[$index]['name']                 = $photo->user->username;
                            $photos[$index]['profile_pic']          = $photo->user->profile_picture;

                            $hash_count++;

                            if ($hash_count == $instaNumOfPhotos) {
                                break;
                            }
                        }
                    }
                }

                $this->wall->push($photos);
                return $this;
            }
        } catch (Exception $e) {
            if ($this->devMode === true) {
                Log::error('Instagram get getInstaPhotos returned an error: ' . $e->getMessage());
            }
            return response($e->getMessage(), $e->getCode());
        }
    }
}
