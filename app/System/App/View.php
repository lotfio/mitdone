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

class View
{
    public function load($file, $data = [])
    {
        $data = (object) $data;
        $file = VIEWS . $file . '.php';

        $header = VIEWS . 'tmp' . DS . 'header.php';
        $footer = VIEWS . 'tmp' . DS . 'footer.php';

        if(!file_exists($file))throw new \Exception('Error view file not found');

        require_once $header;
        require $file;
        require_once $footer;
    }
}