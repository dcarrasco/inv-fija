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
		$this->form_validation->set_rules('ult_mov', 'Series', '');
		$this->form_validation->run();

		$datos['log'] = $this->log_gestor_model->get_log($this->input->post('series'), $this->input->post('set_serie'), $this->input->post('ult_mov') == 'show');
		
		$datos['titulo_modulo'] = 'Consulta información series';


		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_log_gestor_dth_view', $datos);
		$this->load->view('app_footer', $datos);
	}

	public function trafico_por_mes()
	{
		$this->load->model('analisis_series_model');
		$this->load->model('acl_model');

		$datos = array(
				'combo_mes' => $this->analisis_series_model->get_meses_trafico(),
				'datos_trafico' => $this->analisis_series_model->get_trafico_mes($this->input->post('series'), $this->input->post('meses')),
			);

		$datos['titulo_modulo'] = 'Consulta información series';

		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_series_trafico_view', $datos);
		$this->load->view('app_footer', $datos);
	}

	public function ajax_trafico_mes($serie = '', $meses = '')
	{
		$this->load->model('analisis_series_model');
		$data = array(
				'datos' => json_encode($this->analisis_series_model->get_trafico_mes2($serie, $meses)),
			);
		$this->load->view('stock_sap/ajax_trafico_view', $data);
	}
}

/* End of file analisis_series.php */
/* Location: ./application/controllers/analisis_series.php */