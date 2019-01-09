<?php namespace MITDone\Http;

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

 class Cookie
 {
     public function set($name, $value, $exp, $path = "/", $secure = FALSE, $httpOnly = FALSE)
     {

     }

     public function get($name)
     {
        if(isset($_COOKIE[$name])) return $_COOKIE[$name];
        return false;
     }
 }