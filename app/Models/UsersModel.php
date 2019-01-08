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

    /**
     * get all active users method
     * 
     * @return object
     */
    public function all()
    {   
        return $this->select->from('users', '*', ["deleted | = " => "0"]);
    }

    /**
     * count number of all active users
     *
     * @return int
     */
    public function countAllUsers()
    {
        return $this->select->countRows('users', 'WHERE deleted = 0');
    }

    /**
     * count users of last seven days
     *
     * @return   int
     */
    public function countLastSevenDaysUsers()
    {
        return $this->select->countLast7daysRows('users', 'AND deleted = 0');
    }

    /**
     * count users that are engineers.
     * 
     * @return int
     */
    public function countEngineers()
    {
       return $this->select->countRows('users', "WHERE type = 'engineer'");
    }

    /**
     * count new engineers
     * 
     * @return int
     */
    public function countLastSevenDaysEngineers()
    {
        return $this->select->countLast7daysRows('users', "AND type = 'engineer'");
    }

    /**
     * get user by id
     * 
     * @param  int id
     * @return object
     */
    public function getById($id)
    {
        return $this->select->from('users','*', [
            "id      | = " => $id . '| and',
            "deleted | = " => "0"
        ]);
    } 

    /**
     * delete user by id
     * 
     * @param  int id 
     * @return bool
     */
    public function deleteById($id)
    {
        if($this->getById($id)[0]) // foound ! 
        {
            if($this->update->set('users',["deleted" => "1"], ["id | =" => $id])) return 1;
            return 0;
        }

        return 0;
    }
}