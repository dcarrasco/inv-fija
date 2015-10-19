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
 * Clase Controller Revision de facturas despachos a retail
 * *
 * @category CodeIgniter
 * @package  DespachosRetail
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Despachos_retail extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'iojms9128_$$21';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('despachos_model');
		$this->lang->load('despachos');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->retail();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra listado de los despachos a un retails
	 *
	 * @param  integer $rut_retail Numero de la RUT del retail a desplegar
	 * @return void
	 */
	public function retail($rut_retail = NULL)
	{
		$combo_max_facturas = array(
			'1' => 1,
			'3' => 3,
			'5' => 5,
			'10' => 10,
			'20' => 20,
			'30' => 30,
			'50' => 50,
		);

		$this->form_validation->set_rules('rut_retail', 'RUT retail', 'trim');
		$this->form_validation->set_rules('modelos', 'Modelos de equipos', 'trim');
		$this->form_validation->set_rules('max_facturas', 'Maximo de facturas', 'trim');
		$this->form_validation->run();

		$this->despachos_model->limite_facturas = set_value('max_facturas', 5);

		$datos = array(
			'combo_retail'       => $this->despachos_model->get_combo_rut_retail(),
			'combo_max_facturas' => $combo_max_facturas,
			'facturas'           => $this->despachos_model->get_listado_ultimas_facturas(
										set_value('rut_retail'),
										set_value('modelos')
									),

		);

		app_render_view('despachos/retail', $datos);
	}



}
/* End of file despachos_retail.php */
/* Location: ./application/controllers/despachos_retail.php */
