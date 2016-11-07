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
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_sap extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'stock_sap';

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
			'stock_movil' => array(
				'url'   => $this->router->class . '/mostrar_stock/MOVIL',
				'texto' => $this->lang->line('stock_sap_menu_movil'),
				'icon'  => 'mobile',
			),
			'stock_fija' => array(
				'url'   => $this->router->class . '/mostrar_stock/FIJA',
				'texto' => $this->lang->line('stock_sap_menu_fijo'),
				'icon'  => 'phone',
			),
			'transito_fija' => array(
				'url'   => $this->router->class . '/transito/FIJA',
				'texto' => $this->lang->line('stock_sap_menu_transito'),
				'icon'  => 'send',
			),
			'reporte_clasif' => array(
				'url'   => $this->router->class . '/reporte_clasif',
				'texto' => $this->lang->line('stock_sap_menu_clasif'),
				'icon'  => 'th',
			),
		);

		$this->load->driver('cache', array('adapter' => 'file'));
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->mostrar_stock('MOVIL');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte con el stock SAP
	 *
	 * @param  string $tipo_op Indica si el stock a desplegar es fijo o movil
	 * @return void
	 */
	public function mostrar_stock($tipo_op = '')
	{
		$this->load->model('stock_sap_model');
		$this->load->library('grafica_stock');

		$stock = ($tipo_op === 'MOVIL') ? new Stock_sap_movil_model() : new Stock_sap_fija_model();
		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		$arr_mostrar = array('fecha', 'tipo_alm', 'tipo_articulo');
		foreach (array('almacen','material','lote','tipo_stock') as $mostrar)
		{
			if ($this->input->post($mostrar) === $mostrar)
			{
				array_push($arr_mostrar, $mostrar);
			}
		}
		$arr_filtrar = 	array();
		foreach (array('fecha','tipo_alm','tipo_articulo','almacenes','sel_tiposalm','tipo_stock_equipos','tipo_stock_simcard','tipo_stock_otros') as $filtro)
		{
			$arr_filtrar[$filtro] = $this->input->post($filtro);
		}

		$tabla_stock = '';
		$datos_grafico = array();

		$this->form_validation->set_rules($this->stock_sap_model->stock_sap_validation);
		if ($this->form_validation->run())
		{
			// recupera tabla de stock de la BD o del cache
			$tabla_stock = cached_query(
				'mostrar_stock'.$tipo_op.serialize($arr_mostrar).serialize($arr_filtrar),
				$stock,
				'get_stock',
				array($arr_mostrar, $arr_filtrar)
			);

			if ($tipo_op === 'MOVIL' AND $tabla_stock !== '')
			{
				//$datos_grafico = $this->grafica_stock->datos_grafico($stock, $this->input->post());
			}
		}

		// recupera combo fechas de la BD o del cache
		$combo_fechas = cached_query('combo_fechas'.$tipo_op, $stock, 'get_combo_fechas', array());

		// recupera combo almacenes de la BD o del cache
		$combo_almacenes = (set_value('sel_tiposalm', 'sel_tiposalm') === 'sel_tiposalm')
			? cached_query('combo_tiposalm'.$tipo_op, $tipoalmacen_sap, 'get_combo_tiposalm', array($tipo_op))
			: cached_query('combo_almacenes'.$tipo_op, $almacen_sap, 'get_combo_almacenes', array($tipo_op));

		$data = array(
			'menu_modulo' => array(
				'menu'         => $this->_arr_menu,
				'mod_selected' => ($tipo_op === 'MOVIL') ? 'stock_movil' : 'stock_fija'
			),
			'tabla_stock'           => $tabla_stock,
			'combo_almacenes'       => $combo_almacenes,
			'combo_fechas'          => $combo_fechas[set_value('sel_fechas', 'ultimodia')],
			'tipo_op'               => $tipo_op,
			'arr_mostrar'           => $arr_mostrar,
			'datos_grafico'         => $datos_grafico,
			'totaliza_tipo_almacen' => ((in_array('almacen', $arr_mostrar)
											OR in_array('material', $arr_mostrar)
											OR in_array('lote', $arr_mostrar)
											OR in_array('tipo_stock', $arr_mostrar))
										AND ($this->input->post('sel_tiposalm') === 'sel_tiposalm'))
										? TRUE
										: FALSE,
		);


		if ($this->input->post('excel'))
		{
			$this->parser->parse('stock_sap/ver_stock_encab_excel', $data);
			$this->parser->parse('stock_sap/ver_stock_datos', $data);
		}
		else
		{
			app_render_view(array('stock_sap/ver_stock_form', 'stock_sap/ver_stock_datos'), $data);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el detalle de las series del stock
	 *
	 * @param  string $centro   Centro SAP de las series a desplegar
	 * @param  string $almacen  Almacen SAP de las series a desplegar
	 * @param  string $material Material SAP de las series a desplegar
	 * @param  string $lote     Lote SAP de las series a desplegar
	 * @return void
	 */
	public function detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		$stock = new Stock_sap_movil_model();

		$detalle_series = cached_query(
			'detalle_series'.$centro.$almacen.$material.$lote,
			$stock,
			'get_detalle_series',
			array($centro, $almacen, $material, $lote)
		);

		$data = array(
			'detalle_series' => $detalle_series,
			'menu_modulo'    => array('menu' => $this->_arr_menu, 'mod_selected' => ''),
		);

		app_render_view('stock_sap/detalle_series', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve las series en transito
	 *
	 * @param  string $tipo_op Indica si el stock a desplegar es fijo o movil
	 * @return void
	 */
	public function transito($tipo_op = 'FIJA')
	{
		$stock = ($tipo_op === 'MOVIL') ? new Stock_sap_movil_model() : new Stock_sap_fija_model();

		$tabla_stock = '';

		$arr_mostrar_todos = array('fecha', 'almacen', 'tipo_stock', 'material', 'lote');
		$arr_filtrar_todos = array('fecha', 'almacen', 'tipo_stock', 'material', 'lote');

		$arr_mostrar = array('fecha', 'almacen');
		foreach ($arr_mostrar_todos as $mostrar)
		{
			if ($this->input->post($mostrar) === $mostrar)
			{
				array_push($arr_mostrar, $mostrar);
			}
		}

		$arr_filtrar = 	array();
		foreach ($arr_filtrar_todos as $filtrar)
		{
			$arr_filtrar[$filtrar] = $this->input->post($filtrar);
		}

		$this->form_validation->set_rules('fecha[]', 'Fechas', 'required');

		if ($this->form_validation->run())
		{
			$tabla_stock = $stock->get_stock_transito($arr_mostrar, $arr_filtrar);
		}

		$combo_fechas = $stock->get_combo_fechas();

		$data = array(
			'menu_modulo'            => array('menu' => $this->_arr_menu, 'mod_selected' => 'transito_fija'),
			'tabla_stock'            => $tabla_stock,
			'combo_fechas_ultimodia' => $combo_fechas['ultimodia'],
			'combo_fechas_todas'     => $combo_fechas['todas'],
			'tipo_op'                => $tipo_op,
			'arr_mostrar'            => $arr_mostrar,
			'datos_grafico'          => '',
			'totaliza_tipo_almacen'  => (((in_array('almacen', $arr_mostrar)
												OR in_array('material', $arr_mostrar)
												OR in_array('lote', $arr_mostrar)
												OR in_array('tipo_stock', $arr_mostrar)
											)
											AND ($this->input->post('sel_tiposalm') === 'sel_tiposalm'))
											? TRUE : FALSE
										),
		);

		if ($this->input->post('excel'))
		{
			$this->load->view('stock_sap/ver_stock_encab_excel', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
		}
		else
		{
			app_render_view(array('stock_sap/ver_stock_form_transito', 'stock_sap/ver_stock_datos'), $data);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Reporte clasificacion de almacenes
	 *
	 * @return void
	 */
	public function reporte_clasif()
	{
		$tipo_op = set_value('operacion', 'FIJA');
		$fechas = set_value('fechas', NULL);
		$borrar_datos = set_value('sel_borrar') === 'borrar';

		$combo_operacion = array('FIJA' => 'Fija', 'MOVIL' => 'Movil');

		$stock = ($tipo_op === 'MOVIL') ? new Stock_sap_movil_model() : new Stock_sap_fija_model();
		$combo_fechas = $stock->get_combo_fechas();

		$reporte = $stock->reporte_clasificacion($tipo_op, $fechas, $borrar_datos);

		$arrjs_reporte = array();
		$js_slices     = array();
		if (count($reporte['datos']) > 0)
		{
			foreach ($reporte['datos'] as $linea)
			{
				//array_push($arrjs_reporte, '[\'' . $linea['clasificacion'] . '\', ' . (int)($linea['monto']/1000000) . ']');
				array_push($js_slices, '{color: \'' . $linea['color'] . '\'}');
			}
		}

		$view_data = array(
			'menu_modulo'            => array(
				'menu'         => $this->_arr_menu,
				'mod_selected' => 'reporte_clasif',
			),
			'combo_fechas'    => $combo_fechas['ultimodia'],
			'combo_operacion' => $combo_operacion,
			'url_reporte'     => site_url($this->_arr_menu['reporte_clasif']['url']),
			'tipo_op'         => $tipo_op,
			'fechas'          => $fechas,
			'reporte'         => $reporte,
			'reporte_js'      => '[[\'Clasificacion\', \'Monto\'], ' . implode(', ', $arrjs_reporte) . ']',
			'js_slices'       => '[' . implode(',', $js_slices). ']',
		);

		app_render_view('stock_sap/reporte_clasif', $view_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo fechas
	 *
	 * @param  string $tipo_op    Tipo de operacion (fijo o movil)
	 * @param  string $tipo_fecha Indica si se devolveran todas las fechas o la ultima fecha del mes
	 * @return string             JSON con datos
	 */
	public function ajax_fechas($tipo_op = 'MOVIL', $tipo_fecha = 'ultimodia')
	{
		$stock = ($tipo_op === 'MOVIL') ? new Stock_sap_movil_model() : new Stock_sap_fija_model();

		// recupera combo fechas de la BD o del cache
		$arr_fechas = cached_query('combo_fechas'.$tipo_op, $stock, 'get_combo_fechas', array());

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($arr_fechas[$tipo_fecha]));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo almacenes
	 *
	 * @param  string $tipo_op  Tipo de operacion (fija o movil)
	 * @param  string $tipo_alm Indica si se devolveran los almacenes o tipos de almacen
	 * @return void
	 */
	public function ajax_almacenes($tipo_op = 'MOVIL', $tipo_alm = 'sel_tiposalm')
	{
		$this->load->model('stock_sap_model');
		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		$arr_almacenes = ($tipo_alm === 'sel_tiposalm')
			? cached_query('combo_tiposalm'.$tipo_op, $tipoalmacen_sap, 'get_combo_tiposalm', array($tipo_op))
			: cached_query('combo_almacenes'.$tipo_op, $almacen_sap, 'get_combo_almacenes', array($tipo_op));

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($arr_almacenes));
	}



}
/* End of file stock_sap.php */
/* Location: ./application/controllers/stock_sap.php */