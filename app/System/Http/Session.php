<?php namespace MITDone\Http;

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

 class Session
 {
   /**
    * set session method
    *
    * @param  string $key
    * @param  mixed  $value
    * @return mixed
    */
   public static function set($key, $value)
   {
      return $_SESSION[$key] = $value;
   }

   /**
    * set session method
    *
    * @param  string $key
    * @param  mixed  $value
    * @return mixed
    */
   public static function unset($key)
   {
      if(isset($_SESSION[$key]))
      {
         unset($_SESSION[$key]);
         return true;
      }
      return false;
   }

  /**
   * get session method
   *
   * @param  string $key
   * @param  mixed  $value
   * @return mixed
   */
   public static function get($key)
   {
      return $_SESSION[$key] ?? NULL;
   }

   /**
    * regenerate session id to prevent fixation method
    *
    * @param  string $key
    * @param  mixed  $value
    * @return mixed
    */
   public static function regenerate()
   {
      session_regenerate_id(true);
   }

   /**
    * set session name method
    *
    * @param  string $key
    * @param  mixed  $value
    * @return mixed
    */
   public static function name($value)
   {
      return session_name($value);
   }
 }