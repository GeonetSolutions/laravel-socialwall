<?php
/**
 * Package Web Routes
 *
 * @package SocialWall
 * @author  Lewis Thompson <lewis@geonetsolutions.co.uk>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link    http://geonetsolutions.github.io
 */

Route::get('/vue/socialwall', 'Geonetsolutions\Socialwall\Http\Controllers\SocialwallController@index');
Route::get('/ajax/socialwall', 'Geonetsolutions\Socialwall\Http\Controllers\SocialwallController@json');
