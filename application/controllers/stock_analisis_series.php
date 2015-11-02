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
 * Clase Controller Analisis series
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_analisis_series extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'analisis_series';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('stock');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->historia();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar la varios reportes asociados a una o varias series
	 *
	 * @return void
	 */
	public function historia()
	{

		$this->load->model('analisis_series_model');
		$this->load->model('acl_model');
		$datos = array();

		if ($this->input->post('show_mov') === 'show')
		{
			$datos['hist'] = $this->analisis_series_model->get_historia($this->input->post('series'));
		}

		if ($this->input->post('show_despachos') === 'show')
		{
			$datos['desp'] = $this->analisis_series_model->get_despacho($this->input->post('series'));
		}

		if ($this->input->post('show_stock_sap') === 'show')
		{
			$datos['stock'] = $this->analisis_series_model->get_stock_sap($this->input->post('series'));
		}

		if ($this->input->post('show_stock_scl') === 'show')
		{
			$datos['stock_scl'] = $this->analisis_series_model->get_stock_scl($this->input->post('series'));
		}

		if ($this->input->post('show_trafico') === 'show')
		{
			$datos['trafico'] = $this->analisis_series_model->get_trafico($this->input->post('series'));
		}

		if ($this->input->post('show_gdth') === 'show')
		{
			$this->load->model('log_gestor_model');
			$datos['log_gdth'] = $this->log_gestor_model->get_log($this->input->post('series'), 'serie_deco', 'log', "'ALTA', 'BAJA'", FALSE);
		}

		$this->form_validation->set_rules('series', 'Series', '');
		$this->form_validation->set_rules('show_mov', 'Series', '');
		$this->form_validation->set_rules('ult_mov', 'Series', '');
		$this->form_validation->set_rules('show_despachos', 'Series', '');
		$this->form_validation->set_rules('show_stock_sap', 'Series', '');
		$this->form_validation->set_rules('show_stock_scl', 'Series', '');
		$this->form_validation->set_rules('show_trafico', 'Series', '');
		$this->form_validation->set_rules('show_gdth', 'Series', '');
		$this->form_validation->run();

		app_render_view('stock_sap/analisis_series_view', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega el tráfico por mes de un o varias series
	 *
	 * @return void
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

		app_render_view('stock_sap/analisis_series_trafico_view', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos en formato json del trafico por mes de una o varias series
	 *
	 * @return void
	 */

	/**
	 * Devuelve datos en formato json del trafico por mes de una o varias series
	 *
	 * @param  string $serie Numero de serie
	 * @param  string $meses Meses a recuperar
	 * @param  string $tipo  Tipo de registro a comparar (imei o numero de celular)
	 * @return void
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