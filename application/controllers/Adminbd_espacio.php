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
		$bases_datos = [
			'BD_Controles',
			'BD_Inventario',
			'BD_Logistica',
			'BD_Planificacion',
			'BD_Sucursales',
			'BD_TOA',
		];

		$arr_espacio = collect($bases_datos)
			->map(function($base) {
				return $this->adminbd_model->get_tables_sizes($base);
			})
			->flatten(1)
			->sort(function($elem_a, $elem_b) {
				return $elem_a['TotalSpaceKB'] < $elem_b['TotalSpaceKB'] ? 1 : -1;
			});

		$arr_sum = [
			'RowCounts'     => 0,
			'TotalSpaceKB'  => 0,
			'UsedSpaceKB'   => 0,
			'UnusedSpaceKB' => 0,
		];

		$arr_sum = collect($arr_sum)->map(function($sum_init, $sum_key) use ($arr_espacio) {
			return $arr_espacio->sum($sum_key);
		})->all();


		app_render_view('admindb/espacio', [
			'tablas'  => $arr_espacio,
			'arr_sum' => $arr_sum,
		]);
	}

}
/* End of file Adminbd_espacio.php */
/* Location: ./application/controllers/Adminbd_espacio.php */
