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

class ProfileController extends Controller
{

    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login 
    }

    /**
     * show login form method
    */
    public function index()
    { 
        $data['title'] = tr(18);
        return view('admin/profile', $data);
    }
}