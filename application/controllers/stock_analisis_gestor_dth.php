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
class Stock_analisis_gestor_dth extends CI_Controller {

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
		$datos = array();

		$this->form_validation->set_rules('series', 'Series', '');
		$this->form_validation->set_rules('set_serie', 'Series', '');
		$this->form_validation->set_rules('tipo_reporte', 'Series', '');
		$this->form_validation->set_rules('ult_mov', 'Series', '');
		$this->form_validation->set_rules('tipo_op_alta', 'Series', '');
		$this->form_validation->set_rules('tipo_op_baja', 'Series', '');
		$this->form_validation->run();

		$arr_filtro_cas = array();

		if ($this->input->post('tipo_op_alta') === 'alta')
		{
			array_push($arr_filtro_cas, "'ALTA'");
		}

		if ($this->input->post('tipo_op_baja') === 'baja')
		{
			array_push($arr_filtro_cas, "'BAJA'");
		}

		$datos['log'] = $this->log_gestor_model->get_log($this->input->post('series'), $this->input->post('set_serie'), $this->input->post('tipo_reporte'), implode(', ', $arr_filtro_cas), $this->input->post('ult_mov') === 'show');

		app_render_view('stock_sap/analisis_log_gestor_dth_view', $datos);
	}




}
/* End of file stock_analisis_gestor_dth.php */
/* Location: ./application/controllers/stock_analisis_gestor_dth.php */