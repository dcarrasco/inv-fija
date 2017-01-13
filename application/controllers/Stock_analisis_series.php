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
		$datos = array(
			'hist'      => '',
			'desp'      => '',
			'stock'     => '',
			'stock_scl' => '',
			'trafico'   => '',
			'log_gdth'  => array(),
		);

		$is_form_valid = $this->form_validation->set_rules($this->analisis_series_model->validation_analisis)->run();

		if ($is_form_valid)
		{
			$series = set_value('series');

			$analisis_model = new analisis_series_model;
			$gestor_model   = new log_gestor_model;

			$arr_reportes = array(
				'show_mov' => array(
					'class'  => $analisis_model,
					'method' => 'get_historia',
					'params' => array($series),
				),
				'show_despachos' => array(
					'class'  => $analisis_model,
					'method' => 'get_despacho',
					'params' => array($series),
				),
				'show_stock_sap' => array(
					'class'  => $analisis_model,
					'method' => 'get_stock_sap',
					'params' => array($series),
				),
				'show_stock_scl' => array(
					'class'  => $analisis_model,
					'method' => 'get_stock_scl',
					'params' => array($series),
				),
				'show_trafico' => array(
					'class'  => $analisis_model,
					'method' => 'get_trafico',
					'params' => array($series),
				),
				'show_gdth' => array(
					'class'  => $gestor_model,
					'method' => 'get_log',
					'params' => array($series, 'serie_deco', 'log', "'ALTA', 'BAJA'", FALSE),
				),
			);

			foreach ($arr_reportes as $input => $reporte)
			{
				if ($this->input->post($input) === 'show')
				{
					$datos['datos_'.$input] = call_user_func_array(array($reporte['class'], $reporte['method']), $reporte['params']);
				}
			}
		}
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
				'datos_trafico' => $this->analisis_series_model->get_trafico_mes($this->input->post('series'), $this->input->post('meses'), $tipo),
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
			->set_output(json_encode($this->analisis_series_model->get_trafico_mes($serie, $meses, $tipo)));
	}


}
/* End of file stock_analisis_series.php */
/* Location: ./application/controllers/stock_analisis_series.php */