<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_sap extends CI_Controller {

	public $llave_modulo = 'stock_sap';
	private $arr_menu = array();

	// --------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('stock');

		$this->arr_menu = array(
			'stock_movil' => array(
				'url'   => $this->uri->segment(1) . '/mostrar_stock/MOVIL',
				'texto' => $this->lang->line('stock_sap_menu_movil'),
			),
			'stock_fija' => array(
				'url'   => $this->uri->segment(1) . '/mostrar_stock/FIJA',
				'texto' => $this->lang->line('stock_sap_menu_fijo'),
			),
			'transito_fija' => array(
				'url'   => $this->uri->segment(1) . '/transito/FIJA',
				'texto' => $this->lang->line('stock_sap_menu_transito'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @param  none
	 * @return none
	 */
	public function index()
	{
		$this->mostrar_stock('MOVIL');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera las vistas para los métodos de este controlador
	 *
	 * @param  string $vista Nombre de la vista a desplegar
	 * @param  array  $data  Arreglo con las variables a pasar a la vista
	 * @return none
	 */
	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Consulta stock SAP';

		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte con el stock SAP
	 *
	 * @param  string $tipo_op Indica si el stock a desplegar es fijo o movil
	 * @return none
	 */
	public function mostrar_stock($tipo_op = '')
	{
		$this->load->model('stock_sap_model');
		$this->load->library('grafica_stock');

		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		$arr_mostrar = array('fecha', 'tipo_alm', 'tipo_articulo');
		foreach (array('almacen','material','lote','tipo_stock') as $val)
		{
			if ($this->input->post($val) == $val)
			{
				array_push($arr_mostrar, $val);
			}
		}

		$arr_filtrar = 	array();
		foreach (array('tipo_alm','tipo_articulo','almacenes','sel_tiposalm','tipo_stock_equipos','tipo_stock_simcard','tipo_stock_otros') as $val)
		{
			$arr_filtrar[$val] = $this->input->post($val);
		}

		$arr_filtrar['fecha'] = ($this->input->post('sel_fechas') == 'ultimo_dia')
			? $this->input->post('fecha_ultimodia')
			: $this->input->post('fecha_todas');

		$this->form_validation->set_rules('sel_fechas', 'Seleccion de fechas', '');
		$this->form_validation->set_rules('fecha_ultimodia', 'Fechas (ultimo dia del mes)', '');
		$this->form_validation->set_rules('fecha_todas', 'Fechas (todas)', '');
		$this->form_validation->set_rules('sel_tiposalm', 'Seleccion de almacenes', '');
		$this->form_validation->set_rules('tipo_alm', 'Tipos de almacenes', '');
		$this->form_validation->set_rules('almacenes', 'Almacenes', '');
		$this->form_validation->set_rules('almacen', 'Indicador detalle almacenes', '');
		$this->form_validation->set_rules('material', 'Indicador detalle materiales', '');
		$this->form_validation->set_rules('lote', 'Indicador detalle lotes', '');
		$this->form_validation->set_rules('tipo_stock', 'Indicador tipos de stock', '');
		$this->form_validation->set_rules('tipo_stock_equipos', 'Mostrar equipos', '');
		$this->form_validation->set_rules('tipo_stock_simcard', 'Mostrar simcards', '');
		$this->form_validation->set_rules('tipo_stock_otros', '', 'Mostrar otros', '');
		$this->form_validation->set_rules('mostrar_cant_monto', 'Mostar cantidades/montos', '');
		$this->form_validation->run();

		$stock = array();
		if($this->input->post('sel_fechas'))
		{
			$stock = $this->stock_sap_model->get_stock($tipo_op, $arr_mostrar, $arr_filtrar);
		}
		$combo_fechas = $this->stock_sap_model->get_combo_fechas($tipo_op);

		$datos_grafico = array();
		if ($tipo_op == 'MOVIL' AND count($stock) > 0)
		{
			$datos_grafico = $this->grafica_stock->datos_grafico($stock, $this->input->post());
		}

		$data = array(
			'menu_modulo'            => array(
				'menu'         => $this->arr_menu,
				'mod_selected' => ($tipo_op == 'MOVIL') ? 'stock_movil' : 'stock_fija'
			),
			'stock'                  => $stock,
			'combo_tipo_alm'         => $tipoalmacen_sap->get_combo_tiposalm($tipo_op),
			'combo_almacenes'        => $almacen_sap->get_combo_almacenes($tipo_op),
			'combo_fechas_ultimodia' => $combo_fechas['ultimodia'],
			'combo_fechas_todas'     => $combo_fechas['todas'],
			'tipo_op'                => $tipo_op,
			'arr_mostrar'            => $arr_mostrar,
			'datos_grafico'          => $datos_grafico,
			'totaliza_tipo_almacen'  => (((in_array('almacen', $arr_mostrar)  || in_array('material', $arr_mostrar)
											|| in_array('lote', $arr_mostrar) || in_array('tipo_stock', $arr_mostrar)
											)
											&& ($this->input->post('sel_tiposalm') == 'sel_tiposalm')
											) ? TRUE : FALSE),
		);


		if ($this->input->post('excel'))
		{
			$this->load->view('stock_sap/ver_stock_encab_excel', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
		}
		else
		{
			$this->load->view('app_header', $data);
			$this->load->view('stock_sap/ver_stock_form', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
			$this->load->view('app_footer', $data);
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
	 * @return none
	 */
	public function detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		$this->load->model('stock_sap_model');

		$data = array(
			'detalle_series' => $this->stock_sap_model->get_detalle_series($centro, $almacen, $material, $lote),
			'menu_modulo'    => array('menu' => $this->arr_menu, 'mod_selected' => ''),
		);

		$this->_render_view('stock_sap/detalle_series', $data);

	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las series en transito
	 *
	 * @param  string $tipo_op Indica si el stock a desplegar es fijo o movil
	 * @return none
	 */
	public function transito($tipo_op = 'FIJA')
	{
		$this->load->model('stock_sap_model');

		$arr_mostrar_todos = array('fecha', 'almacen', 'tipo_stock', 'material', 'lote');
		$arr_filtrar_todos = array('fecha', 'almacen', 'tipo_stock', 'material', 'lote');

		$arr_mostrar = array('fecha', 'almacen');
		foreach ($arr_mostrar_todos as $val)
		{
			if ($this->input->post($val) == $val)
			{
				array_push($arr_mostrar, $val);
			}
		}

		$arr_filtrar = 	array();
		foreach ($arr_filtrar_todos as $val)
		{
			$arr_filtrar[$val] = $this->input->post($val);
		}

		$arr_filtrar['fecha'] = ($this->input->post('sel_fechas') == 'ultimo_dia')
			? $this->input->post('fecha_ultimodia')
			: $this->input->post('fecha_todas');

		$this->form_validation->set_rules('sel_fechas', '', '');
		$this->form_validation->set_rules('fecha_ultimodia', '', '');
		$this->form_validation->set_rules('fecha_todas', '', '');
		$this->form_validation->set_rules('tipo_stock', '', '');
		$this->form_validation->set_rules('material', '', '');
		$this->form_validation->set_rules('lote', '', '');
		$this->form_validation->run();

		$stock        = $this->stock_sap_model->get_stock_transito($tipo_op, $arr_mostrar, $arr_filtrar);
		$combo_fechas = $this->stock_sap_model->get_combo_fechas($tipo_op);

		$data = array(
			'menu_modulo'            => array('menu' => $this->arr_menu, 'mod_selected' => 'transito_fija'),
			'stock'                  => $stock,
			'combo_fechas_ultimodia' => $combo_fechas['ultimodia'],
			'combo_fechas_todas'     => $combo_fechas['todas'],
			'tipo_op'                => $tipo_op,
			'arr_mostrar'            => $arr_mostrar,
			'totaliza_tipo_almacen'  => (((in_array('almacen', $arr_mostrar)
												|| in_array('material', $arr_mostrar)
												|| in_array('lote', $arr_mostrar)
												|| in_array('tipo_stock', $arr_mostrar)
											)
											&& ($this->input->post('sel_tiposalm') == 'sel_tiposalm'))
											? TRUE : FALSE
										),
		);

		$data['titulo_modulo'] = 'Consulta stock SAP';

		if ($this->input->post('excel'))
		{
			$this->load->view('stock_sap/ver_stock_encab_excel', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
		}
		else
		{
			$this->load->view('app_header', $data);
			$this->load->view('stock_sap/ver_stock_form_transito', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
			$this->load->view('app_footer', $data);
		}
	}



}
/* End of file stock_sap.php */
/* Location: ./application/controllers/stock_sap.php */