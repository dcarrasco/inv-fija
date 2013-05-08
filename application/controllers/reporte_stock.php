<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_stock extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('odk9@i2_23');
		//$this->output->enable_profiler(TRUE);
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
		$this->form_validation->set_rules('incl_almacen', '', 'trim');
		$this->form_validation->set_rules('incl_lote', '', 'trim');
		$this->form_validation->set_rules('incl_estado', '', 'trim');
		$this->form_validation->set_rules('incl_modelos', '', 'trim');
		$this->form_validation->set_rules('order_by', '', 'trim');
		$this->form_validation->set_rules('order_sort', '', 'trim');
		$this->form_validation->run();

		$orden_campo   = set_value('order_by');
		$orden_tipo    = set_value('order_sort');
		$tipo_alm      = $this->input->post('tipo_alm');
		$estado_sap    = $this->input->post('estado_sap');
		$incl_almacen  = set_value('incl_almacen');
		$incl_lote     = set_value('incl_lote');
		$incl_estado   = set_value('incl_estado');
		$incl_modelos  = set_value('incl_modelos');

		$new_orden_tipo  = ($orden_tipo == 'ASC') ? 'DESC' : 'ASC';
		$view = 'listado';

		if ($tipo == 'permanencia')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_reporte_permanencia($orden_campo, $orden_tipo, $tipo_alm, $estado_sap, $incl_almacen, $incl_lote, $incl_estado, $incl_modelos);

			$arr_campos = array();

			$arr_campos['tipo']    = array('titulo' => 'Tipo Almacen','class' => '',   'tipo' => 'texto');
			if ($incl_almacen == '1')
			{
				$arr_campos['centro']    = array('titulo' => 'Centro','class' => '',   'tipo' => 'texto');
				$arr_campos['cod_almacen']    = array('titulo' => 'CodAlmacen','class' => '',   'tipo' => 'texto');
				$arr_campos['des_almacen']    = array('titulo' => 'Almacen','class' => '',   'tipo' => 'texto');
			}
			if ($incl_estado == '1')
			{
				$arr_campos['estado_sap']    = array('titulo' => 'Estado SAP','class' => '',   'tipo' => 'texto');
			}
			if ($incl_lote == '1')
			{
				$arr_campos['lote']    = array('titulo' => 'Lote','class' => '',   'tipo' => 'texto');
			}
			if ($incl_modelos == '1')
			{
				$arr_campos['modelo']    = array('titulo' => 'Modelo','class' => '',   'tipo' => 'texto');
			}
			$arr_campos['m120'] = array('titulo' => '000-120',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['m150'] = array('titulo' => '121-150',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['m180'] = array('titulo' => '151-180',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['m360'] = array('titulo' => '181-360',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['m540'] = array('titulo' => '361-500',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['m720'] = array('titulo' => '501-720',     'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['mas720']    = array('titulo' => '+720',        'class' => 'ac',  'tipo' => 'numero');
			$arr_campos['total']   = array('titulo' => 'Total',       'class' => 'ac bold',  'tipo' => 'numero');
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
				'datos_hoja'      => $datos_hoja,
				'tipo_reporte'    => $view,
				'nombre_reporte'  => $tipo,
				'filtro_dif'      => '',
				'arr_campos'      => $arr_campos,
				'arr_link_campos' => $arr_link_campos,
				'arr_link_sort'   => $arr_link_sort,
				'arr_img_orden'   => $arr_img_orden,
				'titulo_modulo'   => 'Reportes Stock',
				'fecha_reporte'   => $this->reportestock_model->get_fecha_reporte(),
				'combo_tipo_alm'  => $this->reportestock_model->combo_tipo_alm(),
				'combo_estado_sap' => $this->reportestock_model->combo_estado_sap(),
				'menu_app'        => $this->acl_model->menu_app(),
			);

		$this->load->view('app_header', $data);
		$this->load->view('stock_sap/reporte', $data);
		$this->load->view('app_footer', $data);

		//echo "<pre>"; var_dump($data); echo "</pre>";
	}

}

/* End of file reportes.php */
/* Location: ./application/controllers/reportes.php */
