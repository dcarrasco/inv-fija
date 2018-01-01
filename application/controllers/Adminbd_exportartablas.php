<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
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
 * Clase Controller Exportar tablas
 * *
 * @category CodeIgniter
 * @package  AdministradorBD
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Adminbd_exportartablas extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '9js7j6809pt43j01';

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'adminbd';

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
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->exportar();
	}


	// --------------------------------------------------------------------

	/**
	 * Exportar una tabla del sistema en formato CVS
	 *
	 * @return void
	 */
	public function exportar()
	{
		form_validation(Adminbd::create()->rules_exportar_tablas);

		app_render_view('admindb/exportar_tablas', [
			'combo_tablas'  => Adminbd::create()->table_list(),
			'combo_campos'  => Adminbd::create()->fields_list(request('tabla')),
			'result_string' => Adminbd::create()->table_to_csv(request('tabla')),
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Recuperar listado de campos de una tabla
	 *
	 * @param  string $tabla Nombre de la tabla
	 * @return void
	 */
	public function ajax_campos($tabla = '')
	{
		$this->output
			->set_content_type('text')
			->set_output(form_print_options(Adminbd::create()->fields_list($tabla)));
	}


}
// End of file Adminbd_exportartablas.php
// Location: ./controllers/Adminbd_exportartablas.php
