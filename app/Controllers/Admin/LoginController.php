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
     * controller auth check
     */
    public function __construct()
    {
        auth()->Logged('admin/home');
    }
    
    /**
     * show login form method
    */
    public function showLoginForm()
    { 
        $data["title"] = tr(17);
        return view('admin/login',$data);
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