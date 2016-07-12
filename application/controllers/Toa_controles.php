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
			'materiales_consumidos' => array(
				'url'   => $this->router->class . '/materiales_consumidos',
				'texto' => $this->lang->line('toa_controles_materiales_consumidos'),
				'icon'  => 'tv'
			),
			'materiales' => array(
				'url'   => $this->router->class . '/materiales',
				'texto' => $this->lang->line('toa_controles_materiales'),
				'icon'  => 'file-text-o'
			),
			'asignaciones' => array(
				'url'   => $this->router->class . '/asignaciones',
				'texto' => $this->lang->line('toa_controles_asignaciones'),
				'icon'  => 'archive'
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
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_consumos_validation);
		$this->form_validation->run();

		$empresa_toa    = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'consumos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_model->combo_movimientos_consumo,
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_consumo,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/tecnicos',
			'anomes'               => set_value('mes'),
			'control'              => $this->toa_model->control_tecnicos(set_value('empresa'), set_value('mes'), set_value('filtro_trx'), set_value('dato')),
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
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_consumos_validation);
		$this->form_validation->run();

		$empresa_toa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'asignaciones'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_model->combo_movimientos_asignacion,
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_asignacion,
			'url_detalle_dia'      => 'toa_asignaciones/ver_asignaciones/tecnicos',
			'anomes'               => set_value('mes'),
			'control'              => $this->toa_model->control_asignaciones(set_value('empresa'), set_value('mes'), set_value('filtro_trx'), set_value('dato')),
		);

		app_render_view('toa/controles', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de los materiales consumidos
	 *
	 * @return void
	 */
	public function materiales_consumidos()
	{
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_consumos_validation);
		$this->form_validation->run();

		$empresa_toa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'materiales_consumidos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_model->combo_movimientos_consumo,
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_materiales_consumidos,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/material',
			'anomes'               => set_value('mes'),
			'control'              => $this->toa_model->materiales_consumidos(set_value('empresa'), set_value('mes'), set_value('filtro_trx'), set_value('dato')),
		);

		app_render_view('toa/control_materiales_consumidos', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega stock de la contrata
	 *
	 * @return void
	 */
	public function stock()
	{
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_stock_empresa_validation);
		$this->form_validation->run();

		$empresa_toa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'stock'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_stock,
			'url_detalle_dia'      => 'toa_controles/detalle_stock',
			'anomes'               => set_value('mes'),
			'stock_almacenes'      => $this->toa_model->stock_almacenes(set_value('empresa'), set_value('mes'), set_value('dato')),
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
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_stock_tecnicos_validation);
		$this->form_validation->run();

		$empresa_toa = new Empresa_toa();

		$datos = array(
			'menu_modulo'          => array('menu' => $this->_arr_menu, 'mod_selected' => 'stock_tecnicos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_stock,
			'combo_dato_mostrar'   => $this->toa_model->combo_mostrar_stock_tecnicos,
			'url_detalle_dia'      => 'toa_controles/detalle_stock_tecnico',
			'anomes'               => set_value('mes'),
			'stock_tecnicos'       => $this->toa_model->stock_tecnicos(set_value('empresa'), set_value('mes'), set_value('dato')),
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
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->controles_materiales_validation);
		$this->form_validation->run();

		$empresa_toa = new Empresa_toa();
		$tipo_trabajo = new Tipo_trabajo_toa();


		$datos = array(
			'menu_modulo'              => array('menu' => $this->_arr_menu, 'mod_selected' => 'materiales'),
			'combo_empresas'           => $empresa_toa->find('list'),
			'combo_tipos_trabajo'      => array_merge(array('000' => 'Todos'), $tipo_trabajo->find('list', array('opc_ini' => FALSE))),
			'combo_dato_desplegar'     => $this->toa_model->combo_unidades_materiales_tipo_trabajo,
			'url_detalle_dia'          => 'toa_consumos/detalle_peticion',
			'anomes'                   => set_value('mes'),
			'materiales_tipos_trabajo' => $this->toa_model->materiales_tipos_trabajo(set_value('empresa'), set_value('mes'), set_value('tipo_trabajo'), set_value('dato')),
		);

		app_render_view('toa/controles_materiales_tipo_trabajo', $datos);
	}


	// --------------------------------------------------------------------

}
/* End of file Toa_controles.php */
/* Location: ./application/controllers/Toa_controles.php */
