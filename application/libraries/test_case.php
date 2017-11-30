<?php

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
	 * Tamaño de los campos para salida CLI
	 *
	 * @var array
	 */
	protected $cli_padding = [
		'n'                 => 3,
		'File Name'         => 15,
		'Test Name'         => 30,
		'Test Datatype'     => 13,
		'Expected Datatype' => 17,
		'Result'            => 2,
	];

	/**
	 * Contiene el backtrace de todos los test pasa medir % coverage
	 *
	 * @var array
	 */
	protected $backtrace = [];

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
	 * Despliega los resultados en formato HTML
	 *
	 * @return view
	 */
	protected function print_html_result($results_collection)
	{
		$test_report = $results_collection
			->map(function($result) {
				return '<tr><td>'.collect($result)->implode('</td><td>').'</td></tr>';
			})
			->implode();

		return $this->ci->parser->parse('tests/tests', [
			'app_title'   => 'inv_fija testing',
			'base_url'    => base_url(),
			'test_report' => $test_report,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los resultados en formato CLI
	 *
	 * @return void
	 */
	protected function print_cli_result($results_collection)
	{
		$results_collection = $results_collection->map(function($result) {
			return [
				'n'                 => $result['n'],
				'File Name'         => $result['File Name'].':'.$result['Line Number'],
				'Test Name'         => $result['Test Name'],
				'Test Datatype'     => $result['Test Datatype'],
				'Expected Datatype' => $result['Expected Datatype'],
				'Result'            => $result['Result'],
			];
		});

		$padding        = $this->cli_padding($results_collection);
		$separator_line = $this->cli_separator_line($padding);
		$title_line     = $this->cli_title_line($padding);

		$results = $results_collection
			->map(function($result) {
				$result['Result'] = $this->format_result($result['Result'], TRUE);
				return $result;
			})
			->map(function($result) use ($padding) {
				return ' | '.collect($result)
					->only(['n', 'File Name', 'Test Name', 'Test Datatype', 'Expected Datatype', 'Result'])
					->map(function($result, $result_key) use ($padding) {
						return str_pad($result, $padding->get($result_key, 10));
					})
					->implode(' | ').' | ';
			})
			->implode("\n");

		echo "\n{$separator_line}{$title_line}{$separator_line}{$results}\n{$separator_line}";
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
	 * Muestra resultados en formato resumen para CLI
	 *
	 * @return void
	 */
	protected function print_cli_resumen_result()
	{
		$results_collection = $this->results();

		$results_passed = $results_collection
			->filter(function($result) {return $result['Result'] === lang('ut_passed');});

		$results_failed = $results_collection
			->filter(function($result) {return $result['Result'] === lang('ut_failed');});

		$tests  = $results_passed
			->pluck('Notes')
			->map(function($note) {return array_get($note, 'class').':'.array_get($note, 'function');})
			->unique()
			->count();

		$cant   = $results_collection->count();
		$passed = $results_passed->count();
		$failed = $results_failed->count();

		$color_passed = static::CLI_COLOR_F_BLANCO . static::CLI_COLOR_B_VERDE;
		$color_failed = static::CLI_COLOR_F_BLANCO . static::CLI_COLOR_B_ROJO;
		$color_reset  = static::CLI_COLOR_F_BLANCO . static::CLI_COLOR_B_NORMAL;

		echo "\n".$this->print_failures_details($results_collection)."\n";

		echo ($failed)
			? "{$color_failed}FAILURES!\nTests: {$cant}, Failures: {$failed}.{$color_reset}\n"
			: "{$color_passed}OK ({$tests} tests, {$passed} assertions).{$color_reset}\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Detalles de los tests con fallos
	 *
	 * @param  Collection $results Coleccion de resultados
	 * @return string
	 */
	protected function print_failures_details($results)
	{
		$count = 0;
		$failed_results = $results->filter(function($result) {
			return array_get($result, 'Result') === lang('ut_failed');
		});

		return $failed_results->count() === 0
			? ''
			: 'There was '.$failed_results->count()." failure(s):\n\n"
				.$failed_results->map(function($result) use (&$count) {
					return ++$count.') '.array_get($result, 'Notes.class').'::'.array_get($result, 'Notes.function')."\n"
						.$this->print_error_assert($result)
						.array_get($result, 'Notes.full_file').':'.array_get($result, 'Notes.line')."\n";
				})
				->implode("\n");
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve mensaje de error para un test
	 *
	 * @param  mixed $result Resultado esperado
	 * @return mixed
	 */
	public function print_error_assert($result)
	{
		switch (array_get($result, 'Expected Datatype'))
		{
			case 'Array';
							$test_value     = json_encode(array_get($result, 'Notes.test'));
							$expected_value = json_encode(array_get($result, 'Notes.result'));
							return "Failed asserting that two arrays are equal.\n"
								."  Test    : {$test_value}\n"
								."  Expected: {$expected_value}\n\n";
							break;
			case 'Integer':
			case 'int':
							$test_value     = (string) array_get($result, 'Notes.test');
							$expected_value = (string) array_get($result, 'Notes.result');
							return "Failed asserting that {$test_value} matches expected {$expected_value}.\n\n";
							break;
			case 'String':
							$test_value     = (string) array_get($result, 'Notes.test');
							$expected_value = (string) array_get($result, 'Notes.result');
							return "Failed asserting that two strings are equal.\n"
								."  Test    : \"{$test_value}\"\n"
								."  Expected: \"{$expected_value}\"\n\n";
							break;
			case 'Boolean':
							$test_value     = array_get($result, 'Notes.test') ? 'true' : 'false';
							$expected_value = array_get($result, 'Notes.result') ? 'true' : 'false';
							return "Failed asserting that {$test_value} is {$expected_value}.\n\n";
							break;
			case 'Null':
							$test_value     = array_get($result, 'Notes.test');
							return "Failed asserting that {$test_value} is null.\n\n";
							break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con anchos de columnas para despliegue CLI.
	 * En caso que el campo tenga un ancho mayor, se utiliza el
	 * ancho del campo.
	 *
	 * @param  Collection $results_collection Resultados de los tests
	 * @return array
	 */
	protected function cli_padding($results_collection)
	{
		return collect($this->cli_padding)
			->map(function($item_length, $item_name) use ($results_collection) {
				return max([$item_length,
					$results_collection->pluck($item_name)
						->map(function($test) {return strlen($test);})
						->max()
				]);
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Genera linea de separador de tabla para CLI, de acuerdo a los anchos
	 * de los campos
	 *
	 * @param  array $padding Arreglo con los anchos de los campos
	 * @return string
	 */
	protected function cli_separator_line($padding)
	{
		return ' +-'
			.$padding->map(function($item_length) {return str_pad('', $item_length, '-'); })
				->implode('-+-')
			."-+\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Genera linea de titulos de tabla para CLI, de acuerdo a los anchos
	 * de los campos
	 *
	 * @param  array $padding Arreglo con los anchos de los campos
	 * @return string
	 */
	protected function cli_title_line($padding)
	{
		return ' | '
			.$padding->map(function($item_length, $item_key) {return str_pad($item_key, $item_length); })
				->implode(' | ')
			." |\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Genera coleccion de resultado de los tests
	 *
	 * @return Collection
	 */
	protected function results()
	{
		$is_cli = is_cli();
		$cant   = 0;

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
	 * Formatea el resultado del test (passed/failed)
	 *
	 * @param  string  $value  Valor del resultado del test
	 * @param  boolean $is_cli Indicador de formateo para CLI o HTML
	 * @return string
	 */
	protected function format_result($value = '', $is_cli = FALSE)
	{
		if ($is_cli)
		{
			$color = $value === lang('ut_passed') ? static::CLI_COLOR_F_VERDE : static::CLI_COLOR_F_ROJO;

			return "{$color}{$value}".static::CLI_COLOR_F_NORMAL;
		}
		else
		{
			$label_class = $value === lang('ut_passed') ? 'success' : 'danger';

			return "<span class=\"label label-{$label_class}\">{$value}</span>";
		}
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
		$intervalo = $tiempo_inicio->diff($tiempo_fin);

		$texto_intervalo = $intervalo->s > 0
			? ($intervalo->s + $intervalo->f).' segundos'
			: ($intervalo->f*1000).' ms';

		if (is_cli())
		{
			echo "Codeigniter Unit Testing 1.0.0 by DC\n\n"
				."Tiempo: {$texto_intervalo}, Memoria: ".byte_format(memory_get_usage())."\n";
		}

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
	 * @param  mixed  $test   Test a ejecutar
	 * @param  mixed  $result Resultado esperado
	 * @param  string $test_type Tipo de assert
	 * @return mixed
	 */
	public function test($test, $result, $test_type = 'assert_equals')
	{
		$debug = debug_backtrace();

		$this->backtrace->add_item(
			collect($debug)->map(function($stack) {
				return array_get($stack, 'file').':'.array_get($stack, 'line');
			})
		);

		$file = explode('/', array_get($debug, '1.file'));
		$file = array_get($file, count($file)-1);

		$test_name = array_get($debug, '2.function');
		$test_name = str_replace('_', ' ', substr($test_name, 5, strlen($test_name)));

		$notes = [
			'file'       => $file,
			'full_file'  => array_get($debug, '1.file'),
			'line'       => array_get($debug, '1.line'),
			'class'      => array_get($debug, '2.class'),
			'class_type' => array_get($debug, '2.type'),
			'function'   => array_get($debug, '2.function'),
			'test_type'  => $test_type,
			'test'       => $test,
			'result'     => $result,
		];

		return $this->unit->run($test, $result, $test_name, serialize($notes));
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si 2 valores son iguales
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @param  mixed $result Resultado esperado
	 * @return mixed
	 */
	public function assert_equals($test, $result)
	{
		return $this->test($test, $result, 'assert_equals');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es string
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_is_string($test)
	{
		return $this->test($test, 'is_string', 'assert_is_string');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es true
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_true($test)
	{
		return $this->test($test, TRUE, 'assert_true');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es false
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_false($test)
	{
		return $this->test($test, FALSE, 'assert_false');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es NULL
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_null($test)
	{
		return $this->test($test, NULL, 'assert_null');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor contiene otro
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_contains($haystack, $needle)
	{
		$strpos = strpos($haystack, $needle);
		$test = ($strpos !== FALSE) AND is_int($strpos);

		return $this->test($test, TRUE, 'assert_contains');
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


	// --------------------------------------------------------------------

	public function rake($detalle = '')
	{
		$file_list = $this->scan_dir(APPPATH);
		$stats     = $this->file_stats($file_list, $detalle);

		echo $this->print_rake_stats($stats);
	}

	// --------------------------------------------------------------------

	public function file_stats($file_list, $detalle)
	{
		$file_stats = $file_list
			->map([$this, 'recupera_file_contents'])
			->map([$this, 'cuenta_lineas'])
			->map([$this, 'cuenta_loc'])
			->map([$this, 'cuenta_clases'])
			->map([$this, 'cuenta_metodos'])
			->map([$this, 'achica_nombre_archivo']);

		return $this->resumen_file_stats($file_stats, $detalle);
	}

	// --------------------------------------------------------------------

	public function resumen_file_stats($stats, $detalle)
	{
		return $stats
			->pluck($detalle === 'detalle' ? 'file' : 'folder')
			->sort()
			->unique()
			->map_with_keys(function($folder) {
				return [$folder => $folder];
			})
			->map(function($folder) use ($stats, $detalle) {
				return $stats->filter(function($file) use ($folder, $detalle) {
					return $file[$detalle ? 'file' : 'folder'] === $folder;
				});
			})
			->map([$this, 'calcula_stats']);
	}

	// --------------------------------------------------------------------

	public function recupera_file_contents($file)
	{
		return [
			'file'	=> $file,
			static::STATS_KEY_CONTENT => collect(explode("\n", file_get_contents($file))),
		];
	}

	// --------------------------------------------------------------------

	public function cuenta_stats($file, $stat_key, $filter)
	{
		return array_merge($file, [
			$stat_key => array_get($file, static::STATS_KEY_CONTENT, collect())
				->filter($filter)
				->count()
		]);
	}

	// --------------------------------------------------------------------

	public function cuenta_lineas($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_LINES, function($line) {return TRUE;});
	}

	// --------------------------------------------------------------------

	public function cuenta_loc($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_LOC, function($line) {
			return ! preg_match('/^\s*$|^\s*\/\*\*|\s*\*[ ]*|^\s*\/\/|<?php/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function cuenta_clases($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_CLASSES, function($line) {
			return preg_match('/^\s*class/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function cuenta_metodos($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_METHODS, function($line) {
			return preg_match('/^\s*(public|protected|private) function/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function achica_nombre_archivo($file)
	{
		$file['file']   = substr($file['file'], strlen(APPPATH), strlen($file['file']));
		$file['folder'] = array_get(explode('/', $file['file']), 0);
		$file['file']   = substr($file['file'], strlen($file['folder'])+1, strlen($file['file']));

		unset($file[static::STATS_KEY_CONTENT]);

		return $file;
	}

	// --------------------------------------------------------------------

	public function calcula_stats($item)
	{
		return [
			static::STATS_KEY_LINES   => $item->pluck(static::STATS_KEY_LINES)->sum(),
			static::STATS_KEY_LOC     => $item->pluck(static::STATS_KEY_LOC)->sum(),
			static::STATS_KEY_CLASSES => $item->pluck(static::STATS_KEY_CLASSES)->sum(),
			static::STATS_KEY_METHODS => $item->pluck(static::STATS_KEY_METHODS)->sum(),
			static::STATS_KEY_MC      => $item->pluck(static::STATS_KEY_CLASSES)->sum() === 0 ? 0 : (int) ($item->pluck(static::STATS_KEY_METHODS)->sum() / $item->pluck(static::STATS_KEY_CLASSES)->sum()),
			static::STATS_KEY_LOCM    => $item->pluck(static::STATS_KEY_METHODS)->sum() === 0 ? 0 : (int) ($item->pluck(static::STATS_KEY_LOC)->sum() / $item->pluck(static::STATS_KEY_METHODS)->sum()),
		];
	}

	// --------------------------------------------------------------------

	public function scan_dir($path)
	{
		$excluded_folders = ['.', '..', 'cache', 'config', 'language', 'logs', 'vendor', 'views'];

		return collect(scandir($path))
			->filter(function($file) use ($excluded_folders) {
				return ! in_array($file, $excluded_folders);
			})
			->map(function($file) use ($path) {
				return [
					'file'                    => $file,
					'extension'               => pathinfo($path.$file, PATHINFO_EXTENSION),
					'full_file_name'          => $path.$file,
					'file_type'               => filetype($path.$file),
					static::STATS_KEY_CONTENT => '',
				];
			})
			->filter(function($file_data) {
				return array_get($file_data, 'file_type') === 'dir' OR
					(array_get($file_data, 'file_type') === 'file' AND array_get($file_data, 'extension') === 'php');
			})
			->map(function($file_data) {
				return array_merge($file_data, [
					static::STATS_KEY_CONTENT => array_get($file_data, 'file_type') === 'dir'
						? $this->scan_dir(array_get($file_data, 'full_file_name').'/')
						: '',
				]);
			})
			->map_with_keys(function($file_data) {
				return [
					$file_data['full_file_name'] =>
						array_get($file_data, static::STATS_KEY_CONTENT) instanceof Collection
							? array_get($file_data, static::STATS_KEY_CONTENT)->all()
							: $file_data['full_file_name']
				];
			})->flatten();
	}

	// --------------------------------------------------------------------

	public function print_rake_stats($stats)
	{
		$padding = collect([
			static::STATS_KEY_NAME    => 20,
			static::STATS_KEY_LINES   => 7,
			static::STATS_KEY_LOC     => 7,
			static::STATS_KEY_CLASSES => 7,
			static::STATS_KEY_METHODS => 7,
			static::STATS_KEY_MC      => 7,
			static::STATS_KEY_LOCM    => 7,
		]);

		$padding->add_item(
			max([
				$padding->get(static::STATS_KEY_NAME),
				$stats->map(function($stat, $stat_key) {return strlen($stat_key);})->max()
			]), static::STATS_KEY_NAME);

		$totals = collect()
			->add_item(collect([collect($stats->first())
				->map(function($value, $stat_key) use ($stats) {
					return $stats->pluck($stat_key)->sum();
				})->all()
			]), 'Totals')
			->map([$this, 'calcula_stats']);

		echo "\n".$this->rake_print_title($padding);
		echo $this->rake_print_stats($stats, $padding);
		echo $this->rake_print_line($padding);
		echo $this->rake_print_stats($totals, $padding);
		echo $this->rake_print_line($padding);
	}

	// --------------------------------------------------------------------

	public function rake_print_line($padding)
	{
		return '+-'
			.$padding
				->map(function($padding) {return str_pad('', $padding, '-');})
				->implode('-+-')
			.'-+'."\n";
	}

	// --------------------------------------------------------------------

	public function rake_print_title($padding)
	{
		return $this->rake_print_line($padding)
			.'| '.$padding
				->map(function($padding, $name) {
					return str_pad($name, $padding, ' ', $name === static::STATS_KEY_NAME ? STR_PAD_RIGHT : STR_PAD_LEFT);
				})->implode(' | ')
			.' |'."\n"
			.$this->rake_print_line($padding);
	}

	// --------------------------------------------------------------------

	public function rake_print_stats($stats, $padding)
	{
		return $stats->map(function($folder, $folder_name) use ($padding) {
			return '| '
				.str_pad(ucfirst($folder_name), $padding->get(static::STATS_KEY_NAME), ' ')
				.' | '
				.collect($folder)->map(function($stat, $stat_key) use ($padding) {
					return str_pad($stat, $padding->get($stat_key), ' ', STR_PAD_LEFT);
				})->implode(' | ')
				.' |'."\n";
		})->implode('');
	}

	// --------------------------------------------------------------------

	public function print_help()
	{
		if (is_cli())
		{
			$titulo = static::CLI_COLOR_F_AMARILLO;
			$opcion = static::CLI_COLOR_F_VERDE;
			$reset  = static::CLI_COLOR_F_NORMAL;

			echo "\n{$titulo}Uso:{$reset}\n"
				."  php public/index.php tests [opciones]\n\n"
				."{$titulo}Opciones:{$reset}\n"
				."  {$opcion}help{$reset}              Muestra esta ayuda.\n"
				."  {$opcion}detalle{$reset}           Muestra cada linea de test.\n"
				."  {$opcion}coverage{$reset}          Muestra el % de cobertura de los tests.\n"
				."  {$opcion}rake [detalle]{$reset}    Muestra estadisticas de la aplicacion.\n";
		}

	}
}
