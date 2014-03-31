<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_analisis_series extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('analisis_series');

		if (ENVIRONMENT != 'production')
		{
			//$this->output->enable_profiler(TRUE);
		}
	}

	// --------------------------------------------------------------------

	public function index()
	{
		$this->historia();
	}

	// --------------------------------------------------------------------

	public function historia()
	{

		$this->load->model('analisis_series_model');
		$this->load->model('acl_model');
		$datos = array();

		if ($this->input->post('show_mov') == 'show')
		{
			$datos['hist'] = $this->analisis_series_model->get_historia($this->input->post('series'));
		}

		if ($this->input->post('show_despachos') == 'show')
		{
			$datos['desp'] = $this->analisis_series_model->get_despacho($this->input->post('series'));
		}

		if ($this->input->post('show_stock_sap') == 'show')
		{
			$datos['stock'] = $this->analisis_series_model->get_stock_sap($this->input->post('series'));
		}

		if ($this->input->post('show_stock_scl') == 'show')
		{
			$datos['stock_scl'] = $this->analisis_series_model->get_stock_scl($this->input->post('series'));
		}

		if ($this->input->post('show_trafico') == 'show')
		{
			$datos['trafico'] = $this->analisis_series_model->get_trafico($this->input->post('series'));
		}

		if ($this->input->post('show_gdth') == 'show')
		{
			$this->load->model('log_gestor_model');
			$datos['log_gdth'] = $this->log_gestor_model->get_log($this->input->post('series'), 'serie_deco', 'log', "'ALTA', 'BAJA'", FALSE);
		}

		$datos['titulo_modulo'] = 'Consulta información series';

		$this->form_validation->set_rules('series', 'Series', '');
		$this->form_validation->set_rules('show_mov', 'Series', '');
		$this->form_validation->set_rules('ult_mov', 'Series', '');
		$this->form_validation->set_rules('show_despachos', 'Series', '');
		$this->form_validation->set_rules('show_stock_sap', 'Series', '');
		$this->form_validation->set_rules('show_stock_scl', 'Series', '');
		$this->form_validation->set_rules('show_trafico', 'Series', '');
		$this->form_validation->set_rules('show_gdth', 'Series', '');
		$this->form_validation->run();

		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_series_view', $datos);
		$this->load->view('app_footer', $datos);
	}

	// --------------------------------------------------------------------

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

	// --------------------------------------------------------------------

	public function ajax_trafico_mes($serie = '', $meses = '')
	{
		$this->load->model('analisis_series_model');
		$data = array(
				'datos' => json_encode($this->analisis_series_model->get_trafico_mes2($serie, $meses)),
			);
		$this->load->view('stock_sap/ajax_trafico_view', $data);
	}


}
/* End of file stock_analisis_series.php */
/* Location: ./application/controllers/stock_analisis_series.php */