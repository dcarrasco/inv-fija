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
class Stock_reporte extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'odk9@i2_23';

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
		$this->lang->load('stock');

		$this->_arr_menu = array(
			'permanencia' => array(
				'url'   => $this->router->class . '/listado/permanencia',
				'texto' => $this->lang->line('stock_perm_menu_perm'),
			),
			'mapastock' => array(
				'url'   => $this->router->class . '/mapastock',
				'texto' => $this->lang->line('stock_perm_menu_mapa'),
				),
			'movhist' => array(
				'url'   => $this->router->class . '/movhist',
				'texto' => $this->lang->line('stock_perm_menu_movs'),
			),
			'est03' => array(
				'url'   => $this->router->class . '/est03',
				'texto' => $this->lang->line('stock_perm_menu_est03'),
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
		$this->load->model('reportestock_model');

		// define reglas para usar set_value
		$this->form_validation->set_rules('tipo_alm');
		$this->form_validation->set_rules('estado_sap');
		$this->form_validation->set_rules('tipo_mat');
		$this->form_validation->set_rules('tipo_op', '', 'trim');
		$this->form_validation->set_rules('incl_almacen', '', 'trim');
		$this->form_validation->set_rules('incl_lote', '', 'trim');
		$this->form_validation->set_rules('incl_modelos', '', 'trim');
		$this->form_validation->set_rules('order_by', '', 'trim');
		$this->form_validation->set_rules('order_sort', '', 'trim');
		$this->form_validation->run();

		$orden_campo   = set_value('order_by');
		$orden_tipo    = set_value('order_sort');
		$tipo_alm      = $this->input->post('tipo_alm');
		$estado_sap    = $this->input->post('estado_sap');
		$tipo_mat      = $this->input->post('tipo_mat');
		$tipo_op       = set_value('tipo_op', 'MOVIL');
		$incl_almacen  = set_value('incl_almacen');
		$incl_lote     = set_value('incl_lote');
		$incl_modelos  = set_value('incl_modelos');

		$new_orden_tipo  = ($orden_tipo === 'ASC') ? 'DESC' : 'ASC';
		$view = 'listado';

		if ($tipo === 'permanencia')
		{
			$orden_campo = ($orden_campo === '') ? 'tipo' : $orden_campo;

			$arr_config = array(
				'orden' => array(
					'campo' => $orden_campo,
					'tipo'  => $orden_tipo,
				),
				'filtros' => array(
					'tipo_alm'   => $tipo_alm,
					'estado_sap' => $estado_sap,
					'tipo_mat'   => $tipo_mat,
				),
				'mostrar' => array(
					'almacen' => $incl_almacen,
					'lote'    => $incl_lote,
					'modelos' => $incl_modelos,
				),
			);

			$datos_hoja = $this->reportestock_model->get_reporte_permanencia($arr_config);

			$arr_campos = array();

			$arr_campos['tipo'] = array('titulo' => 'Tipo Almacen','class' => '', 'tipo' => 'texto');

			if ($incl_almacen === '1')
			{
				$arr_campos['centro'] = array('titulo' => 'Centro','class' => '', 'tipo' => 'texto');
				$arr_campos['almacen'] = array('titulo' => 'CodAlmacen','class' => '', 'tipo' => 'texto');
				$arr_campos['des_almacen'] = array('titulo' => 'Almacen','class' => '', 'tipo' => 'texto');
			}

			if (count($estado_sap) > 0 AND is_array($estado_sap))
			{
				$arr_campos['estado_sap'] = array('titulo' => 'Estado Stock','class' => '', 'tipo' => 'texto');
			}

			if ($incl_lote === '1')
			{
				$arr_campos['lote'] = array('titulo' => 'Lote','class' => '', 'tipo' => 'texto');
			}

			if (count($tipo_mat) > 0 AND is_array($tipo_mat))
			{
				$arr_campos['tipo_material'] = array('titulo' => 'Tipo Material','class' => '', 'tipo' => 'texto');
			}

			if ($incl_modelos === '1')
			{
				$arr_campos['modelo'] = array('titulo' => 'Modelo','class' => '', 'tipo' => 'texto');
			}

			$arr_campos['m030'] = array('titulo' => '000-030', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m060'] = array('titulo' => '031-060', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m090'] = array('titulo' => '061-090', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m120'] = array('titulo' => '091-120', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m180'] = array('titulo' => '121-180', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m360'] = array('titulo' => '181-360', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['m720'] = array('titulo' => '361-720', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['mas720'] = array('titulo' => '+720', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['otro']   = array('titulo' => 'otro', 'class' => 'text-center', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
			$arr_campos['total']  = array('titulo' => 'Total', 'class' => 'text-center bold', 'tipo' => 'link_detalle_series', 'href' => $this->router->class . '/detalle');
		}

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by']  = ($campo === $orden_campo) ? $new_orden_tipo : 'ASC';
			$arr_campos[$campo]['img_orden'] = ($campo === $orden_campo)
				? ' <span class="text-muted glyphicon ' .
					(($arr_campos[$campo]['order_by'] === 'ASC') ? 'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down') .
					'" ></span>'
				: '';
		}

		$data = array(
			'menu_modulo'      => array('menu' => $this->_arr_menu, 'mod_selected' => $tipo),
			'reporte'          => $this->app_common->reporte($arr_campos, $datos_hoja),
			'tipo_reporte'     => $view,
			'nombre_reporte'   => $tipo,
			'filtro_dif'       => '',
			'arr_campos'       => $arr_campos,
			'titulo_modulo'    => 'Reportes Stock',
			'fecha_reporte'    => $this->reportestock_model->get_fecha_reporte(),
			'combo_tipo_alm'   => $this->reportestock_model->combo_tipo_alm($tipo_op),
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

		$this->load->model('reportestock_model');

		$data = array(
			'menu_modulo'        => array('menu' => $this->_arr_menu, 'mod_selected' => 'permanencia'),
			'menu_configuracion' => '',
			'detalle_series'     => $this->reportestock_model->get_detalle_series(
				$this->input->get('id_tipo'),
				$this->input->get('centro'),
				$this->input->get('almacen'),
				$this->input->get('estado_stock'),
				$this->input->get('lote'),
				$this->input->get('material'),
				$this->input->get('tipo_material'),
				$this->input->get('permanencia')
			),
			'titulo_modulo'   => 'Reportes Stock',
		);

		app_render_view('stock_sap/detalle_series', $data);

		$this->load->model('reportestock_model');
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega mapa de monto y cantidades de materiales en bodegas
	 *
	 * @return void
	 */
	public function mapastock()
	{
		$this->form_validation->set_rules('sel_centro', '', 'trim');
		$this->form_validation->set_rules('sel_treemap_type', '', 'trim');
		$this->form_validation->run();

		$centro = set_value('sel_centro', 'CL03');

		$this->load->model('reportestock_model');
		$arr_treemap = $this->reportestock_model->get_treemap_permanencia($centro);
		$arr_treemap_cantidad = $this->reportestock_model->arr_query2treemap('cantidad', $arr_treemap, array('tipo', 'alm', 'marca', 'modelo'), 'cant', 'perm');
		$arr_treemap_valor = $this->reportestock_model->arr_query2treemap('valor', $arr_treemap, array('tipo', 'alm', 'marca', 'modelo'), 'valor', 'perm');

		$data = array(
			'menu_modulo' => array('menu' => $this->_arr_menu, 'mod_selected' => 'mapastock'),
			'treemap_data_cantidad' => $arr_treemap_cantidad,
			'treemap_data_valor' => $arr_treemap_valor,
		);

		app_render_view('stock_sap/treemap', $data);
	}


	// --------------------------------------------------------------------

	public function movhist()
	{
		$this->load->model('reportestock_model');
		$this->app_common->form_validation_config();

		$this->form_validation->set_rules('tipo_fecha');
		$this->form_validation->set_rules('tipo_alm');
		$this->form_validation->set_rules('tipo_mat');
		$this->form_validation->set_rules('tipo_cruce_alm');
		$this->form_validation->set_rules('fechas', 'Fecha reporte', 'required');
		$this->form_validation->set_rules('cmv', 'Movimientos', 'required');
		$this->form_validation->set_rules('almacenes', 'Almacenes', 'required');
		$this->form_validation->set_rules('materiales', 'Materiales', 'required');

		$this->form_validation->run();

		$param_tipo_fecha     = set_value('tipo_fecha', 'ANNO');
		$param_tipo_alm       = set_value('tipo_alm', 'MOVIL-TIPOALM');
		$param_tipo_mat       = set_value('tipo_mat', 'TIPO');
		$param_tipo_cruce_alm = set_value('tipo_cruce_alm', 'alm');

		$config_reporte = array(
			'tipo_fecha'     => $param_tipo_fecha,
			'fechas'         => $this->input->post('fechas'),
			'cmv'            => $this->input->post('cmv'),
			'tipo_alm'       => $param_tipo_alm,
			'almacenes'      => $this->input->post('almacenes'),
			'tipo_cruce_alm' => $param_tipo_cruce_alm,
			'tipo_mat'       => $param_tipo_mat,
			'materiales'     => $this->input->post('materiales'),
		);

		$arr_campos = array();
		$arr_campos['fecha']      = array('titulo' => 'Fecha', 'class' => '', 'tipo' => 'texto');
		$arr_campos['cmv']        = array('titulo' => 'CMov', 'class' => '', 'tipo' => 'texto');
		$arr_campos['ce']         = array('titulo' => 'Centro', 'class' => '', 'tipo' => 'texto');
		$arr_campos['alm']        = array('titulo' => 'Almacen', 'class' => '', 'tipo' => 'texto');
		$arr_campos['rec']        = array('titulo' => 'Dest', 'class' => '', 'tipo' => 'texto');
		$arr_campos['n_doc']      = array('titulo' => 'Num doc', 'class' => '', 'tipo' => 'texto');
		$arr_campos['lote']       = array('titulo' => 'Lote', 'class' => '', 'tipo' => 'texto');
		$arr_campos['codigo_sap'] = array('titulo' => 'Codigo SAP', 'class' => '', 'tipo' => 'texto');
		$arr_campos['cantidad']   = array('titulo' => 'Cantidad', 'class' => '', 'tipo' => 'numero');

		$orden_campo     = set_value('order_by');
		$orden_tipo      = set_value('order_sort');
		$new_orden_tipo  = $orden_tipo === 'ASC' ? 'DESC' : 'ASC';
		$arr_link_campos = array();
		$arr_link_sort   = array();
		$arr_img_orden   = array();
		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by']  = ($campo === $orden_campo) ? $new_orden_tipo : 'ASC';
			$arr_campos[$campo]['img_orden'] = ($campo === $orden_campo)
				? ' <span class="text-muted glyphicon ' .
					(($arr_campos[$campo]['order_by'] === 'ASC') ? 'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down') .
					'" ></span>'
				: '';
		}

		$data = array(
			'menu_modulo'      => array('menu' => $this->_arr_menu, 'mod_selected' => 'movhist'),
			'combo_tipo_fecha' => $this->reportestock_model->get_combo_tipo_fecha(),
			'combo_fechas'     => $this->reportestock_model->get_combo_fechas($param_tipo_fecha),
			'combo_cmv'        => $this->reportestock_model->get_combo_cmv(),
			'combo_tipo_alm'   => $this->reportestock_model->get_combo_tipo_alm(),
			'combo_almacenes'  => $this->reportestock_model->get_combo_almacenes($param_tipo_alm),
			'combo_tipo_mat'   => $this->reportestock_model->get_combo_tipo_mat(),
			'combo_materiales' => $this->reportestock_model->get_combo_materiales($param_tipo_mat),
			'reporte'          => $this->app_common->reporte($arr_campos, $this->reportestock_model->get_reporte_movhist($config_reporte)),
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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

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
		$this->load->model('reportestock_model');

		$data = array(
			'menu_modulo' => array('menu' => $this->_arr_menu, 'mod_selected' => 'est03'),
			'almacenes'   => $this->reportestock_model->get_almacenes_est03(),
			'marcas'      => $this->reportestock_model->get_marcas_est03(),
			'reporte'     => $this->reportestock_model->get_stock_est03(),
		);

		app_render_view('stock_sap/est03', $data);
	}


}
/* End of file stock_reporte.php */
/* Location: ./application/controllers/stock_reporte.php */