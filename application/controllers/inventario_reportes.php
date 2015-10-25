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
 * Clase Controller Reportes de Inventario
 *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_reportes extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'reportes';

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
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('inventario_model');
		$this->lang->load('inventario');

		$this->_arr_menu = array(
			'hoja' => array(
				'url'   => $this->router->class . '/listado/hoja',
				'texto' => $this->lang->line('inventario_menu_reporte_hoja'),
			),
			'material' => array(
				'url'   => $this->router->class . '/listado/material',
				'texto' => $this->lang->line('inventario_menu_reporte_mat'),
			),
			'material_faltante' => array(
				'url'   => $this->router->class . '/listado/material_faltante',
				'texto' => $this->lang->line('inventario_menu_reporte_faltante'),
			),
			'ubicacion' => array(
				'url'   => $this->router->class . '/listado/ubicacion' ,
				'texto' => $this->lang->line('inventario_menu_reporte_ubicacion'),
			),
			'tipos_ubicacion' => array(
				'url'   => $this->router->class . '/listado/tipos_ubicacion',
				'texto' => $this->lang->line('inventario_menu_reporte_tip_ubic'),
			),
			'ajustes' => array(
				'url'   => $this->router->class . '/listado/ajustes',
				'texto' => $this->lang->line('inventario_menu_reporte_ajustes'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return none
	 */
	public function index()
	{
		$this->listado('hoja');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera los diferentes listados de reportes de inventario
	 *
	 * @param  string $tipo   Tipo de reporte
	 * @param  string $param1 Parámetro variable
	 * @return none
	 */
	public function listado($tipo = 'hoja', $param1 = '')
	{
		// define reglas para usar set_value
		$this->form_validation->set_rules($this->inventario_model->reportes_validation);
		$this->form_validation->run();

		$id_inventario = set_value('inv_activo', $this->inventario_model->get_id_inventario_activo());

		$view = 'listado';

		$datos_hoja = $this->inventario_model->get_reporte($tipo, $id_inventario, set_value('order_by'), set_value('order_sort', 'ASC'), set_value('incl_ajustes'), set_value('elim_sin_dif'), $param1);
		$arr_campos = $this->inventario_model->get_campos_reporte($tipo);

		$data = array(
			'menu_modulo'       => array('menu' => $this->_arr_menu, 'mod_selected' => $tipo),
			'reporte'           => $this->app_common->reporte($arr_campos, $datos_hoja),
			'tipo_reporte'      => $view,
			'nombre_reporte'    => $tipo,
			'filtro_dif'        => '',
			'titulo_modulo'     => 'Reportes',
			'combo_inventarios' => $this->inventario_model->get_combo_inventarios(),
			'inventario_activo' => $this->inventario_model->get_id_inventario_activo(),
			'id_inventario'     => $id_inventario,
		);

		app_render_view('inventario/reporte', $data);
	}



}
/* End of file inventario_reportes.php */
/* Location: ./application/controllers/inventario_reportes.php */