<?php namespace Controllers\Admin;

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

use MITDone\App\Controller;

class LogOutController extends Controller
{
    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login');
    }

    public function logOut()
    {
        session_destroy();
        redirect('admin/login', 0);
    }
}