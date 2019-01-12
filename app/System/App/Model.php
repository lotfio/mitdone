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
use MITDone\Database\Select;
use MITDone\Database\Update;
use MITDone\Database\Connection;
use PDO;

class Model
{
    public function __construct()
    {
        $this->select = new Select;
        $this->update = new Update;
        $this->con    = Connection::init();
    }
}