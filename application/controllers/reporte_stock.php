<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_stock extends CI_Controller {

	private $arr_menu = array(
				'permanencia'        => array('url' => '/reporte_stock/listado/permanencia', 'texto' => 'Permanencia'),
			);


	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('odk9@i2_23');

		if (ENVIRONMENT != 'production')
		{
			$this->output->enable_profiler(TRUE);
		}
	}


	public function index()
	{
		$this->listado('permanencia');
	}

	/**
	 * [listado description]
	 * @param  string $tipo   [description]
	 * @param  string $param1 [description]
	 * @return [type]
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

		$new_orden_tipo  = ($orden_tipo == 'ASC') ? 'DESC' : 'ASC';
		$view = 'listado';

		if ($tipo == 'permanencia')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_reporte_permanencia($orden_campo, $orden_tipo, $tipo_alm, $estado_sap, $tipo_mat, $incl_almacen, $incl_lote, $incl_modelos);

			$arr_campos = array();

			$arr_campos['tipo']    = array('titulo' => 'Tipo Almacen','class' => '',   'tipo' => 'texto');
			if ($incl_almacen == '1')
			{
				$arr_campos['centro']    = array('titulo' => 'Centro','class' => '',   'tipo' => 'texto');
				$arr_campos['almacen']    = array('titulo' => 'CodAlmacen','class' => '',   'tipo' => 'texto');
				$arr_campos['des_almacen']    = array('titulo' => 'Almacen','class' => '',   'tipo' => 'texto');
			}
			if (count($estado_sap) > 0 and is_array($estado_sap))
			{
				$arr_campos['estado_sap']    = array('titulo' => 'Estado Stock','class' => '',   'tipo' => 'texto');
			}
			if ($incl_lote == '1')
			{
				$arr_campos['lote']    = array('titulo' => 'Lote','class' => '',   'tipo' => 'texto');
			}
			if (count($tipo_mat) > 0 and is_array($tipo_mat))
			{
				$arr_campos['tipo_material']    = array('titulo' => 'Tipo Material','class' => '',   'tipo' => 'texto');
			}
			if ($incl_modelos == '1')
			{
				$arr_campos['modelo']    = array('titulo' => 'Modelo','class' => '',   'tipo' => 'texto');
			}
			$arr_campos['m030'] = array('titulo' => '000-030', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m060'] = array('titulo' => '031-060', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m090'] = array('titulo' => '061-090', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m120'] = array('titulo' => '091-120', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m180'] = array('titulo' => '121-180', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m360'] = array('titulo' => '181-360', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['m720'] = array('titulo' => '361-720', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['mas720'] = array('titulo' => '+720', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['otro']   = array('titulo' => 'otro', 'class' => 'text-center',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
			$arr_campos['total']  = array('titulo' => 'Total', 'class' => 'text-center bold',  'tipo' => 'link_detalle_series', 'href' => 'reporte_stock/detalle');
		}

		$arr_link_campos = array();
		$arr_link_sort   = array();
		$arr_img_orden   = array();
		foreach ($arr_campos as $campo => $valor)
		{
			$arr_link_campos[$campo] = $campo;

			$arr_link_sort[$campo] = (($campo == $orden_campo) ? $new_orden_tipo : 'ASC' );

			$arr_img_orden[$campo]   = ($campo == $orden_campo) ?
											'<img src="' . base_url() . 'img/' . (($orden_tipo == 'ASC') ?
											'ic_up_circle.png' : 'ic_down_circle.png') . '" />': '';
		}

		$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $tipo),
				//'arr_campos'      => $arr_campos,
				//'datos_hoja'      => $datos_hoja,
				//'arr_link_campos' => $arr_link_campos,
				//'arr_link_sort'   => $arr_link_sort,
				//'arr_img_orden'   => $arr_img_orden,
				'reporte'         => $this->app_common->reporte($arr_campos, $datos_hoja, $arr_link_campos, $arr_link_sort, $arr_img_orden),
				'tipo_reporte'    => $view,
				'nombre_reporte'  => $tipo,
				'filtro_dif'      => '',
				'arr_campos'      => $arr_campos,
				'titulo_modulo'   => 'Reportes Stock',
				'fecha_reporte'   => $this->reportestock_model->get_fecha_reporte(),
				'combo_tipo_alm'  => $this->reportestock_model->combo_tipo_alm($tipo_op),
				'combo_estado_sap' => $this->reportestock_model->combo_estado_sap(),
				'combo_tipo_mat'   => $this->reportestock_model->combo_tipo_mat(),
			);

		$this->load->view('app_header', $data);
		$this->load->view('stock_sap/reporte', $data);
		$this->load->view('app_footer', $data);

		//echo "<pre>"; var_dump($data); echo "</pre>";
	}

	public function detalle()
	{

		$this->load->model('reportestock_model');

		$data = array(
			'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => 'permanencia'),
			'menu_configuracion' => '',
			'detalle_series'     => $this->reportestock_model->get_detalle_series($this->input->get('id_tipo'), $this->input->get('centro'), $this->input->get('almacen'), $this->input->get('estado_stock'), $this->input->get('lote'), $this->input->get('material'), $this->input->get('tipo_material'), $this->input->get('permanencia')),
			'titulo_modulo'   => 'Reportes Stock',
		);

		$this->load->view('app_header', $data);
		$this->load->view('stock_sap/detalle_series', $data);
		$this->load->view('app_footer', $data);

		$this->load->model('reportestock_model');
		


	}

}

/* End of file reportes.php */
/* Location: ./application/controllers/reportes.php */