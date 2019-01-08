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

Router::get('/', "indexController@index");

// admin routes
Router::get('/admin', "Admin\\LoginController@showLoginForm");
Router::get('/admin/login', "Admin\\LoginController@showLoginForm");
Router::post('/admin/login', "Admin\\LoginController@loginDo");

Router::get('/admin/home', "Admin\\HomeController@index");
Router::get('/admin/logout', "Admin\\LogOutController@logout");

Router::get('/admin/profile', "Admin\\ProfileController@index");

Router::get('/admin/users', "Admin\\UsersController@index");


Router::get('/admin/users/show/{id}', "Admin\\UsersController@show")->filter(["id"=>"/[0-9]+/"]);
Router::get('/admin/users/edit/{id}', "Admin\\UsersController@edit")->filter(["id"=>"/[0-9]+/"]);
Router::get('/admin/users/delete/{id}', "Admin\\UsersController@delete")->filter(["id"=>"/[0-9]+/"]);