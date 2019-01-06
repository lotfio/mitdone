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
        $data   = (object) $data;
        $file   = VIEWS . $file . '.php';
        
        if(!file_exists($file))   throw new \Exception("Error view file $file   not found ! ",  404);
        require_once ($file);
        return;
    }
}