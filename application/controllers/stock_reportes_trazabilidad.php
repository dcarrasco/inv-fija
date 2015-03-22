<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_reportes_trazabilidad extends CI_Controller {

	public $llave_modulo = 'j*&on238=B';
	private $arr_menu = array();


	public function __construct()
	{
		parent::__construct();

		$this->arr_menu = array(
			'perm_consumo' => array('url' => $this->uri->segment(1) . '/listado/perm_consumo', 'texto' => 'Permanencia Consumo'),
			'det_consumo' => array('url' => $this->uri->segment(1) . '/listado/det_consumo', 'texto' => 'Detalle Series Consumo'),
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
		$this->listado('perm_consumo');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar listados de trazabilidad de series
	 *
	 * @param  string $tipo   Tipo de reporte
	 * @param  string $param1 [description]
	 * @return [type]
	 */
	public function listado($tipo = 'perm_consumo', $param1 = '')
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

		if ($tipo == 'perm_consumo')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_reporte_perm_consumo($orden_campo, $orden_tipo, $tipo_alm, $estado_sap, $incl_almacen, $incl_lote, $incl_estado, $incl_modelos);

			$arr_campos = array();

			$arr_campos['tipo'] = array('titulo' => 'Tipo Almacen','class' => '', 'tipo' => 'texto');

			if ($incl_almacen == '1')
			{
				$arr_campos['centro'] = array('titulo' => 'Centro','class' => '', 'tipo' => 'texto');
				$arr_campos['almacen'] = array('titulo' => 'CodAlm','class' => '', 'tipo' => 'texto');
				$arr_campos['des_almacen'] = array('titulo' => 'Almacen','class' => '', 'tipo' => 'texto');
			}

			if ($incl_estado == '1')
			{
				$arr_campos['estado_sap'] = array('titulo' => 'Estado SAP','class' => '', 'tipo' => 'texto');
			}

			if ($incl_modelos == '1')
			{
				$arr_campos['material'] = array('titulo' => 'CodMat','class' => '', 'tipo' => 'texto');
				$arr_campos['des_material'] = array('titulo' => 'Material','class' => '', 'tipo' => 'texto');
			}

			if ($incl_lote == '1')
			{
				$arr_campos['lote'] = array('titulo' => 'Lote','class' => '', 'tipo' => 'texto');
			}

			$arr_campos['m030']   = array('titulo' => '000-030', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['m060']   = array('titulo' => '031-060', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['m090']   = array('titulo' => '061-090', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['m180']   = array('titulo' => '091-180', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['mas180'] = array('titulo' => '+181', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['total']  = array('titulo' => 'Total', 'class' => 'text-center', 'tipo' => 'numero');
		}
		elseif ($tipo == 'det_consumo')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_detalle_series_consumo($tipo_alm);

			$arr_campos = array();

			$arr_campos['tipo']         = array('titulo' => 'Tipo Almacen', 'class' => '', 'tipo' => 'texto');
			$arr_campos['centro']       = array('titulo' => 'Centro', 'class' => '', 'tipo' => 'texto');
			$arr_campos['almacen']      = array('titulo' => 'Cod Alm', 'class' => '', 'tipo' => 'texto');
			$arr_campos['des_almacen']  = array('titulo' => 'Almacen', 'class' => '', 'tipo' => 'texto');
			$arr_campos['material']     = array('titulo' => 'Cod Mat', 'class' => '', 'tipo' => 'texto');
			$arr_campos['des_material'] = array('titulo' => 'Material', 'class' => '', 'tipo' => 'texto');
			$arr_campos['lote']         = array('titulo' => 'Lote', 'class' => '', 'tipo' => 'texto');
			$arr_campos['dias']         = array('titulo' => 'Permanencia', 'class' => '', 'tipo' => 'texto');
			$arr_campos['serie']        = array('titulo' => 'Serie', 'class' => '', 'tipo' => 'texto');

		}

		$arr_link_campos = array();
		$arr_link_sort   = array();
		$arr_img_orden   = array();
		foreach ($arr_campos as $campo => $valor)
		{
			$arr_link_campos[$campo] = $campo;
			$arr_link_sort[$campo] = (($campo == $orden_campo) ? $new_orden_tipo : 'ASC' );
			$arr_img_orden[$campo]   = ($campo == $orden_campo) ?
											'<span class="glyphicon ' . (($orden_tipo == 'ASC') ?
											'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down') . '"></span>': '';
		}

		$data = array(
			'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $tipo),
			'datos_hoja'      => $datos_hoja,
			'tipo_reporte'    => $view,
			'nombre_reporte'  => $tipo,
			'filtro_dif'      => '',
			'arr_campos'      => $arr_campos,
			'arr_link_campos' => $arr_link_campos,
			'arr_link_sort'   => $arr_link_sort,
			'arr_img_orden'   => $arr_img_orden,
			'titulo_modulo'   => 'Reportes Trazabilidad',
			'combo_tipo_alm'  => $this->reportestock_model->combo_tipo_alm_consumo(),
			//'combo_estado_sap' => $this->reportestock_model->combo_estado_sap(),
		);

		$this->load->view('app_header', $data);
		$this->load->view('stock_sap/reporte_trazabilidad', $data);
		$this->load->view('app_footer', $data);
	}



}
/* End of file stock_reportes_trazabilidad.php */
/* Location: ./application/controllers/stock_reportes_trazabilidad.php */