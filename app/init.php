<?php use MITDone\Aven\Facades\Aven as Router;

require 'autoload.php';


// autoload routes;
(function(){ require APP . 'routes/web.php';})();

try{
    
    MITDone\Aven\Facades\Aven::init();

}catch(\Exception $e)
{
    die($e->getMessage());
}