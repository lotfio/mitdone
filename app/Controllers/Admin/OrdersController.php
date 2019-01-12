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
use Models\{UsersModel, OrdersModel};

class OrdersController extends Controller
{
    private $users;
    private $orders;

    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login

        $this->users   = new UsersModel;
        $this->orders  = new OrdersModel;
    }


    /**
     * orders index page
     * 
     * @param  integer $page 
     * @return        
     */
    public function index($page = 1)
    {

 		$data['title']  = "Orders";
 		$data['orders'] = $this->orders->all($page); 

 		return view('admin/orders', $data);
    }

    /**
     * delete order
     * 
     * @param  integer $id
     * @return   
     */
    public function delete($id = 0)
    {
        $id = (int) $id ?? 0;
        
        return $this->orders->deleteById($id);
    }


    /**
     * show order method
     * 
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function show($id = 0)
    {
        $id = (int) $id ?? 0;
        $data['title'] = "Edit Order";

        $data['order'] = $this->orders->getById($id)[0];

        return view('admin/orders.show', $data);
    }

    /**
     * dediit order method
     * 
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function edit($id = 0)
    {
        $id = (int) $id ?? 0;
        $data['title'] = "Edir Order";

        return view('admin/orders.edit');
    }

}