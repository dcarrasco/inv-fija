<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminbd_queries extends CI_Controller {

	public $llave_modulo = 'jk8jkljKLJH28';


	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminbd_model');
		$this->lang->load('adminbd');
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
		$this->queries();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite digitar una hoja de detalle de inventario
	 *
	 * @param  integer $hoja Numero de la hoja a visualizar
	 * @return none
	 */
	public function queries()
	{
		$datos = array(
			'queries_data' => $this->adminbd_model->get_running_queries(),
		);

		app_render_view('admindb/queries', $datos);

	}

	// --------------------------------------------------------------------





}
/* End of file Adminbd_queries.php */
/* Location: ./application/controllers/Adminbd_queries.php */
