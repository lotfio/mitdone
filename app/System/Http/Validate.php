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

class Validate
{
    public function email($inp)
    {
        return filter_var($inp, FILTER_VALIDATE_EMAIL);
    }

    public function string($inp)
    {
        return filter_var($inp, FILTER_SANITIZE_STRING);
    }

    public function int($inp){}
        
    public function phone($inp)
    {
        return filter_var($inp, FILTER_VALIDATE_INT);
    }
}