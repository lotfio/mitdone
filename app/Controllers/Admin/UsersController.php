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
use Models\{UsersModel};

class UsersController extends Controller
{
    private $users;

    /**
     * controller auth check
     */
    public function __construct()
    {
        auth()->notLogged('admin/login'); // check login 

        $this->users  = new UsersModel;
    }


    public function index()
    {
        $data['title']    = tr(25);
        $data['allUsers'] = $this->users->all();

        return view('admin/users', $data);
    }

    /**
     * show users method
     *
     * @param integer $id
     * @return void
     */
    public function show($id = 0)
    {
        
        return view('admin/users.show');
    }
}
    