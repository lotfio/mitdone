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
     * count orders requests
     *
     * @return void
     */
    public function counRequests()
    {
        return $this->select->countRows('ordersrequests');
    }

    /**
     * count last orders requests
     *
     * @return void
     */
    public function  countLastSevenDaysRequests()
    {
        return $this->select->countLast7daysRows('ordersrequests');
    }
}