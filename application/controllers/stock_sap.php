<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_sap extends CI_Controller {

	private $arr_menu = array();

	// --------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('stock_sap');

		if (ENVIRONMENT != 'production')
		{
			$this->output->enable_profiler(TRUE);
		}

		$this->arr_menu = array(
			'stock_movil'     => array('url' => $this->uri->segment(1) . '/mostrar_stock/MOVIL', 'texto' => 'Stock Movil'),
			'stock_fija'      => array('url' => $this->uri->segment(1) . '/mostrar_stock/FIJA', 'texto' => 'Stock Fija'),
			'transito_fija'   => array('url' => $this->uri->segment(1) . '/transito/FIJA', 'texto' => 'Transito Fija'),
			//'grupos_movil'    => array('url' => $this->uri->segment(1) . '/lista_grupos/MOVIL', 'texto' => 'Grupos Movil'),
			//'grupos_fija'     => array('url' => $this->uri->segment(1) . '/lista_grupos/FIJA', 'texto' => 'Grupos Fija'),
			//'almacenes_movil' => array('url' => $this->uri->segment(1) . '/lista_almacenes/MOVIL', 'texto' => 'Almacenes Movil'),
			//'almacenes_fija'  => array('url' => $this->uri->segment(1) . '/lista_almacenes/FIJA', 'texto' => 'Almacenes Fija'),
		);
	}

	// --------------------------------------------------------------------

	public function index()
	{
		$this->mostrar_stock('MOVIL');
	}

	// --------------------------------------------------------------------

	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Consulta stock SAP';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}

	// --------------------------------------------------------------------

	public function mostrar_stock($tipo_op = '')
	{
		$this->load->model('stock_sap_model');
		$this->load->model('almacen_sap_model');

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

		$arr_filtrar['fecha'] = ($this->input->post('sel_fechas') == 'ultimo_dia') ? $this->input->post('fecha_ultimodia') : $this->input->post('fecha_todas');

		$this->form_validation->set_rules('sel_fechas', '', '');
		$this->form_validation->set_rules('fecha_ultimodia', '', '');
		$this->form_validation->set_rules('fecha_todas', '', '');
		$this->form_validation->set_rules('sel_tiposalm', '', '');
		$this->form_validation->set_rules('tipo_alm', '', '');
		$this->form_validation->set_rules('almacen', '', '');
		$this->form_validation->set_rules('almacenes', '', '');
		$this->form_validation->set_rules('material', '', '');
		$this->form_validation->set_rules('lote', '', '');
		$this->form_validation->set_rules('tipo_stock', '', '');
		$this->form_validation->set_rules('tipo_stock_equipos', '', '');
		$this->form_validation->set_rules('tipo_stock_simcard', '', '');
		$this->form_validation->set_rules('tipo_stock_otros', '', '');
		$this->form_validation->run();

		$stock = array();
		if($this->input->post('sel_fechas'))
		{
			$stock = $this->stock_sap_model->get_stock($tipo_op, $arr_mostrar, $arr_filtrar);
		}
		$combo_fechas = $this->stock_sap_model->get_combo_fechas($tipo_op);

		$serie_q_equipos = '[]';
		$serie_v_equipos = '[]';
		$serie_q_simcard = '[]';
		$serie_v_simcard = '[]';
		$serie_q_otros = '[]';
		$serie_v_otros = '[]';
		$str_eje_x = '[]';
		$str_label_series = '[]';

		if ($tipo_op == 'MOVIL' and count($stock) > 0)
		{
			$graph_q_equipos = array();
			$graph_q_simcard = array();
			$graph_q_otros   = array();
			$graph_v_equipos = array();
			$graph_v_simcard = array();
			$graph_v_otros   = array();

			$arr_eje_x = array();
			$arr_label_series = array();

			foreach($stock as $reg)
			{
				// si seleccionamos mas de una fecha, entonces usamos la fecha en el eje_X
				if (count($this->input->post('fecha_ultimodia'))>0)
				{
					$idx_eje_x = 'fecha_stock';
					if (!in_array("'" . $reg[$idx_eje_x] . "'", $arr_eje_x))
					{
						array_push($arr_eje_x, "'" . $reg[$idx_eje_x] . "'");
					}
				}

				// si seleccionamos tipos de almacen, entonces usamos el tipo de almacen como las series
				if ($this->input->post('sel_tiposalm') == 'sel_tiposalm')
				{
					// si no se ha seleccionado la apertura por almacenes, usa el tipo de almacen
					if (!$this->input->post('almacen'))
					{
						$idx_series = 'tipo_almacen';
						$label_serie = '{label:\'' . $reg[$idx_series] . '\'}';
						if (!in_array($label_serie, $arr_label_series))
						{
							array_push($arr_label_series, $label_serie);
						}
					}
					// si se selecciono apertura por almacén
					else
					{
						// si hay mas de un tipo de almacen seleccionado...
						if ($this->input->post('almacen') and count($this->input->post('tipo_alm'))>1)
						{
							$idx_series = 'cod_almacen';
							$label_serie = '{label:\'' . $reg['tipo_almacen'] . '/'. $reg[$idx_series] . '-' . $reg['des_almacen'] . '\'}';
							if (!in_array($label_serie, $arr_label_series))
							{
								array_push($arr_label_series, $label_serie);
							}
						}
						// si hay solo un tipo de almacen seleccionado...
						else
						{
							$idx_series = 'cod_almacen';
							$label_serie = '{label:\'' . $reg[$idx_series] . '-' . $reg['des_almacen'] . '\'}';
							if (!in_array($label_serie, $arr_label_series))
							{
								array_push($arr_label_series, $label_serie);
							}
						}
					}
				}
				// si seleccionamos solo almacenes...
				else
				{
					$idx_series = 'cod_bodega';
					$label_serie = '{label:\'' . $reg['centro'] . '-' . $reg[$idx_series] . ' ' . $reg['des_almacen'] . '\'}';
					if (!in_array($label_serie, $arr_label_series))
					{
						array_push($arr_label_series, $label_serie);
					}
				}

				$graph_q_equipos[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['EQUIPOS'];
				$graph_v_equipos[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_EQUIPOS']/1000000;

				$graph_q_simcard[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['SIMCARD'];
				$graph_v_simcard[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_SIMCARD']/1000000;

				$graph_q_otros[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['OTROS'];
				$graph_v_otros[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_OTROS']/1000000;
			}

			$serie_q_equipos = $this->_arr_series_to_string($graph_q_equipos);
			$serie_v_equipos = $this->_arr_series_to_string($graph_v_equipos);
			$serie_q_simcard = $this->_arr_series_to_string($graph_q_simcard);
			$serie_v_simcard = $this->_arr_series_to_string($graph_v_simcard);
			$serie_q_otros = $this->_arr_series_to_string($graph_q_otros);
			$serie_v_otros= $this->_arr_series_to_string($graph_v_otros);
			$str_eje_x = '[' . implode(',', $arr_eje_x) . ']';
			$str_label_series = '[' . implode(',', $arr_label_series) . ']';
		}

		$data = array(
			'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => ($tipo_op == 'MOVIL') ? 'stock_movil' : 'stock_fija'),
			'stock'                  => $stock,
			'combo_tipo_alm'         => $this->almacen_sap_model->get_combo_tiposalm($tipo_op),
			'combo_almacenes'        => $this->almacen_sap_model->get_combo_almacenes($tipo_op),
			'combo_fechas_ultimodia' => $combo_fechas['ultimodia'],
			'combo_fechas_todas'     => $combo_fechas['todas'],
			'tipo_op'                => $tipo_op,
			'arr_mostrar'            => $arr_mostrar,
			'serie_q_equipos'        => $serie_q_equipos,
			'serie_v_equipos'        => $serie_v_equipos,
			'serie_q_simcard'        => $serie_q_simcard,
			'serie_v_simcard'        => $serie_v_simcard,
			'serie_q_otros'          => $serie_q_otros,
				'serie_v_otros'          => $serie_v_otros,
				'str_eje_x'              => $str_eje_x,
			'str_label_series'       => $str_label_series,
			'totaliza_tipo_almacen'  => (((in_array('almacen', $arr_mostrar)  || in_array('material', $arr_mostrar)
											|| in_array('lote', $arr_mostrar) || in_array('tipo_stock', $arr_mostrar)
											)
											&& ($this->input->post('sel_tiposalm') == 'sel_tiposalm')
											) ? TRUE : FALSE),
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
			$this->load->view('stock_sap/ver_stock_form', $data);
			$this->load->view('stock_sap/ver_stock_datos', $data);
			$this->load->view('app_footer', $data);
		}
	}

	// --------------------------------------------------------------------

	private function _arr_series_to_string($arr = array())
	{
		$arr_temp = array();
		foreach($arr as $arr_elem)
		{
			array_push($arr_temp, '[' . implode(',', $arr_elem) . ']');
		}
		return('[' . implode(',', $arr_temp) . ']');
	}

	// --------------------------------------------------------------------

	public function detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		$this->load->model('stock_sap_model');

		$data = array(
			'detalle_series' => $this->stock_sap_model->get_detalle_series($centro, $almacen, $material, $lote),
			'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => ''),
		);

		$this->_render_view('stock_sap/detalle_series', $data);

	}

	// --------------------------------------------------------------------

	public function transito($tipo_op = '')
	{
		$this->load->model('stock_sap_model');
		$this->load->model('almacen_sap_model');

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

		$arr_filtrar['fecha'] = ($this->input->post('sel_fechas') == 'ultimo_dia') ? $this->input->post('fecha_ultimodia') : $this->input->post('fecha_todas');

		$this->form_validation->set_rules('sel_fechas', '', '');
		$this->form_validation->set_rules('fecha_ultimodia', '', '');
		$this->form_validation->set_rules('fecha_todas', '', '');
		$this->form_validation->set_rules('material', '', '');
		$this->form_validation->set_rules('lote', '', '');
		$this->form_validation->set_rules('tipo_stock', '', '');
		$this->form_validation->set_rules('tipo_stock_equipos', '', '');
		$this->form_validation->set_rules('tipo_stock_simcard', '', '');
		$this->form_validation->set_rules('tipo_stock_otros', '', '');
		$this->form_validation->run();

		$stock        = $this->stock_sap_model->get_stock_transito($tipo_op, $arr_mostrar, $arr_filtrar);
		$combo_fechas = $this->stock_sap_model->get_combo_fechas($tipo_op);

		$data = array(
			'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => 'transito_fija'),
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
											&& ($this->input->post('sel_tiposalm') == 'sel_tiposalm')
											) ? TRUE : FALSE),
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