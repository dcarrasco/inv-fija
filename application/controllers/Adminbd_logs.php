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
 * Clase Controller Revision de logs servidor
 * *
 * @category CodeIgniter
 * @package  AdministradorBD
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Adminbd_logs extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'j8hdajhJKHHu12';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('adminbd');

		$this->arr_menu = [
			'borra_log' => [
				'url'   => "{$this->router->class}/borra_log",
				'texto' => lang('adminbd_log_menu_borra'),
				'icon'  => 'database',
			],
			'indices' => [
				'url'   => "{$this->router->class}/indices",
				'texto' => lang('adminbd_log_menu_indices'),
				'icon'  => 'database',
			],
		];
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->borra_log();
	}


	// --------------------------------------------------------------------

	/**
	 * Permite mostrar log de borrado de logs sql
	 *
	 * @return void
	 */
	public function borra_log()
	{
		$this->_show_file('../upload/log_borra_log.log', 'borra_log');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite mostrar log de regeneracion de indices
	 *
	 * @return void
	 */
	public function indices()
	{
		$this->_show_file('../upload/log_indices_bd_logistica.log', 'indices');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite mostrar un archivo de texto
	 *
	 * @param string $archivo Ruta y nombre del archivo a mostrar
	 * @param string $menu    Opcion de menu seleccionada
	 * @return void
	 */
	private function _show_file($archivo = '', $menu = '')
	{
		$log_file = file_exists($archivo) ? file($archivo) : ['--- Archivo no encontrado ---'];

		app_render_view('admindb/logs', [
			'menu_modulo' => ['menu' => $this->arr_menu, 'mod_selected' => $menu],
			'log_file'    => $log_file,
		]);
	}

}
/* End of file adminbd_logs.php */
/* Location: ./application/controllers/adminbd_logs.php */
