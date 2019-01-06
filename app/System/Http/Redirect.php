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

 class Redirect
 {
    public static function to($location)
    {
        header( "location:" . BASE_URI . $location, true, 303);
        exit;
    }

    public static function toWithDelay($location, $time = 2)
    {
        header( "Refresh:$time;".BASE_URI . $location, true, 303);
    }
 }