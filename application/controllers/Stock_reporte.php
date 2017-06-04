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
 * Clase Controller Reportes de stock
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
class Stock_reporte extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'odk9@i2_23';

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

		$this->load->model('reportestock_model');
		$this->lang->load('stock');

		$this->set_menu_modulo(array(
			'permanencia' => array(
				'url'   => $this->router->class . '/listado/permanencia',
				'texto' => $this->lang->line('stock_perm_menu_perm'),
				'icon'  => 'clock-o',
			),
			'mapastock' => array(
				'url'   => $this->router->class . '/mapastock',
				'texto' => $this->lang->line('stock_perm_menu_mapa'),
				'icon'  => 'globe',
				),
			'movhist' => array(
				'url'   => $this->router->class . '/movhist',
				'texto' => $this->lang->line('stock_perm_menu_movs'),
				'icon'  => 'calendar',
			),
			'est03' => array(
				'url'   => $this->router->class . '/est03',
				'texto' => $this->lang->line('stock_perm_menu_est03'),
				'icon'  => 'refresh',
			),
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->listado('permanencia');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar reportes de stock SAP
	 *
	 * @param  string $tipo   Tipo del reporte
	 * @param  string $param1 [description]
	 * @return void
	 */
	public function listado($tipo = 'permanencia', $param1 = '')
	{
		$arr_campos = array();
		$datos_hoja = array();
		$tipoalmacen_sap = new Tipoalmacen_sap;

		$view = 'listado';

		// define reglas de validacion
		$is_form_valid = $this->form_validation->set_rules($this->reportestock_model->permanencia_validation)->run();

		if ($is_form_valid)
		{
			if ($tipo === 'permanencia')
			{
				$arr_campos = $this->reportestock_model->get_campos_reporte_permanencia();
				$datos_hoja = $this->reportestock_model->get_reporte_permanencia($this->reportestock_model->get_config_reporte_permanencia());
			}
		}

		$data = array(
			'menu_modulo'      => $this->get_menu_modulo($tipo),
			'reporte'          => $this->reporte->genera_reporte($arr_campos, $datos_hoja),
			'tipo_reporte'     => $view,
			'nombre_reporte'   => $tipo,
			'filtro_dif'       => '',
			'arr_campos'       => $arr_campos,
			'fecha_reporte'    => $this->reportestock_model->get_fecha_reporte(),
			'combo_tipo_alm'   => $tipoalmacen_sap->get_combo_tiposalm(request('tipo_op', 'MOVIL')),
			'combo_estado_sap' => $this->reportestock_model->combo_estado_sap(),
			'combo_tipo_mat'   => $this->reportestock_model->combo_tipo_mat(),
		);

		app_render_view('stock_sap/reporte', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega el detalle del stock
	 *
	 * @return void
	 */
	public function detalle()
	{
		$detalle_series = $this->reporte->genera_reporte(
			$this->reportestock_model->get_campos_reporte_detalle_series(),
			$this->reportestock_model->get_detalle_series(request())
		);

		$data = array(
			'menu_modulo'        => $this->get_menu_modulo('permanencia'),
			'menu_configuracion' => '',
			'detalle_series'     => $detalle_series,
		);

		app_render_view('stock_sap/detalle_series', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega mapa de monto y cantidades de materiales en bodegas
	 *
	 * @return void
	 */
	public function mapastock()
	{
		$this->form_validation
			->set_rules('sel_centro', '', 'trim')
			->set_rules('sel_treemap_type', '', 'trim')
			->run();

		$centro = request('sel_centro', 'CL03');

		$arr_treemap = $this->reportestock_model->get_treemap_permanencia($centro);
		$arr_treemap_cantidad = $this->reportestock_model->arr_query2treemap('cantidad', $arr_treemap, array('tipo', 'alm', 'marca', 'modelo'), 'cant', 'perm');
		$arr_treemap_valor = $this->reportestock_model->arr_query2treemap('valor', $arr_treemap, array('tipo', 'alm', 'marca', 'modelo'), 'valor', 'perm');

		$data = array(
			'menu_modulo'           => $this->get_menu_modulo('mapastock'),
			'treemap_data_cantidad' => $arr_treemap_cantidad,
			'treemap_data_valor'    => $arr_treemap_valor,
		);

		app_render_view('stock_sap/treemap', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega reporte de movimientos
	 *
	 * @return void
	 */
	public function movhist()
	{
		$this->form_validation->set_rules($this->reportestock_model->movhist_validation)->run();

		$param_tipo_fecha     = request('tipo_fecha', 'ANNO');
		$param_tipo_alm       = request('tipo_alm', 'MOVIL-TIPOALM');
		$param_tipo_mat       = request('tipo_mat', 'TIPO');
		$param_tipo_cruce_alm = request('tipo_cruce_alm', 'alm');
		$param_sort           = request('sort', '+fecha');

		$arr_campos    = $this->reportestock_model->get_campos_reporte_movhist();
		$datos_reporte = $this->reportestock_model->get_reporte_movhist(array(
			'filtros' => array(
				'tipo_fecha'     => $param_tipo_fecha,
				'fechas'         => request('fechas'),
				'cmv'            => request('cmv'),
				'tipo_alm'       => $param_tipo_alm,
				'almacenes'      => request('almacenes'),
				'tipo_cruce_alm' => $param_tipo_cruce_alm,
				'tipo_mat'       => $param_tipo_mat,
				'materiales'     => request('materiales'),
			),
			'orden' => $param_sort,
		));

		$data = array(
			'menu_modulo'      => $this->get_menu_modulo('movhist'),
			'combo_tipo_fecha' => $this->reportestock_model->get_combo_tipo_fecha(),
			'combo_fechas'     => $this->reportestock_model->get_combo_fechas($param_tipo_fecha),
			'combo_cmv'        => $this->reportestock_model->get_combo_cmv(),
			'combo_tipo_alm'   => $this->reportestock_model->get_combo_tipo_alm(),
			'combo_almacenes'  => $this->reportestock_model->get_combo_almacenes($param_tipo_alm),
			'combo_tipo_mat'   => $this->reportestock_model->get_combo_tipo_mat(),
			'combo_materiales' => $this->reportestock_model->get_combo_materiales($param_tipo_mat),
			'reporte'          => $this->reporte->genera_reporte($arr_campos, $datos_reporte),
		);

		app_render_view('stock_sap/movhist', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipo fecha
	 *
	 * @param  string $tipo Tipo de fechas a devolver
	 * @return string       Tipos de fechas formato json
	 */
	public function movhist_ajax_tipo_fecha($tipo = 'ANNO')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_tipo_fecha($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo fecha
	 *
	 * @param  string $tipo   Tipo de fechas a devolver
	 * @param  string $filtro Filtro de las fechas
	 * @return string         Tipos de fechas formato json
	 */
	public function movhist_ajax_fechas($tipo = 'ANNO', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_fechas($tipo, $filtro)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipos de almacen
	 *
	 * @param  string $tipo Tipo de almacen
	 * @return string       Tipos de almacen
	 */
	public function movhist_ajax_tipo_alm($tipo = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_tipo_alm($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo almacenes
	 *
	 * @param  string $tipo   Tipo de almacen
	 * @param  string $filtro Filtro de las fechas
	 * @return string         Tipos de almacen
	 */
	public function movhist_ajax_almacenes($tipo = 'MOVIL-TIPOALM', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_almacenes($tipo, $filtro)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipo de material
	 *
	 * @param  string $tipo Tipo de material
	 * @return string       Tipos de material
	 */
	public function movhist_ajax_tipo_mat($tipo = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_tipo_mat($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo materiales
	 *
	 * @param  string $tipo   Tipo de material
	 * @param  string $filtro Filtro de los materiales
	 * @return string         Materiales
	 */
	public function movhist_ajax_materiales($tipo = 'TIPO', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->reportestock_model->get_combo_materiales($tipo, $filtro)));
	}



	// --------------------------------------------------------------------

	/**
	 * Despliega el detalle del stock
	 *
	 * @return void
	 */
	public function est03()
	{
		$data = array(
			'menu_modulo' => $this->get_menu_modulo('est03'),
			'almacenes'   => $this->reportestock_model->get_almacenes_est03(),
			'marcas'      => $this->reportestock_model->get_marcas_est03(),
			'reporte'     => $this->reportestock_model->get_stock_est03(),
		);

		app_render_view('stock_sap/est03', $data);
	}


}
/* End of file stock_reporte.php */
/* Location: ./application/controllers/stock_reporte.php */
