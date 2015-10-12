<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Despachos_retail extends CI_Controller {

	public $llave_modulo = 'iojms9128_$$21';


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
	 * @param  none
	 * @return none
	 */
	public function index()
	{
		$this->retail();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite digitar una hoja de detalle de inventario
	 *
	 * @param  integer $hoja Numero de la hoja a visualizar
	 * @return none
	 */
	public function retail($rut = NULL)
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

	// --------------------------------------------------------------------





}
/* End of file despachos_retail.php */
/* Location: ./application/controllers/despachos_retail.php */
