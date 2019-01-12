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

    public function all($page)
    {
        $current = isset($page) ? (int) $page : 1;
        $perpage = 10;
        $next = $current + 1;
        $prev = $current - 1;
        $pages = (int) ceil($this->counRequests() / $perpage);
        $start = ($current > 1) ? ($current * $perpage) - $perpage : 0;

        $data['pagination'] = [
            "current" => $current,
            "prev"    => $prev,
            "next"    => $next,
            "total"   => $pages
        ];

        $data['orders'] = $this->select->custom(
            'ordersrequests.*,
            users.id as u_id, users.name as u_name,
            towns.id as t_id, towns.name as t_name, 
            cities.id as c_id, cities.name as c_name', 'ordersrequests', 
            "INNER JOIN users on ordersrequests.user_id = users.id
            INNER JOIN towns on ordersrequests.town = towns.id
            INNER JOIN cities on ordersrequests.city = cities.id

        WHERE deleted_at IS NULL LIMIT {$start},{$perpage}");
        return $data;
    }
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

    
    public function getById($id)
    {
        $id = (int) $id ?? 0;

        return $this->select->from("ordersrequests","*", [
            "id | = " => $id // no need for deleted_at since all selected records o, table are foltered
        ]);
    }

    public function deleteById($id)
    {
        if($this->getById($id))
        {
            return $this->update->set('ordersrequests',["deleted_at" => date("Y-m-d H:i:s")], [
                "id | = " => $id
            ]);
        }

        return 0;
    }
}