<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
class Adminbd_espacio extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var string
	 */
	public $llave_modulo = 'kishuh27hbsj';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
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
		$this->espacio();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra listado de queries en ejecucion
	 *
	 * @return void
	 */
	public function espacio()
	{
		$adminbd = new Adminbd();

		app_render_view('admindb/espacio', [
			'tablas'  => $adminbd->get_tables_sizes(),
			'arr_sum' => $adminbd->get_tables_sizes_sums(),
		]);
	}

}
/* End of file Adminbd_espacio.php */
/* Location: ./application/controllers/Adminbd_espacio.php */
