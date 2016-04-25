<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Hook Funcionalidad a ejecutar en todos los controladores
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class MY_Hooks {

	/**
	 * Funciones y operaciones ejecutadas con todos los controladores
	 *
	 * @return void
	 */
	public function acl_hook()
	{
		$ci =& get_instance();
		$ci->load->model('acl_model');
		$ci->config->load('inv-fija');

		$whitelist_classes = array('login', 'migration');

		$class = $ci->router->fetch_class();

		$llave_modulo = property_exists($class, 'llave_modulo') ? $ci->llave_modulo : '';

		if ( ! in_array($class, $whitelist_classes) AND  ! $ci->acl_model->autentica_modulo($llave_modulo))
		{
			redirect('login/');
		}

		if (ENVIRONMENT !== 'production')
		{
			if ( ! $ci->input->is_ajax_request() AND strpos(uri_string(), 'ajax') === FALSE AND ! $ci->input->is_cli_request())
			{
				$ci->output->enable_profiler(TRUE);
			}
		}

		// log información
		log_message('debug', 'REQUEST USER: ' . $ci->session->user . ', URI: '. $ci->uri->uri_string());
	}

}