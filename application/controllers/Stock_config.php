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

		$this->arr_menu = array(
			'almacen_sap' => array(
				'url'   => $this->router->class . '/listado/almacen_sap',
				'texto' => $this->lang->line('stock_config_menu_alm'),
				'icon'  => 'home',
			),
			'tipoalmacen_sap' => array(
				'url'   => $this->router->class . '/listado/tipoalmacen_sap',
				'texto' => $this->lang->line('stock_config_menu_tipalm'),
				'icon'  => 'th',
			),
			'clasifalmacen_sap' => array(
				'url'   => $this->router->class . '/listado/clasifalmacen_sap',
				'texto' => $this->lang->line('stock_config_menu_clasifalm'),
				'icon'  => 'th',
			),
			'tipo_clasifalm' => array(
				'url'   => $this->router->class . '/listado/tipo_clasifalm',
				'texto' => $this->lang->line('stock_config_menu_tipo_clasifalm'),
				'icon'  => 'th',
			),
			'proveedor' => array(
				'url'   => $this->router->class . '/listado/proveedor',
				'texto' => $this->lang->line('stock_config_menu_proveedores'),
				'icon'  => 'shopping-cart',
			),
			'usuario_sap' => array(
				'url'   => $this->router->class . '/listado/usuario_sap',
				'texto' => $this->lang->line('stock_config_menu_usuarios_sap'),
				'icon'  => 'user',
			),
			'almacenes_no_ingresados' => array(
				'url'   => $this->router->class . '/almacenes_no_ingresados',
				'texto' => $this->lang->line('stock_config_menu_alm_no_ing'),
				'icon'  => 'home',
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los almacenes que no están ingresados al sistema
	 *
	 * @return void
	 */
	public function almacenes_no_ingresados()
	{
		$almacen = new Almacen_sap;

		$data = array(
			'menu_modulo' => array('menu' => $this->arr_menu, 'mod_selected' => 'almacenes_no_ingresados'),
			'almacenes'   => $almacen->almacenes_no_ingresados(),
		);

		app_render_view('stock_sap/almacenes_no_ingresados', $data);

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
		$tipoalm = new Tipoalmacen_sap;

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($tipoalm->get_combo_tiposalm($tipo_op)));
	}


}
/* End of file stock_config.php */
/* Location: ./application/controllers/stock_config.php */