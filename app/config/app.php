<?php

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

define('DS', DIRECTORY_SEPARATOR);
define('AVAIL_LANG', ["ar","en",'fr']);
define('DEF_LANG', AVAIL_LANG[1]);

define('PROTOCOL',  $_SERVER['REQUEST_SCHEME']);
define('HOST',      $_SERVER['HTTP_HOST']);
//define('BASE_URI',  '/'); // if no sub directories keep this empty do not add slash / NGINX
define('BASE_URI',  '/doctortech-lotfio/'); // APACHE

define('URL', PROTOCOL ."://". HOST . "/");

// base folder
define('ROOT', dirname(dirname(__DIR__)) . DS);

// app folders
define('APP',         ROOT    . 'app'         . DS);
define('CONFIG',      APP     . 'config'      . DS);
define('CONTROLLERS', APP     . 'Controllers' . DS);
define('MODELS',      APP     . 'Models'      . DS);
define('SYS',         APP     . 'System'      . DS);
define('HELPERS',     SYS     . 'helpers'     . DS);
define('VIEWS',       APP     . 'resources'   . DS . 'views'     . DS);
define('STORAGE',     APP     .  'storage'    . DS);
define('CACHE',       STORAGE .  'cache'      . DS);
define('LANG',        STORAGE .  'languages'  . DS);

// PUBLIC FOLDERS for php
define('PUB_FOLDER',    ROOT           . 'public' . DS);
define('ASSETS_FOLDER', PUB_FOLDER     . 'assets' . DS);
define('CSS_FOLDER',    ASSETS_FOLDER  . 'css'    . DS);
define('JS_FOLDER',     ASSETS_FOLDER  . 'js'     . DS);
define('IMG_FOLDER',    ASSETS_FOLDER  . 'img'    . DS);

// public folders http
define('PUB',    URL      . 'public'   . "/");
define('ASSETS', URL      . BASE_URI   . 'assets' . "/");
define('CSS',    ASSETS   . 'css'      . "/");
define('JS',     ASSETS   . 'js'       . "/");
define('img',    ASSETS   . 'img'      . "/");

define('UPLOADS',  URL      . BASE_URI . 'uploads'. DS);
define('UP_FILES', UPLOADS  . 'files' . DS);
define('UP_IMG',   UPLOADS  . 'img'   . DS);

// security
define('CSRF', "__CSRF");
define('AUTH_SESS_NAME', 'auth');