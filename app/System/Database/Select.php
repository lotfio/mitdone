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
     * count rows function
     *
     * @param [type] $tbl
     * @param [type] $where
     * @return void
     */
    public function countRows($tbl, $where = NULL)
    {
        $sql  = "SELECT COUNT(*) FROM $tbl $where";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    /**
     * Undocumented function
     *
     * @param [type] $tbl
     * @param [type] $where
     * @return void
     */
    public function countLast7daysRows($tbl, $cond = NULL)
    {
        $sql  = "SELECT COUNT(*) FROM $tbl WHERE created_at >= NOW() + INTERVAL -7 DAY AND created_at <  NOW() + INTERVAL  0 DAY $cond";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function from($tbl, $sel, $cond = null, $cond2 = NULL)
    {
        $binds = NULL;
        $sql = "SELECT ";
        if (is_array($sel)) // an array of selection
        {
            if (count($sel) > 1) {
                for ($i = 0; $i < count($sel) - 1; $i++) {
                    $sql .= $sel[$i] . ", ";
                }
                $sql .= $sel[count($sel) - 1] . " FROM `$tbl` ";
                if (!$cond) $sql .= $cond2;
            } else {
                $sql .= $sel[0] . " FROM `$tbl` ";
                if (!$cond) $sql .= $cond2;
            }
        } else { // not an array
            // if coma separated string
            if (preg_match("#\,#", $sel)) {
                $selection = explode(",", $sel);
                for ($i = 0; $i < count($selection) - 1; $i++) {
                    $sql .= $selection[$i] . ", ";
                }
                $sql .= $selection[count($selection) - 1] . " FROM `$tbl` ";
                if (!$cond) $sql .= $cond2;
            } else {
                // only one selection
                $sql .= $sel . " FROM `$tbl` ";
                if (!$cond) $sql .= $cond2;
            }
        }
        /**
         * if is set condition
         * else select without condition
         */
        if (isset($cond) && is_array($cond)) {
            $keys  = array_keys($cond);
            $values = array_values($cond);
            $sql .= "WHERE ";
            if (count($cond) > 1) // multiple conditions
            {
                foreach ($keys as $key) {
                    $k[] = explode("|", $key);
                }
                foreach ($values as $value) {
                    $v[] = explode("|", $value);
                }
                for ($i = 0; $i < count($k); $i++) {
                    $nph[] = trim($k[$i][0]);
                    $val[] = trim($v[$i][0]);
                }
                $nph = array_map(function ($elem) {
                    return ":" . $elem . rand();
                }, $nph);
                for ($i = 0; $i < count($k) - 1; $i++) {
                    $sql .= trim($k[$i][0]) . " " . trim($k[$i][1]) . " " . $nph[$i] . $v[$i][1] . " ";
                }
                $sql .= trim($k[count($k) - 1][0]) . " " . trim($k[count($k) - 1][1]) . " " . $nph[count($nph) - 1] . " " . $cond2;
                $binds = array_combine($nph, $val);
            } else { // one condition  one array element
                foreach ($keys as $key) {
                    $k[] = explode("|", $key);
                }
                foreach ($values as $value) {
                    $v[] = explode("|", $value);
                }
                for ($i = 0; $i < count($k); $i++) {
                    $nph[] = trim($k[$i][0]);
                    $val[] = trim($v[$i][0]);
                }
                $nph = array_map(function ($elem) {
                    return ":" . $elem . rand();
                }, $nph);
                $sql .= trim($k[0][0]) . " " . trim($k[0][1]) . " " . $nph[0] . " " . $cond2;
                $binds = array_combine($nph, $val);
            }
        }
        $stmt = $this->con->prepare($sql);
        $stmt->execute($binds);
        return $stmt->rowCount() > 0 ? $stmt->fetchAll() : 0;
    }

}