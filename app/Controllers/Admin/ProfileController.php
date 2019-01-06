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

class ProfileController extends Controller
{
    
    private $admin;

    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login 

        /**
         * if authenticated admin
         */
        $model = new AdminModel();
        $this->admin = is_array($model->authUser()) ? $model->authUser()[0] : NULL;
    }

    /**
     * show login form method
    */
    public function index()
    { 
        $data['title'] = tr(18);
        $data['admin'] = $this->admin;
        return view('admin/profile', $data);
    }
}