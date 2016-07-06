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
 * Clase Controller Reporte de consumos TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_panel extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '98njhsg6kn56ks';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toa_model');
		$this->lang->load('toa');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		return $this->panel();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function panel()
	{
		$empresa = new Empresa_toa();

		$this->form_validation->set_rules($this->toa_model->panel_validation);

		$datos = array(
			'combo_empresas' => $empresa->find('list'),
			'form_validated' => FALSE,
		);

		if ($this->form_validation->run() === TRUE)
		{
			$sel_empresa = set_value('empresa');
			$sel_mes     = set_value('mes');

			$datos['form_validated'] = TRUE;
			$datos['cant_peticiones_empresa'] = $this->toa_model->gchart_data($this->toa_model->peticiones_empresa($sel_empresa, $sel_mes));
			$datos['monto_peticiones_empresa'] = $this->toa_model->gchart_data($this->toa_model->peticiones_empresa($sel_empresa, $sel_mes, 'monto'));
			$datos['cant_tecnicos_empresa'] = $this->toa_model->gchart_data($this->toa_model->tecnicos_empresa($sel_empresa, $sel_mes));
			$datos['stock_empresa'] = $this->toa_model->gchart_data($this->toa_model->stock_empresa($sel_empresa, $sel_mes));
			$datos['stock_tecnicos_empresa'] = $this->toa_model->gchart_data($this->toa_model->stock_tecnicos_empresa($sel_empresa, $sel_mes));
		}

		app_render_view('toa/panel_empresa', $datos);
	}


}
/* End of file Toa_panel.php */
/* Location: ./application/controllers/Toa_panel.php */