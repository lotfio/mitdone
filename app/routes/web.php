<?php use MITDone\Aven\Facades\Aven as Router;

/**
 * MITDone PHP MVC Framework 2018 
 *
 * @author      MITDone llc <dev@mitdone.com>
 * @copyright   2018 MITDone llc
 * @license     MIT
 *
 * @link        https://mitdone.com
 *
 */

Router::config([
    "namespace"=>"Controllers\\",
    "cache"    => CACHE
]);


// web routes will be defined here

Router::get('/', function(){return "hello world";});

Router::get('/admin/login', "Admin\\LoginController@showLoginForm");

Router::get('/admin/login', "Admin\\LoginController@showLoginForm");