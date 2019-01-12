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

    /**
     * getting user orders
     *
     * @param  int $id
     * @return void
     */
    public function userOrders($id)
    {
        $sql  = "SELECT * FROM ordersrequests INNER JOIN towns ON ordersrequests.town = towns.id
        INNER JOIN cities ON ordersrequests.city = cities.id WHERE ordersrequests.user_id=?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id]) ? $stmt->fetchAll() : 0;
    }
}