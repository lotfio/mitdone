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
use Models\AdminModel;

class LoginController extends Controller
{
    /**
     * show login form method
    */
    public function showLoginForm()
    {
        return view('admin/login');
    }

    /**
     * proceed login form
     */
    public function loginDo()
    {
        $login = new AdminModel;
        return $login->login();       
    }
}