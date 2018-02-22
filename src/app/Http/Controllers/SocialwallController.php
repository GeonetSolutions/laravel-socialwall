<?php
/**
 * SocialwallController
 * Socialwall Controller Class
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
namespace Geonetsolutions\Socialwall\Http\Controllers;

use App\Http\Controllers\Controller;
use Geonetsolutions\Socialwall\Facades\GeoSocialWall;
use Cache;

/**
 * SocialwallController
 * Socialwall Controller Class
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
class SocialwallController extends Controller
{
    /**
     * Index method outputs the Package view
     * to display a social wall.
     *
     * @return void
     */
    public function index()
    {
        return view('geonetsocialwall::socialwall');
    }

    /**
     * Looks for the Social Wall in the Cache
     * before either returning the cached entries
     * or getting the entries from the DB.
     *
     * @return void
     */
    public function json()
    {
        if (Cache::has('GeoSocialWall')) {
            $socialPosts = Cache::get('GeoSocialWall');
        } else {
            $socialPosts = GeoSocialWall::getFbPosts()->getTweets()->getInstaPhotos()->getByDate('json');
            Cache::put('GeoSocialWall', $socialPosts, 15);
        }
        return $socialPosts;
    }
}
