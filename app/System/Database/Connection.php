<?php namespace MITDone\Database;

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
use PDO;

class Connection
{
    private static $ins;

    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    private function __construct(){}
    private function __destruct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function init()
    {
        if(!isset(SELF::$ins))
        {
            SELF::$ins = new PDO(
                "mysql:host=" . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
                env('DB_USER'),
                env('DB_PASS'),
                SELF::$options
            );
        }
        return SELF::$ins;
    }
}