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

 class Request
 {
     public function post($input)
     {
         if(isset($_POST[$input])) return $_POST[$input];
         return false;
     }
 }