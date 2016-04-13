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
class Toa_controles extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'oknsk82js7s';

	/**
	 * Menu de opciones
	 *
	 * @var  array
	 */
	private $_arr_menu = array();

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('movs_fija_model');
		$this->lang->load('toa');

		$this->_arr_menu = array(
			'tecnicos' => array(
				'url'   => $this->router->class . '/tecnicos',
				'texto' => $this->lang->line('toa_controles_tecnicos'),
				'icon'  => 'user'
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		return $this->tecnicos();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function tecnicos($empresa = NULL, $mes = NULL)
	{
		$empresa = new Empresa_toa();
		$stock_sap_fija = new Stock_sap_fija_model();

		for ($anno = 2016; $anno <= 2030; $anno++)
		{
			for ($mes = 1; $mes <= 12; $mes++)
			{
				$id_annomes = (string) $anno*100+$mes;
				$annomes    = substr($id_annomes, 0, 4).'-'.substr($id_annomes, 4, 2);
				$combo_meses[$id_annomes] = $annomes;
			}
		}

		$datos = array(
			'menu_modulo'    => array('menu' => $this->_arr_menu, 'mod_selected' => 'tecnicos'),
			'combo_empresas' => $empresa->find('list'),
			'combo_meses'    => $combo_meses,
			'control'        => $this->movs_fija_model->control_tecnicos(set_value('empresa'), set_value('mes')),
			'anomes'         => set_value('mes'),
		);

		app_render_view('toa/controles', $datos);
	}


	// --------------------------------------------------------------------

}
/* End of file Toa_controles.php */
/* Location: ./application/controllers/Toa_controles.php */