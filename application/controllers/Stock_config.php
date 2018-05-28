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

use Stock\Almacen_sap;
use Stock\Tipoalmacen_sap;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Configuracion de stock
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_config extends Orm_controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo  = 'config_stock';

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'almacen_sap'         => ['icon'=>'home'],
		'tipoalmacen_sap'     => ['icon'=>'th'],
		'clasifalmacen_sap'   => ['icon'=>'th'],
		'tipo_clasifalm'      => ['icon'=>'th'],
		'proveedor'           => ['icon'=>'shopping-cart'],
		'usuario_sap'         => ['icon'=>'user'],
		'clase_movimiento'    => ['icon'=>'th'],
		'tipo_movimiento'     => ['icon'=>'th'],
		'tipo_movimiento_cmv' => ['icon'=>'th'],
		'almacenes_no_ingresados' => ['url'=>'{{route}}/almacenes_no_ingresados', 'texto'=>'lang::stock_config_menu_alm_no_ing', 'icon'=>'home'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'stock';

	/**
	 * Namespace de los modelos
	 *
	 * @var string
	 */
	protected $model_namespace = '\\Stock\\';

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
	 * Despliega los almacenes que no están ingresados al sistema
	 *
	 * @return void
	 */
	public function almacenes_no_ingresados()
	{
		app_render_view('stock_sap/almacenes_no_ingresados', [
			'menu_modulo' => $this->get_menu_modulo('almacenes_no_ingresados'),
			'almacenes'   => Almacen_sap::create()->almacenes_no_ingresados(),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve <option> para poblar <select> con tipos de almacen
	 *
	 * @param  string $tipo_op Tipo de operación a filtrar
	 * @return void
	 */
	public function get_select_tipoalmacen($tipo_op = '')
	{
		$this->output
			->set_content_type('text')
			->set_output(form_print_options(Tipoalmacen_sap::create()->get_combo_tiposalm($tipo_op)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve <option> para poblar <select> con tipos de almacen
	 *
	 * @param  string $tipo_op Tipo de operación a filtrar
	 * @return void
	 */
	public function get_select_almacen($tipo_op = '')
	{
		$this->output
			->set_content_type('text')
			->set_output(form_print_options(Almacen_sap::create()->get_combo_almacenes($tipo_op)));
	}

}
// End of file Stock_config.php
// Location: ./controllers/Stock_config.php
