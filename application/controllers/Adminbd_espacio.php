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
class Adminbd_espacio extends CI_Controller {

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
		$bases_datos = array(
			'BD_Controles',
			'BD_Inventario',
			'BD_Logistica',
			'BD_Planificacion',
			'BD_Sucursales',
			'BD_TOA',
		);

		$arr_espacio = collect($bases_datos)->map(function($base) {
			return collect($this->adminbd_model->get_tables_sizes($base))
				->map(function($elem) use ($base) {
					return array_merge(array('DataBase' => $base), $elem);
				})
				->all();
		})->reduce(function($new_array, $elem) {
			return array_merge($new_array, $elem);
		}, array());

		function sort_size($elem_a, $elem_b) {
			return $elem_a['TotalSpaceKB'] < $elem_b['TotalSpaceKB'] ? 1 : -1;
		}

		uasort($arr_espacio, 'sort_size');

		$datos = array(
			'tablas' => $arr_espacio,
		);

		app_render_view('admindb/espacio', $datos);
	}



}
/* End of file Adminbd_espacio.php */
/* Location: ./application/controllers/Adminbd_espacio.php */
