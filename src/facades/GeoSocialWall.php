<?php
/**
 * Socialwall
 * Socialwall Facade
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
namespace Geonetsolutions\Socialwall\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Socialwall
 * Socialwall Facade
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
class GeoSocialWall extends Facade
{

    /**
     * getFacadeAccessor
     */
    protected static function getFacadeAccessor()
    {
        return 'GeoSocialWall';
    }
}
