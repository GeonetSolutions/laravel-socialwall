<?php
/**
 * SocialwallServiceProvider
 * Service provider to register the package with Laravels IOC.
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
namespace Geonetsolutions\Socialwall;

use Illuminate\Support\ServiceProvider;
use Geonetsolutions\Socialwall\Services\SocialWallService;

/**
 * SocialwallServiceProvider
 * Laravel Service Provider to bind this Package.
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */
class SocialwallServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ __DIR__.'/config/socialwall.php' => config_path('socialwall.php')], 'config');
        $this->publishes([ __DIR__.'/resources/assets/js/components/GeonetSocialWallComponent.vue' => resource_path('assets/js/components/GeonetSocialWallComponent.vue')], 'vue');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'geonetsocialwall');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('GeoSocialWall', SocialWallService::class);
    }
}
