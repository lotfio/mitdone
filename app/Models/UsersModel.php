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
        return $this->select->from('users', '*');
    }

    /**
     * get all users
     *
     * @return void
     */
    public function countAllUsers()
    {
        return $this->select->countRows('users');
    }

    /**
     * new users
     * 
     */
    public function countLastSevenDaysUsers()
    {
        return $this->select->countLast7daysRows('users');
    }

    public function countEngineers()
    {
       return $this->select->countRows('users', "WHERE type = 'engineer'");
    }

    public function countLastSevenDaysEngineers()
    {
        return $this->select->countLast7daysRows('users', "AND type = 'engineer'");
    }

}