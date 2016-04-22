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
 * Clase Controller Reporte de consumos TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_controles extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'oknsk82js7s';

	/**
	 * Menu de opciones
	 *
	 * @var  array
	 */
	private $_arr_menu = array();

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toa_model');
		$this->lang->load('toa');

		$this->_arr_menu = array(
			'consumos' => array(
				'url'   => $this->router->class . '/consumos',
				'texto' => $this->lang->line('toa_controles_tecnicos'),
				'icon'  => 'user'
			),
			'asignaciones' => array(
				'url'   => $this->router->class . '/asignaciones',
				'texto' => $this->lang->line('toa_controles_asignaciones'),
				'icon'  => 'archive'
			),
			'materiales' => array(
				'url'   => $this->router->class . '/materiales',
				'texto' => $this->lang->line('toa_controles_materiales'),
				'icon'  => 'file-text-o'
			),
			'stock' => array(
				'url'   => $this->router->class . '/stock',
				'texto' => $this->lang->line('toa_controles_stock'),
				'icon'  => 'signal'
			),
			'stock_tecnicos' => array(
				'url'   => $this->router->class . '/stock_tecnicos',
				'texto' => $this->lang->line('toa_controles_stock_tecnicos'),
				'icon'  => 'truck'
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		return $this->consumos();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de las actuaciones de los técnicos
	 *
	 * @return void
	 */
	public function consumos()
	{
		$empresa = new Empresa_toa();
		$stock_sap_fija = new Stock_sap_fija_model();

		$datos = array(
			'menu_modulo'     => array('menu' => $this->_arr_menu, 'mod_selected' => 'consumos'),
			'combo_empresas'  => $empresa->find('list'),
			'combo_filtro_trx' => $this->toa_model->combo_movimientos_consumo,
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_consumo,
			'control'         => $this->toa_model->control_tecnicos($this->input->get('empresa'), $this->input->get('mes'), $this->input->get('filtro_trx'), $this->input->get('dato')),
			'anomes'          => $this->input->get('mes'),
			'url_detalle_dia' => 'toa_consumos/ver_peticiones/tecnicos',
		);

		app_render_view('toa/controles', $datos);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de las asignaciones de material a los técnicos
	 *
	 * @return void
	 */
	public function asignaciones()
	{
		$empresa = new Empresa_toa();
		$stock_sap_fija = new Stock_sap_fija_model();

		$datos = array(
			'menu_modulo'    => array('menu' => $this->_arr_menu, 'mod_selected' => 'asignaciones'),
			'combo_empresas' => $empresa->find('list'),
			'combo_filtro_trx' => $this->toa_model->combo_movimientos_asignacion,
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_asignacion,
			'control'        => $this->toa_model->control_asignaciones($this->input->get('empresa'), $this->input->get('mes'), $this->input->get('filtro_trx'), $this->input->get('dato')),
			'anomes'         => $this->input->get('mes'),
			'url_detalle_dia' => 'toa_asignaciones/ver_asignaciones/tecnicos',
		);

		app_render_view('toa/controles', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega stock de la contrata
	 *
	 * @return void
	 */
	public function stock()
	{
		$empresa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'stock'),
			'combo_empresas'       => $empresa->find('list'),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_stock,
			'stock_almacenes'      => $this->toa_model->stock_almacenes($this->input->get('empresa'), $this->input->get('mes'), $this->input->get('dato')),
			'anomes'               => $this->input->get('mes'),
			'url_detalle_dia'      => 'toa_controles/detalle_stock',
		);

		app_render_view('toa/controles_stock', $datos);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega stock de los tecnicos de la contrata
	 *
	 * @return void
	 */
	public function stock_tecnicos()
	{
		$empresa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'stock_tecnicos'),
			'combo_empresas'       => $empresa->find('list'),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_stock,
			'stock_tecnicos'       => $this->toa_model->stock_tecnicos($this->input->get('empresa'), $this->input->get('mes'), $this->input->get('dato')),
			'anomes'               => $this->input->get('mes'),
			'url_detalle_dia'      => 'toa_controles/detalle_stock_tecnico',
		);

		app_render_view('toa/controles_stock_tecnicos', $datos);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock de la contrata
	 *
	 * @return void
	 */
	public function detalle_stock($fecha = NULL, $centro_almacen = NULL)
	{
		$datos = array(
			'reporte' => $this->toa_model->detalle_stock_almacen($fecha, $centro_almacen),
		);

		app_render_view('toa/peticiones', $datos);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock del tecnico de la contrata
	 *
	 * @return void
	 */
	public function detalle_stock_tecnico($fecha = NULL, $id_tecnico = NULL)
	{
		$datos = array(
			'reporte' => $this->toa_model->detalle_stock_tecnico($fecha, $id_tecnico),
		);

		app_render_view('toa/peticiones', $datos);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock del tecnico de la contrata
	 *
	 * @return void
	 */
	public function materiales()
	{
		$empresa = new Empresa_toa();
		$tipo_trabajo = new Tipo_trabajo_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'materiales'),
			'combo_empresas'       => $empresa->find('list'),
			'combo_tipos_trabajo'  => array_merge(array('*' => 'Todos'), $tipo_trabajo->find('list')),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_materiales_tipo_trabajo,
			'materiales_tipos_trabajo' => $this->toa_model->materiales_tipos_trabajo($this->input->get('empresa'), $this->input->get('mes'), $this->input->get('tipo_trabajo'), $this->input->get('dato')),
			'anomes'               => $this->input->get('mes'),
			'url_detalle_dia'      => 'toa_consumos/detalle_peticion',
		);

		app_render_view('toa/controles_materiales_tipo_trabajo', $datos);
	}


	// --------------------------------------------------------------------

}
/* End of file Toa_controles.php */
/* Location: ./application/controllers/Toa_controles.php */