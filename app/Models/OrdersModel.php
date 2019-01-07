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

class OrdersModel extends Model
{

    /**
     * get all orders requests
     *
     * @return void
     */
    public function counRequests()
    {
        return count($this->select->from('*', 'ordersrequests'));
    }

    /**
     * get last orders requests
     *
     * @return void
     */
    public function  countLastSevenDaysRequests()
    {
        return count($this->select->from('*', 'ordersrequests', 'WHERE created_at >= NOW() + INTERVAL -7 DAY AND 
        created_at <  NOW() + INTERVAL  0 DAY'));
    }
}