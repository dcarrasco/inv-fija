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
 * Clase Controller Configuracion ACL (access control list)
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Acl_config extends Orm_controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo  = 'acl_config';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('acl');

		$this->arr_menu = array(
			'usuarios' => array(
				'url'   => 'usuarios',
				'texto' => $this->lang->line('acl_config_menu_usuarios'),
				'icon'  => 'user',
			),
			'aplicaciones' => array(
				'url'   => 'aplicaciones',
				'texto' => $this->lang->line('acl_config_menu_aplicaciones'),
				'icon'  => 'folder-o',
			),
			'roles' => array(
				'url'   => 'roles',
				'texto' => $this->lang->line('acl_config_menu_roles'),
				'icon'  => 'server',
			),
			'modulos' => array(
				'url'   => 'modulos',
				'texto' => $this->lang->line('acl_config_menu_modulos'),
				'icon'  => 'list-alt',
			),
		);

	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve <option> para poblar <select> con tipos de almacen
	 *
	 * @param  string $id_app ID de la app para filtrar modulos a desplegar
	 * @return void
	 */
	public function get_select_modulo($id_app = '')
	{
		$modulo = new modulo;

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($modulo->find('list', array('conditions' => array('id_app' => $id_app)))));
	}


}
/* End of file acl_config.php */
/* Location: ./application/controllers/acl_config.php */