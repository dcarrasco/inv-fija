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

use test\test_case;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Reportes de stock
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Tests extends Controller_base {

	/**
	 * Test case para realizar las pruebas
	 *
	 * @var test_case
	 */
	protected $test;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! is_cli() && ENVIRONMENT === 'production')
		{
			show_404();
		}

		$this->test = new test_case();
	}

	// --------------------------------------------------------------------

	/**
	 * Accion por defecto, resumen de los tests
	 *
	 * @return void
	 */
	public function index()
	{
		$this->test->resumen_all_files();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra detalle de los tests
	 *
	 * @return void
	 */
	public function detalle()
	{
		$this->test->all_files();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra detalle de los tests
	 *
	 * @return void
	 */
	public function file($filename = '', $method = '')
	{
		$this->test->all_files(TRUE, $filename, $method);
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra opciones de linea de comandos de los tests
	 *
	 * @return void
	 */
	public function help()
	{
		$this->test->print_cli_help();
	}

	// --------------------------------------------------------------------

	/**
	 * Reporte de cobertura de los tests
	 *
	 * @return void
	 */
	public function coverage()
	{
		$this->test->print_coverage();
	}

	// --------------------------------------------------------------------

	/**
	 * Estadisticas de los archivos de la aplicacion
	 *
	 * @param  string $detalle Indica si mostrara detalle de los archivos
	 * @param  string $sort    Columna para ordenar el reporte de detalle
	 *
	 * @return void
	 */
	public function rake($detalle = '', $sort = '')
	{
		$this->test->rake($detalle, $sort);
	}

}
// End of file tests.php
// Location: ./controllers/tests.php
