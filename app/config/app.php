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

define('BASE_URI',  '/doctortech-lotfio/'); // to be removed
define('PROTOCOL',  $_SERVER['REQUEST_SCHEME']);
define('HOST',      $_SERVER['HTTP_HOST']);

define('URL', PROTOCOL ."://". HOST . "/");

// base folder
define('ROOT', dirname(dirname(__DIR__)) . DS);

// app folders
define('APP',         ROOT . 'app'         . DS);
define('CONFIG',      APP  . 'config'      . DS);
define('CONTROLLERS', APP  . 'Controllers' . DS);
define('MODELS',      APP  . 'Models'      . DS);
define('SYS',         APP  . 'System'      . DS);
define('HELPERS',     SYS  . 'helpers'     . DS);
define('VIEWS',       APP  . 'resources'   . DS . 'views'     . DS);
define('CACHE',       APP  . 'storage'     . DS . 'cache'     . DS);
define('LANG',        APP  . 'storage'     . DS . 'languages' . DS);

// PUBLIC FOLDERS for php
define('PUB_FOLDER',    ROOT           . 'public' . DS);
define('ASSETS_FOLDER', PUB_FOLDER     . 'assets' . DS);
define('CSS_FOLDER',    ASSETS_FOLDER  . 'css'    . DS);
define('JS_FOLDER',     ASSETS_FOLDER  . 'js'     . DS);
define('img_FOLDER',    ASSETS_FOLDER  . 'img'    . DS);

// public folders http
define('PUB',    URL      . 'public'   . "/");
define('ASSETS', URL      . BASE_URI   . 'assets' . "/");
define('CSS',    ASSETS   . 'css'      . "/");
define('JS',     ASSETS   . 'js'       . "/");
define('img',    ASSETS   . 'img'      . "/");

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'doctor_tech');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_EMULATE_PREPARES => false
]);