<?php

namespace test;

/**
 * Clase de testeo
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_case {

	use has_rake;
	use has_assertions;
	use has_cli_output;
	use has_html_output;


	/**
	 * Colores CLI
	 */
	const CLI_COLOR_F_NORMAL   = "\033[0m";
	const CLI_COLOR_F_BLANCO   = "\033[37m";
	const CLI_COLOR_F_VERDE    = "\033[1;32m";
	const CLI_COLOR_F_ROJO     = "\033[31m";
	const CLI_COLOR_F_AMARILLO = "\033[33m";
	const CLI_COLOR_B_NORMAL   = "\033[49m";
	const CLI_COLOR_B_ROJO     = "\033[41m";
	const CLI_COLOR_B_VERDE    = "\033[42m";
	const CLI_COLOR_VERDE_OSC  = "\033[0;32m";
	const CLI_FONDO_GRIS       = "\033[47m";
	const CLI_REVERSE_COLOR    = "\033[7m";

	const STATS_KEY_NAME    = 'Name';
	const STATS_KEY_LINES   = 'Lines';
	const STATS_KEY_LOC     = 'LOC';
	const STATS_KEY_CLASSES = 'Classes';
	const STATS_KEY_METHODS = 'Methods';
	const STATS_KEY_MC      = 'M/C';
	const STATS_KEY_LOCM    = 'LOC/M';
	const STATS_KEY_CONTENT = 'Content';

	/**
	 * Instancia codeigniter
	 *
	 * @var object
	 */
	protected $ci;

	/**
	 * Instancia unit test de codeigniter
	 *
	 * @var object
	 */
	protected $unit;

	/**
	 * Contiene el backtrace de todos los test pasa medir % coverage
	 *
	 * @var array
	 */
	protected $backtrace = [];

	/**
	 * Tiempo de ejecución del proceso
	 */
	protected $process_duration;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('unit_test');
		$this->ci->output->enable_profiler(FALSE);

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$this->unit = $this->ci->unit;

		$this->backtrace = collect([]);

		$this->unit->use_strict(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el stack de backtrace
	 *
	 * @return Collection
	 */
	public function get_backtrace()
	{
	    return $this->backtrace;
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los resultados de los tests
	 *
	 * @return mixed
	 */
	protected function print_results()
	{
		$results = $this->results();

		return is_cli() ? $this->print_cli_result($results) : $this->print_html_result($results);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los resultados de los tests
	 *
	 * @return mixed
	 */
	protected function print_resumen_results()
	{
		return is_cli() ? $this->print_cli_resumen_result() : $this->print_html_result($this->results());
	}

	// --------------------------------------------------------------------

	/**
	 * Genera coleccion de resultado de los tests
	 *
	 * @return Collection
	 */
	protected function results()
	{
		$cant = 0;

		return $results = collect($this->unit->result())
			// recupera nombre de archivo y numero de linea de campo notas
			->map(function($result) {
				$result['Notes'] = unserialize($result['Notes']);
				$result['File Name'] = array_get($result, 'Notes.file');
				$result['Line Number'] = array_get($result, 'Notes.line');

				return $result;
			})
			// numera las lineas
			->map(function($result) use (&$cant) {
				return array_merge(['n' => ++$cant], $result);
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Testea todos los metodos (o tests) de un clase test
	 *
	 * @return void
	 */
	public function all_methods()
	{
		$test_methods = collect(get_class_methods(get_class($this)))
			->filter(function($method) { return substr($method, 0, 5) === 'test_'; })
			->each(function($method)   { $this->{$method}(); });
	}

	// --------------------------------------------------------------------

	/**
	 * Testea todos los archivos de clase test en carpeta de tests
	 * e imprime los resultados de todos los tests.
	 *
	 * @return void
	 */
	public function all_files($detalle = TRUE)
	{
		get_instance()->load->helper('number');

		$tiempo_inicio = new \DateTime();

		$this->backtrace = collect(scandir(APPPATH.'/tests'))
			->filter(function($file) { return substr($file, -4) === '.php'; })
			->map(function($file) { return substr($file, 0, strlen($file) - 4); })
			->map(function($file) {
				$test_object = new $file();
				$test_object->all_methods();
				return $test_object->get_backtrace()->flatten();
			})->flatten();

		$tiempo_fin = new \DateTime();
		$this->process_duration = $tiempo_inicio->diff($tiempo_fin);

		return $detalle ? $this->print_results() : $this->print_resumen_results();
	}

	// --------------------------------------------------------------------

	public function resumen_all_files()
	{
		return $this->all_files(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta un test
	 *
	 * @param  mixed  $test       Test a ejecutar
	 * @param  mixed  $result     Resultado esperado
	 * @param  string $test_type  Tipo de assert
	 * @param  array  $test_notes Notas del test
	 * @return mixed
	 */
	public function test($test, $result, $test_type = 'assert_equals', $test_notes = [])
	{
		$debug = debug_backtrace();

		$this->backtrace->add_item(
			collect($debug)->map(function($stack) {
				return array_get($stack, 'file').':'.array_get($stack, 'line');
			})
		);

		$file = explode('/', str_replace('\\', '/', array_get($debug, '1.file')));
		$file = array_get($file, count($file)-1);

		$test_name = array_get($debug, '2.function');
		$test_name = str_replace('_', ' ', substr($test_name, 5, strlen($test_name)));

		$notes = array_merge($test_notes, [
			'file'       => $file,
			'full_file'  => array_get($debug, '1.file'),
			'line'       => array_get($debug, '1.line'),
			'class'      => array_get($debug, '2.class'),
			'class_type' => array_get($debug, '2.type'),
			'function'   => array_get($debug, '2.function'),
			'test_type'  => $test_type,
			'test'       => $test,
			'result'     => $result,
		]);

		return $this->unit->run($test, $result, $test_name, serialize($notes));
	}

	// --------------------------------------------------------------------

	public function print_coverage()
	{
		$backtrace = $this->sum_backtrace_lines();
	    dump($backtrace_lines);
	}

	// --------------------------------------------------------------------

	public function sum_backtrace_lines()
	{
		return $this->backtrace
			// filtra aquellas lineas que no son de APPPATH
			->filter(function($line) {
				return strpos($line, APPPATH) !== FALSE;
			})
			// ->filter(function($line) {
			// 	return strpos($line, APPPATH.'tests/') === FALSE;
			// })
			->reduce(function($total, $line) {
				list($file_name, $file_line) = explode(':', $line);
				return $total->add_item([
					'file'  => $file_name,
					'line'  => $file_line,
					'count' => array_get($total->item($line), 'count', 0) + 1]
				, $line);
			}, collect())
			->reduce(function($total, $line_data) {
				return $total->add_item($total->get($line_data['file'], collect())->add_item($line_data['count'], $line_data['line']), $line_data['file']);
			}, collect());
	}

}
