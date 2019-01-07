<?php namespace MITDone\Database;

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
use PDO;

 class Select
 {
    /**
     * connection
     *
     * @var resource
     */
    private $con;

    public function __construct()
    {
        $this->con = Connection::init();
    }

    /**
     * select from all function
     *
     * @param  string $tableName
     * @param  string $where
     * @return mixed
     */
    public function allFrom($tableName, $where = NULL)
    {

    $sql = "SELECT * FROM $tableName";
    $arr = [];

    if(is_array($where)){
        
        $sql .= " WHERE ";

        foreach($where as $key => $value) {$arr[":".$key] = $value; } // adding place holder to keys
                
        if(\count($arr) > 1) // if many conditions 
        {
            foreach($arr as $key => $value)
            {
                $sql .= ltrim($key, ':') ."=". $key . " AND ";
            }

            $sql = \rtrim($sql, ' AND '); // if only one remove and

        }else{
            foreach($where as $key => $value)
            {
                $sql .= $key ."=:". $key;
            }
        }  
    }

    $stmt = $this->con->prepare($sql);
    $stmt->execute($arr);
    
    if($stmt->rowCount() > 0)
    {
        return $stmt->fetchAll();
    }

    return false;

    }

    public function from($sel, $tbl, $where = NULL)
    {
        $sql = "SELECT $sel FROM $tbl  $where";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0)
        {
            return $stmt->fetchAll();
        }
        return [];
    }
 }