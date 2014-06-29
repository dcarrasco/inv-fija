<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class myHooks {

	public function acl_hook()
	{
		$CI_local =& get_instance();
		$RTR =& load_class('Router', 'core');
		$class = $RTR->fetch_class();
		$CI_local->load->model('acl_model');
		$llave_modulo = property_exists($class, 'llave_modulo') ? $CI_local->llave_modulo : '';

		if ($class != 'login')
		{
			$CI_local->acl_model->autentica($llave_modulo);
		}

		if (ENVIRONMENT != 'production')
		{
			$CI_local->output->enable_profiler(TRUE);
		}
	}


}