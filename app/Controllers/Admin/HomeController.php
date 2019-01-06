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

class HomeController extends Controller
{
    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login');
    }

    /**
     * show login form method
    */
    public function index()
    { 
        $data['title'] = tr(18);
        return view('admin/index', $data);
    }
}