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

class AdminModel extends Model
{
    public function login()
    {
        $errors = [];

        if(!validate()->string(post('phone'))) $errors[]  = "Wrong phone number";
        if(!validate()->string(post('passwd'))) $errors[] = "Wrong password";
        
        $phone  = validate()->string(post('phone'));
        $passwd = validate()->string(post('passwd'));

 
        if(empty($errors))
        {
            $sql  = "SELECT * FROM users WHERE phone = ? and password = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$phone, SHA1($passwd)]);
            
            if($stmt->rowCount() == 1)
            {
                return 1;
            }
            $errors[] = tr(10);
        }

        return view('admin/login', $errors);
    }
}