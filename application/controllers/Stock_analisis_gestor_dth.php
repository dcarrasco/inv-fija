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
 * Clase Controller Analisis gestor DTH
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_analisis_gestor_dth extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'fhzcJJPL12$/!';

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
		$this->log_gestor();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function log_gestor()
	{

		$this->load->model('log_gestor_model');
		$datos = [];

		$this->form_validation->set_rules($this->log_gestor_model->validation_rules)->run();

		$arr_filtro_cas = [];

		if (request('tipo_op_alta') === 'alta')
		{
			array_push($arr_filtro_cas, "'ALTA'");
		}

		if (request('tipo_op_baja') === 'baja')
		{
			array_push($arr_filtro_cas, "'BAJA'");
		}

		app_render_view('stock_sap/analisis_log_gestor_dth_view', [
			'log' => $this->log_gestor_model->get_log(
				request('series'),
				request('set_serie'),
				request('tipo_reporte'),
				implode(', ', $arr_filtro_cas),
				request('ult_mov') === 'show'
			)
		]);
	}

}
/* End of file stock_analisis_gestor_dth.php */
/* Location: ./application/controllers/stock_analisis_gestor_dth.php */
