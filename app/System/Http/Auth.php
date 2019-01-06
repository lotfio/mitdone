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

class Auth
{
    /**
     * check authentication method
     */
    public function Logged($action)
    {
        if(Session::get(AUTH_SESS_NAME))  return redirect($action); // if logged
    }

    /**
     * check authentication method
     */
    public function notLogged($action)
    {
        if(!Session::get(AUTH_SESS_NAME))  return redirect($action); // if logged
    }

}
