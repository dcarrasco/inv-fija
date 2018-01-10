<?php

namespace test;

use ReflectionClass;

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
	 * Devuelve un controller de codeigniter, sin usar el constructor
	 *
	 * @param  string $class Clase del controller a cargar
	 * @return mixed
	 */
	public function load_controller($class)
	{
		$class_object = (new ReflectionClass($class))->newInstanceWithoutConstructor();

		$ci_properties = ['benchmark', 'hooks', 'config', 'log', 'utf8', 'uri', 'router', 'output', 'security', 'input', 'lang', 'load', 'db', 'form_validation', 'session', 'parser', 'unit'];

		collect($ci_properties)->each(function($property) use (&$class_object) {
			$class_object->{$property} =& $this->ci->{$property};
		});

		return $class_object;
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
			->each(function($method) {$this->{$method}(); });
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

				xdebug_start_code_coverage();
				$test_object->all_methods();
				$file_backtrace = xdebug_get_code_coverage();
				xdebug_stop_code_coverage();

				return $file_backtrace;
			});

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

		$notes = array_merge([
			'file'       => $file,
			'full_file'  => array_get($debug, '1.file'),
			'line'       => array_get($debug, '1.line'),
			'class'      => array_get($debug, '2.class'),
			'class_type' => array_get($debug, '2.type'),
			'function'   => array_get($debug, '2.function'),
			'test_type'  => $test_type,
			'test'       => $test,
			'result'     => $result,
		], $test_notes);

		return $this->unit->run($test, $result, $test_name, serialize($notes));
	}

	// --------------------------------------------------------------------

	public function print_coverage()
	{
		$this->all_files(FALSE);

		$total_coverage = collect([]);
		$backtrace = $this->backtrace;

		$files = $this->backtrace
			->map(function($test_file) { return collect($test_file)->keys();})
			->flatten()
			->sort()
			->unique()
			->map_with_keys(function($file) use ($backtrace) {
				return [$file => $file];
			})
			->map(function($file) use ($backtrace) {
				return $backtrace->map(function($test_file) use ($file) {
					return array_get($test_file, $file, []);
				});
			})
			->map(function($usage_collection, $file) {
				return collect(collect($usage_collection)->reduce(function($elem1, $elem2) {
					return $this->sum_backtrace_lines($elem1, $elem2);
				}, []));
			})
			->map(function($usage_collection) {
				return $usage_collection->map(function($usage, $line) {
					return ['usage' => $usage, 'line' => $line];
				})->sort(function($usage1, $usage2) {
					return $usage1['line'] > $usage2['line'];
				})->map(function($usage) {
					return $usage['usage'];
				})->all();
			});

		dump($files);

		// dump('********************',$total_coverage);
	}

	// --------------------------------------------------------------------

	public function sum_backtrace_lines($archivo1 = [], $archivo2 = [])
	{
		$suma = $archivo1;

		// suma elementos comunes
		foreach($suma as $num_linea => $cantidad)
		{
			if (array_key_exists($num_linea, $archivo2))
			{
				$suma[$num_linea] = $suma[$num_linea] + $archivo2[$num_linea];
			}
		}

		// agrega elementos nuevos en archivo2
		foreach($archivo2 as $num_linea => $cantidad)
		{
			if ( ! array_key_exists($num_linea, $suma))
			{
				$suma[$num_linea] = $cantidad;
			}
		}

		return $suma;
	}

}
