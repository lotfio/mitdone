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
use Models\{UsersModel,AdminModel};

class UsersController extends Controller
{
    private $admin;
    private $users;

    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login 

        /**
         * if authenticated admin
         */
        $model = new AdminModel;
        $this->admin = is_array($model->authUser()) ? $model->authUser()[0] : NULL;
        $this->users  = new UsersModel;
    }


    public function index()
    {
        $data['title'] = tr(25);
        $data['admin'] = $this->admin;
        $data['allUsers'] = $this->users->all();

        return view('admin/users', $data);
    }
}
    