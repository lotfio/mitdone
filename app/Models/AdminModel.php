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
    public function login($phone, $passwd)
    {
        $sql  = "SELECT * FROM users WHERE phone = ? and password = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$phone, SHA1($passwd)]);
        
        if($stmt->rowCount() == 1)
        {
            return 1;
        }

        return 0;

    }
}