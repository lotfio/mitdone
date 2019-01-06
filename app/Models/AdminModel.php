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
use MITDone\Database\Select;

class AdminModel extends Model
{
    public function login()
    {
        $errors = [];

        if(!validate()->string(post('phone'))) $errors[]  = "Wrong phone number";
        if(!validate()->string(post('passwd'))) $errors[] = "Wrong password";
        if(!validate()->validateCSRF()) $errors[] = "error CSRF";
        
        $phone  = validate()->string(post('phone'));
        $passwd = validate()->string(post('passwd'));
 
        if(empty($errors))
        {
            $find = $this->select->allFrom('users', [
                "phone"    => $phone,
                "password" => SHA1($passwd)
            ]);
            
            if($find)
            {
                $user = $find[0];
                Session::set(AUTH_SESSION_NAME, $user->id);
                Session::unset(CSRF);
                Session::regenerate(); // prevent session fixation
                return redirect("admin/home");
            }
            $errors[] = tr(10);
        }

        return view('admin/login', $errors);
    }
}