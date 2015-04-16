<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_analisis_series extends CI_Controller {

	public $llave_modulo = 'analisis_series';

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('stock');

		$this->app_common->form_validation_config();
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
		$this->historia();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar la varios reportes asociados a una o varias series
	 *
	 * @param  none
	 * @return none
	 */
	public function historia()
	{

		$this->load->model('analisis_series_model');
		$this->load->model('acl_model');
		$datos = array();

		if ($this->form_validation->run('stock_analisis_series/historia'))
		{

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
		}

		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_series_view', $datos);
		$this->load->view('app_footer', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega el tráfico por mes de un o varias series
	 *
	 * @param  none
	 * @return none
	 */
	public function trafico_por_mes()
	{
		$this->load->model('analisis_series_model');
		$this->load->model('acl_model');

		$tipo =set_value('sel_tipo');

		$datos = array(
				'combo_mes' => $this->analisis_series_model->get_meses_trafico(),
				'datos_trafico' => $this->analisis_series_model->get_trafico_mes2($this->input->post('series'), $this->input->post('meses'), $tipo),
			);

		$datos['titulo_modulo'] = 'Consulta información series';

		$this->load->view('app_header', $datos);
		$this->load->view('stock_sap/analisis_series_trafico_view', $datos);
		$this->load->view('app_footer', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos en formato json del trafico por mes de una o varias series
	 *
	 * @param  none
	 * @return none
	 */
	public function ajax_trafico_mes($serie = '', $meses = '', $tipo = 'imei')
	{
		$this->load->model('analisis_series_model');

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->analisis_series_model->get_trafico_mes2($serie, $meses, $tipo)));
	}


}
/* End of file stock_analisis_series.php */
/* Location: ./application/controllers/stock_analisis_series.php */