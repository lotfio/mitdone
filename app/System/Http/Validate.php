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
    /**
     * validate  email method
     */
    public function email($inp)
    {
        return filter_var($inp, FILTER_VALIDATE_EMAIL);
    }

    /**
     * validate string method
     */
    public function string($inp)
    {
        return filter_var($inp, FILTER_SANITIZE_STRING);
    }

    /**
     * validate integer method
     */
    public function int($inp){

        return filter_var($inp, FILTER_VALIDATE_INT);
    }
    
    /**
     * validate phone method 
     */
    public function phone($inp)
    {
        return filter_var($inp, FILTER_VALIDATE_INT);
    }

    /**
     * generate csrf input
     *
     * @return void
     */
    public function generateCSRF()
    {
        return "<input type='hidden' name='".CSRF."' value='". Session::set(CSRF, SHA1(rand(0, 10000000))) ."'";
    }

    /**
     * validate csrf
     *
     * @return void
     */
    public function validateCSRF()
    {
        if(Session::get(CSRF) && Session::get(CSRF) === post(CSRF))
        {
            return true;
        }
        return false;
    }
}