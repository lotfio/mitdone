<?php namespace MITDone\App;

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

class Model
{
    protected $con;

    public function __construct()
    {
        $this->con = new PDO("mysql:host=" . DB_HOST .";dbname=" . DB_NAME, DB_USER, DB_PASS, DB_OPTIONS);
    }
}