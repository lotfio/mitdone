<?php use MITDone\Aven\Facades\Aven as Router;

require 'autoload.php';


// autoload routes;
(function(){ require APP . 'routes/web.php'; require APP . 'routes/api.php';})();

try{
    
    MITDone\Aven\Facades\Aven::init();

}catch(\Exception $e)
{
    if(env('APP_ENV') == 'DEV') dd($e);

    switch($e->getCode())
    {
        case 404: view("errors/404");
        break;
        case 500: view("errors/500");
        break;
        default:  view("errors/500");
        break;
    }    
}