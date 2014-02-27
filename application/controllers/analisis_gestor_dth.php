<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis_gestor_dth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('fhzcJJPL12$/!');

		if (ENVIRONMENT != 'production')
		{
			$this->output->enable_profiler(TRUE);
		}
	}

	public function index()
	{
		$this->log_gestor();
	}

	public function log_gestor()
	{

		$this->load->model('log_gestor_model');
		$datos = array();

		$this->form_validation->set_rules('series', 'Series', '');
		$this->form_validation->set_rules('set_serie', 'Series', '');
		$this->form_validation->set_rules('tipo_reporte', 'Series', '');
		$this->form_validation->set_rules('ult_mov', 'Series', '');
		$this->form_validation->set_rules('tipo_op_alta', 'Series', '');
		$this->form_validation->set_rules('tipo_op_baja', 'Series', '');
		$this->form_validation->run();

		$arr_filtro_cas = array();
		if ($this->input->post('tipo_op_alta') == 'alta')
		{
			array_push($arr_filtro_cas, "'ALTA'");
		}
		if ($this->input->post('tipo_op_baja') == 'baja')
		{
			array_push($arr_filtro_cas, "'BAJA'");
		}


		$datos['log'] = $this->log_gestor_model->get_log($this->input->post('series'), $this->input->post('set_serie'), $this->input->post('tipo_reporte'), implode(', ', $arr_filtro_cas), $this->input->post('ult_mov') == 'show');

		$datos['titulo_modulo'] = 'Consulta información series';


		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_log_gestor_dth_view', $datos);
		$this->load->view('app_footer', $datos);
	}


}

/* End of file analisis_gestor_dth.php */
/* Location: ./application/controllers/analisis_gestor_dth.php */