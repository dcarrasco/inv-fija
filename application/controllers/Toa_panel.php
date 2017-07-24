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

use Toa\Empresa_toa;

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
class Toa_panel extends Controller_base {

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
		$is_form_valid = $this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->panel_validation)
			->run();

		$datos = [
			'combo_empresas' => array_merge(Empresa_toa::create()->find('list'), ['***' => 'Todas']),
			'form_validated' => FALSE,
		];

		if ($is_form_valid)
		{
			$sel_empresa = request('empresa');
			$sel_mes     = request('mes');

			$datos = array_merge($datos, [
				'form_validated'               => TRUE,
				'cant_peticiones_empresa'      => $this->toa_model->get_resumen_panel_gchart('SAP_Q_PET', $sel_empresa, $sel_mes),
				'cant_peticiones_empresa_proy' => $this->toa_model->get_resumen_panel_proyeccion('SAP_Q_PET', $sel_empresa, $sel_mes),

				'monto_peticiones_empresa'      => $this->toa_model->get_resumen_panel_gchart('SAP_MONTO_PET', $sel_empresa, $sel_mes),
				'monto_peticiones_empresa_proy' => $this->toa_model->get_resumen_panel_proyeccion('SAP_MONTO_PET', $sel_empresa, $sel_mes),

				'cant_peticiones_instala' => $this->toa_model->get_resumen_panel_gchart(['sap' => 'SAP_Q_PET_INSTALA', 'toa' => 'TOA_Q_PET_INSTALA'], $sel_empresa, $sel_mes),
				'cant_peticiones_repara'  => $this->toa_model->get_resumen_panel_gchart(['sap'  => 'SAP_Q_PET_REPARA', 'toa' => 'TOA_Q_PET_REPARA'], $sel_empresa, $sel_mes),
				'cant_tecnicos_empresa'   => $this->toa_model->get_resumen_panel_gchart(['sap' => 'SAP_Q_TECNICOS', 'toa' => 'TOA_Q_TECNICOS'], $sel_empresa, $sel_mes),
				'stock_empresa'           => $this->toa_model->get_resumen_panel_gchart('SAP_MONTO_STOCK_ALM', $sel_empresa, $sel_mes),
				'stock_tecnicos_empresa'  => $this->toa_model->get_resumen_panel_gchart('SAP_MONTO_STOCK_TEC', $sel_empresa, $sel_mes),

				'usage_peticiones_instala' => $this->toa_model->get_resumen_panel_usage('SAP_Q_PET_INSTALA', 'TOA_Q_PET_INSTALA', $sel_empresa, $sel_mes),
				'usage_peticiones_repara'  => $this->toa_model->get_resumen_panel_usage('SAP_Q_PET_REPARA', 'TOA_Q_PET_REPARA', $sel_empresa, $sel_mes),
				'usage_cant_tecnicos'      => $this->toa_model->get_resumen_panel_usage('SAP_Q_TECNICOS', 'TOA_Q_TECNICOS', $sel_empresa, $sel_mes),

				'proy_q_pet'     => fmt_cantidad($this->toa_model->get_resumen_usage('SAP_Q_PET', $sel_empresa, $sel_mes)/$this->toa_model->get_resumen_panel_porcentaje_mes('SAP_Q_PET', $sel_empresa, $sel_mes)),
				'proy_monto_pet' => fmt_cantidad($this->toa_model->get_resumen_usage('SAP_MONTO_PET', $sel_empresa, $sel_mes)/$this->toa_model->get_resumen_panel_porcentaje_mes('SAP_MONTO_PET', $sel_empresa, $sel_mes)),
			]);

		}

		app_render_view('toa/panel_empresa', $datos);
	}


}
/* End of file Toa_panel.php */
/* Location: ./application/controllers/Toa_panel.php */
