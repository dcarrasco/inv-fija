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
 * Clase Controller Revision de queries en ejecucion
 * *
 * @category CodeIgniter
 * @package  AdministradorBD
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Adminbd_queries extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var string
	 */
	public $llave_modulo = 'jk8jkljKLJH28';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
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
	 * @return void
	 */
	public function index()
	{
		$this->queries();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra listado de queries en ejecucion
	 *
	 * @return void
	 */
	public function queries()
	{
		$datos = array(
			'queries_data' => $this->adminbd_model->get_running_queries(),
		);

		app_render_view('admindb/queries', $datos);

	}



}
/* End of file Adminbd_queries.php */
/* Location: ./application/controllers/Adminbd_queries.php */