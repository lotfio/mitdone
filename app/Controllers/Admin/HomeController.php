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
use Models\{UsersModel,AdminModel, OrdersModel};

class HomeController extends Controller
{
    private $model;
    private $admin;
    private $user;


    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login 

        /**
         * if authenticated admin
         */
        $this->model   = new AdminModel();
        $this->user    = new UsersModel();
        $this->orders  = new OrdersModel();
        $this->admin = is_array($this->model->authUser()) ? $this->model->authUser()[0] : NULL;
    }

    /**
     * show login form method
    */
    public function index()
    { 
        $data['title'] = tr(18);
        $data['admin'] = $this->admin;

        $data['countAllUsers']                     = $this->user->countAllUsers();
        $data['countLastSevenDaysUsers']           = $this->user->countLastSevenDaysUsers();
        $data['countOrdersRequests']               = $this->orders->counRequests();
        $data['countLastSevenDaysOrdersRequests']  = $this->orders->countLastSevenDaysRequests();
        $data['countEngineers']                    = $this->user->countEngineers();
        $data['countLastSevenDaysEngineers']       = $this->user->countLastSevenDaysEngineers();

        return view('admin/index', $data);
    }
}