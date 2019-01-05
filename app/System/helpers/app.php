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
 *  view loader function
 *  load views
 */
if(!function_exists('view'))
{
    function view($file, $data = [])
    {
      $view = new MITDone\App\View;

      return $view->load($file, $data);
    }
}

/**
 *  request function
 *  
 */
if(!function_exists('request'))
{
    function request()
    {
      return new MITDone\Http\Request;
    }
}

/**
 *  post function
 *  
 */
if(!function_exists('post'))
{
    function post($input)
    {
      return request()->post($input);
    }
}

/**
 *  post function
 *  
 */
if(!function_exists('validate'))
{
    function validate()
    {
      return new MITDone\Http\Validate;
    }
}
