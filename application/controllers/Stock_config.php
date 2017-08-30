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

use Stock\Almacen_sap;
use Stock\Tipoalmacen_sap;

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
		$this->lang->load('stock');

		$this->set_menu_modulo([
			'almacen_sap' => [
				'url'   => $this->router->class . '/listado/almacen_sap',
				'texto' => lang('stock_config_menu_alm'),
				'icon'  => 'home',
			],
			'tipoalmacen_sap' => [
				'url'   => $this->router->class . '/listado/tipoalmacen_sap',
				'texto' => lang('stock_config_menu_tipalm'),
				'icon'  => 'th',
			],
			'clasifalmacen_sap' => [
				'url'   => $this->router->class . '/listado/clasifalmacen_sap',
				'texto' => lang('stock_config_menu_clasifalm'),
				'icon'  => 'th',
			],
			'tipo_clasifalm' => [
				'url'   => $this->router->class . '/listado/tipo_clasifalm',
				'texto' => lang('stock_config_menu_tipo_clasifalm'),
				'icon'  => 'th',
			],
			'proveedor' => [
				'url'   => $this->router->class . '/listado/proveedor',
				'texto' => lang('stock_config_menu_proveedores'),
				'icon'  => 'shopping-cart',
			],
			'usuario_sap' => [
				'url'   => $this->router->class . '/listado/usuario_sap',
				'texto' => lang('stock_config_menu_usuarios_sap'),
				'icon'  => 'user',
			],
			'almacenes_no_ingresados' => [
				'url'   => $this->router->class . '/almacenes_no_ingresados',
				'texto' => lang('stock_config_menu_alm_no_ing'),
				'icon'  => 'home',
			],
			'clase_movimiento' => [
				'url'   => $this->router->class . '/listado/clase_movimiento',
				'texto' => lang('stock_config_menu_cmv'),
				'icon'  => 'th',
			],
			'tipo_movimiento' => [
				'url'   => $this->router->class . '/listado/tipo_movimiento',
				'texto' => lang('stock_config_menu_tipos_movs'),
				'icon'  => 'th',
			],
			'tipo_movimiento_cmv' => [
				'url'   => $this->router->class . '/listado/tipo_movimiento_cmv',
				'texto' => lang('stock_config_menu_tipos_movs_cmv'),
				'icon'  => 'th',
			],
		]);
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
/* End of file stock_config.php */
/* Location: ./application/controllers/stock_config.php */
