<?php 

use MITDone\Aven\Facades\Aven as Router;

Router::any('/panel/admin/{url}', function(){

    $uri = explode('/', trim($_GET['url']));
    $uri = array_values(array_filter($uri));
    
    if(isset($_GET['url']))
    {
        if($uri[0] == 'api' && $uri[1] == "v2")
        {
            unset($uri[0], $uri[1]);
            $params = array_values($uri);
    
            $api = new Api;
            $api->v2(...$params);
    
            return;
        }
    }

});