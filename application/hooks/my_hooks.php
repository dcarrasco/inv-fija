<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Hooks {

	public function acl_hook()
	{
		$CI_local =& get_instance();

		$CI_local->load->model('acl_model');
		$CI_local->config->load('inv-fija');

		$class = $CI_local->router->fetch_class();

		$llave_modulo = property_exists($class, 'llave_modulo') ? $CI_local->llave_modulo : '';

		if ($class != 'login' AND $class != 'migration')
		{
			if (! $CI_local->acl_model->autentica($llave_modulo))
			{
				redirect('login/');
			}
		}

		if (ENVIRONMENT != 'production')
		{
			//$CI_local->output->enable_profiler(TRUE);
		}

		// log información
		log_message('debug', 'Request User: ' . $CI_local->session->user . ', URI: '. $CI_local->uri->uri_string());
	}

}