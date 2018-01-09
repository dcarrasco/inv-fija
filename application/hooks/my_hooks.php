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

use Acl\Acl;

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

	public function pre_system_hook()
	{
		try
		{
			$dotenv = new Dotenv\Dotenv(BASEPATH.'../application');
			$dotenv->load();
		}
		catch (Exception $e)
		{
			exit($e->getMessage());
		}
	}

	/**
	 * Funciones y operaciones ejecutadas con todos los controladores
	 *
	 * @return void
	 */
	public function acl_hook()
	{
		// redirecciona a https
		// if ( ! isset($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) !== "on")
		// {
		// 	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		// 	redirect($url);
		// 	exit;
		// }

		$ci =& get_instance();
		$ci->config->load('inv-fija');

		$whitelist_classes = ['login', 'migration', 'tests'];

		$class = $ci->router->fetch_class();
		$llave_modulo = property_exists($class, 'llave_modulo') ? $ci->llave_modulo : '';

		// Valida las credenciales del usuario
		if ( ! in_array($class, $whitelist_classes) AND ! Acl::create()->autentica_modulo($llave_modulo))
		{
			redirect('login?redirect_to='.uri_string());
		}

		// despliega el profiler en desarrollo
		if (ENVIRONMENT !== 'production'
			AND ! $ci->input->is_ajax_request()
			AND strpos(uri_string(), 'ajax') === FALSE
			AND ! $ci->input->is_cli_request()
		)
		{
			// $ci->output->enable_profiler(TRUE);
		}

		// log información
		log_message('debug', 'REQUEST USER: ' . $ci->session->user . ', URI: '. $ci->uri->uri_string());
	}

}
