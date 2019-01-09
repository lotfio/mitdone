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

/**
 * TODO to be updated based on the session language
 */
if(!function_exists('tr'))
{
    function tr($word)
    {
        $lang = $_SESSION['lang'] ?? NULL; // session class cannot be used since we cal this func before loader loads classes

        switch($lang)
        {
            case "ar" :  $laguageFile = LANG . $lang . DS . 'app.txt'; break;
            case "en" :  $laguageFile = LANG . $lang . DS . 'app.txt'; break;
            case "fr" :  $laguageFile = LANG . $lang . DS . 'app.txt'; break;
            default   :  $laguageFile = LANG . DEF_LANG   . DS . 'app.txt'; break;
        }
        
        if(!file_exists($laguageFile))  throw new Exception('Error Language File $laguageFile Not Found', 404);
        $lang = file($laguageFile);

        $word = $lang[$word - 1] ?? " Word Not Found";
        return rtrim($word); // remove white spaces on the right side
    }
}

/**
 *  post function
 *  
 */
if(!function_exists('redirect'))
{
    function redirect($to, $time = 2)
    {
      return MITDone\Http\Redirect::to($to, $time);
    }
}

/**
 *  post function
 *  
 */
if(!function_exists('auth'))
{
    function auth()
    {
      return new MITDone\Http\Auth;
    }
}

if(!function_exists('singleView'))
{
    function singleView($file, $data = NULL)
    {
        $file   = VIEWS . $file . '.php';
        if(!file_exists($file)) throw new \Exception("Error view file $footer not found ! ", 404);
        require_once ($file);
    }
}

if(!function_exists('activeTab'))
{
    function activeTab($menuItem)
    {
        $uri = trim(str_replace(BASE_URI, NULL, $_SERVER['REQUEST_URI']));
        $tab = explode('/', $uri)[1]; // action always;
        return $tab == $menuItem ? 'active' : NULL;
    }
}


/**
 * env function
 * envirenment vars function
 */
if(!function_exists('env'))
{
    function env($val, $default = NULL)
    {
        $file   = ROOT . '.env';
        if(!file_exists($file)) throw new \Exception(".env file $file not found ! ", 404);
        
        $file = array_filter(array_map('trim',file($file))); // read env file and remove white spaces

        $file = array_filter($file, function($elem){ // remove no key values elements
            return preg_match('#\:#', $elem);
        });
        $envVariable = [];   // expload file
        $env         = [];   // set keys and values

        foreach($file as $var)
        {
            $envVariable[] = explode(':', $var);
            for($i = 0; $i < count($envVariable); $i++)
            {
                $env[trim($envVariable[$i][0])] = trim($envVariable[$i][1]);
            }
        }
        return $env[$val] ?? $default;
    }
}

/**
 * escape string to prevent xss
 */
if(!function_exists('e'))
{
    function e($string)
    {
       return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
/**
 * set user image
 */
if(!function_exists('image'))
{
    function image($userImage, $defaultImage = "default-avatar.jpg")
    {
        if($userImage == '' || (!file_exists(PHP_UP_IMG . $userImage))) return UP_IMG . $defaultImage;


        return UP_IMG . $userImage;
    }
}

/**
 * set user image
 */
if(!function_exists('upload'))
{
    function upload()
    {
        return new \MITDone\Http\FileUpload;
    }
}
/**
 * set user image
 */
if(!function_exists('imageResizer'))
{
    function imageResizer($image)
    {
        return new \MITDone\Image\Resizer($image);
    }
}