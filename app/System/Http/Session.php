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

 class Session
 {
    /**
     * initilise sesion method
     */
    public function init()
    {
    if(session_status() == PHP_SESSION_NONE) session_start();
    }
 }