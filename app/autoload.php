<?php

class Autoload
{
	/**
	 * LoadFrom method
	 *
	 * @param   $dir
	 * @return  void
	 */
	public function loadFrom($dir)
	{
		/**
		 * autoload all config files
		 * from config folder
		 */
		foreach (glob((__DIR__) . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR ."*.php") as $filename)
		{
		    if(!file_exists($filename)) throw new Exception("Error $filename Not found", 404);
		    require_once $filename;
		}
	}

	/**
	 * loadClasses method
	 *
	 * @param   $class called class
	 * @return  void
	 */
	public function loadClasses($class)
	{
		$class = str_replace("\\", DS , ucfirst($class));
		$class = str_replace('MITDone', 'System', $class); // System = MITDone
		$file  = APP . $class . '.php';

		if(!file_exists($file)) throw new Exception("File $file not found ! ", 404);

		require_once $file;
	}

	/**
	 * load method
	 *
	 * @return  void
	 */
	public function load() : void
	{

		$this->loadFrom('config'); // load config files
		$this->loadFrom('System' . DS . 'helpers' . DS); // load helpers functions

		spl_autoload_register(function($class){ // autoload classes on call

			$this->loadClasses($class);
		});
	}
}

(new Autoload)->load();