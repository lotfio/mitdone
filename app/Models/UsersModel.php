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

class UsersModel extends Model
{

    public function all()
    {
        return $this->select->allFrom('users');
    }
    /**
     * get all users
     *
     * @return void
     */
    public function countAllUsers()
    {
        return count($this->select->allFrom('users'));
    }

    /**
     * new users
     *
     * @return void
     */
    public function countLastSevenDaysUsers()
    {
        return count(($this->select->from('*','users', 'WHERE created_at >= NOW() + INTERVAL -7 DAY AND 
        created_at <  NOW() + INTERVAL  0 DAY'
        )));
    }

    public function countEngineers()
    {
        return count($this->select->allFrom('users', ["type"=>"engineer"]));
    }

    public function countLastSevenDaysEngineers()
    {
        return count(($this->select->from('*','users', 'WHERE type="engineer" AND created_at >= NOW() + INTERVAL -7 DAY AND 
        created_at <  NOW() + INTERVAL  0 DAY'
        )));
    }

}