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

define('BASE_URI', '/doctortech-lotfio/'); // to be removed

define('ROOT', dirname(dirname(__DIR__)) . DS);
define('APP', ROOT . 'app' . DS);
define('PUB', ROOT . 'public' . DS);

define('CONFIG',      APP . 'config' . DS);
define('CONTROLLERS', APP . 'Controllers' . DS);
define('MODELS',      APP . 'Models' . DS);
define('SYS',         APP . 'System' . DS);
define('HELPERS',     SYS . 'helpers'. DS);
define('VIEWS',       APP . 'resources' . DS . 'views' . DS);

define('CACHE', APP . 'storage' . DS . 'cache' . DS);