<?php namespace Controllers;

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

use MITDone\App\Controller;
use Models\AdminModel;

class IndexController extends Controller
{
    public function index()
    {
       //echo validate()->generateCSRF();
       echo __CLASS__;
    }
}