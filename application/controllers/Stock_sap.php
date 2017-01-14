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
class Stock_sap extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'stock_sap';

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

		$this->set_menu_modulo(array(
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

		$arr_mostrar = array('fecha', 'tipo_articulo');
		foreach (array('sel_tiposalm','almacen','material','lote','tipo_stock') as $mostrar)
		{
			($this->input->post($mostrar) === $mostrar) AND array_push($arr_mostrar, $mostrar);
		}

		$tabla_stock = '';
		$datos_grafico = array();
		$stock = ($tipo_op === 'MOVIL') ? new Stock_sap_movil_model() : new Stock_sap_fija_model();
		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		$combo_fechas    = $stock->get_combo_fechas();
		$combo_almacenes = (set_value('sel_tiposalm', 'sel_tiposalm') === 'sel_tiposalm')
			? $tipoalmacen_sap->get_combo_tiposalm($tipo_op)
			: $almacen_sap->get_combo_almacenes($tipo_op);

		$is_form_valid = $this->form_validation->set_rules($this->stock_sap_model->stock_sap_validation)->run();

		$data = array(
			'menu_modulo'     => $this->get_menu_modulo(($tipo_op === 'MOVIL') ? 'stock_movil' : 'stock_fija'),
			'tabla_stock'     => $is_form_valid ? $stock->reporte($arr_mostrar, $this->input->post()) : '',
			'combo_almacenes' => $combo_almacenes,
			'combo_fechas'    => $combo_fechas[set_value('sel_fechas', 'ultimodia')],
			'tipo_op'         => $tipo_op,
			'arr_mostrar'     => $arr_mostrar,
			'datos_grafico'   => $datos_grafico,
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
			'menu_modulo'    => $this->get_menu_modulo(''),
			'detalle_series' => $detalle_series,
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
			'menu_modulo'            => $this->get_menu_modulo('transito_fija'),
			'tabla_stock'            => $tabla_stock,
			'combo_fechas_ultimodia' => $combo_fechas['ultimodia'],
			'combo_fechas_todas'     => $combo_fechas['todas'],
			'tipo_op'                => $tipo_op,
			'arr_mostrar'            => $arr_mostrar,
			'datos_grafico'          => '',
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
			'menu_modulo'     => $this->get_menu_modulo('reporte_clasif'),
			'combo_fechas'    => $combo_fechas['ultimodia'],
			'combo_operacion' => $combo_operacion,
			'url_reporte'     => site_url($this->menu_opciones['reporte_clasif']['url']),
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
		$arr_fechas = $stock->get_combo_fechas();

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
			? $tipoalmacen_sap->get_combo_tiposalm($tipo_op)
			: $almacen_sap->get_combo_almacenes($tipo_op);

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($arr_almacenes));
	}



}
/* End of file stock_sap.php */
/* Location: ./application/controllers/stock_sap.php */