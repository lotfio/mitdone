<?php namespace Models;

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
use MITDone\App\Model;
use MITDone\Http\Session;

class AdminModel extends Model
{
    /**
     * Admin login method
     *
     * @return void
     */
    public function login()
    {
        $data['title']  = tr(17);
        $data['errors'] = [];

        if(!validate()->string(post('phone')))   $data['errors'][] = tr(14);
        if(!validate()->string(post('passwd')))  $data['errors'][] = tr(15);
        if(!validate()->validateCSRF())          $data['errors'][] = tr(16);
        
        $phone  = validate()->string(post('phone'));
        $passwd = validate()->string(post('passwd'));
 
        if(empty($data['errors']))
        {
            $find = $this->select->from('users','*', [
                "phone    | = " => $phone . '| and',
                "password | = " => SHA1($passwd)
            ]);
            
            if($find)
            {
                $user = $find[0];
                Session::set(AUTH_SESS_NAME, $user->id);
                Session::unset(CSRF);
                Session::regenerate(); // prevent session fixation
                return redirect("admin/home");
            }
            $data['errors'][] = tr(10);
        }

        return view('admin/login', $data);
    }

    /**
     * Geth authennticated user data
     *
     * @return void
     */
    public function authUser()
    {
        $auth = $this->select->from('users','*',[
            "id | = " => Session::get(AUTH_SESS_NAME)
        ]);

        unset($auth[0]->password); // unset pass
        return $auth;
    }
}