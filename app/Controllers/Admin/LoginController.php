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

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin/login');
    }

    public function loginDo()
    {
        dd($_POST);
    }
}