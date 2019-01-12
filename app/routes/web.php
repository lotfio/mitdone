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

Router::get('/', "IndexController@index");

// admin routes
Router::get('/admin', "Admin\\LoginController@showLoginForm");
Router::get('/admin/login', "Admin\\LoginController@showLoginForm");
Router::post('/admin/login', "Admin\\LoginController@loginDo");

Router::get('/admin/home', "Admin\\HomeController@index");
Router::get('/admin/logout', "Admin\\LogOutController@logout");

Router::get('/admin/profile', "Admin\\ProfileController@index");

Router::get('/admin/users', "Admin\\UsersController@index");
Router::get('/admin/users/{page}', "Admin\\UsersController@index")->filter(["page"=>"/[0-9]+/"]);

// users
Router::get('/admin/users/show/{id}', "Admin\\UsersController@show")->filter(["id"=>"/[0-9]+/"]);
Router::get('/admin/users/edit/{id}', "Admin\\UsersController@edit")->filter(["id"=>"/[0-9]+/"]);
Router::post('/admin/users/edit/{id}', "Admin\\UsersController@processEdit")->filter(["id"=>"/[0-9]+/"]);
Router::delete('/admin/users/delete/{id}', "Admin\\UsersController@delete")->filter(["id"=>"/[0-9]+/"]);

Router::get('/admin/users/notify/{id}', "Admin\\UsersController@notify")->filter(["id"=>"/[0-9]+/"]);
Router::post('/admin/users/notify/{id}', "Admin\\UsersController@notify")->filter(["id"=>"/[0-9]+/"]);
Router::get('/admin/users/message/{id}', "Admin\\UsersController@message")->filter(["id"=>"/[0-9]+/"]);
Router::post('/admin/users/message/{id}', "Admin\\UsersController@message")->filter(["id"=>"/[0-9]+/"]);


// orders
Router::get('/admin/orders/', "Admin\\OrdersController@index");
Router::get('/admin/orders/{page}', "Admin\\OrdersController@index")->filter(["page"=>"/[0-9]+/"]);

Router::delete('/admin/orders/delete/{id}', "Admin\\OrdersController@delete")->filter(["id"=>"/[0-9]+/"]);

Router::get('/admin/orders/show/{id}', "Admin\\OrdersController@show")->filter(["id"=>"/[0-9]+/"]);
Router::get('/admin/orders/edit/{id}', "Admin\\OrdersController@edit")->filter(["id"=>"/[0-9]+/"]);