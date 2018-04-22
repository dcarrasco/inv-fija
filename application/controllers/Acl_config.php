<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

use Acl\modulo;

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

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'usuario' => ['icon'=>'user'],
		'app'     => ['icon'=>'folder-o'],
		'rol'     => ['icon'=>'server'],
		'modulo'  => ['icon'=>'list-alt'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'acl';

	/**
	 * Namespace de los modelos
	 *
	 * @var string
	 */
	protected $model_namespace = '\\Acl\\';

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
		return $this->output
			->set_content_type('text')
			->set_output(form_print_options(modulo::create()->where('id_app', $id_app)->get_dropdown_list(FALSE)));
	}

}
// End of file Acl_config.php
// Location: ./controllers/Acl_config.php
