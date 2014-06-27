<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class myHooks {

	public function pre_controller()
	{
		$RTR =& load_class('Router', 'core');
		$class = $RTR->fetch_class();
		$e = new $class;
		var_dump($e);
	}


}