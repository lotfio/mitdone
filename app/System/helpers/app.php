<?php

/**
 * This is application helpers file
 * all helpers functions will be here
 */

/**
 *  dump data and stop execution
 *  usful method for debugging
 */
if(!function_exists('dd'))
{
    function dd($data)
    {
        print('<pre>'); print_r($data); print('</pre>'); die;
    }
}

/**
 *  dump data and stop execution
 *  usful method for debugging
 */
if(!function_exists('view'))
{
    function view($file, $data = [])
    {
      $view = new MITDone\App\View;

      return $view->load($file, $data);
    }
}
