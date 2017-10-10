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
		'Test Name'         => 30,
		'Test Datatype'     => 13,
		'Expected Datatype' => 17,
		'Result'            => 2,
		'File Name'         => 15,
		'Line Number'       => 11,
	];

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

		$this->unit->use_strict(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los resultados de los tests
	 *
	 * @return mixed
	 */
	protected function print_results()
	{
		return is_cli() ? $this->print_cli_result() : $this->print_html_result();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los resultados en formato HTML
	 *
	 * @return view
	 */
	protected function print_html_result()
	{
		$test_report = $this->results()
			->map(function($result) {
				return '<tr><td>'.collect($result)->implode('</td><td>').'</td></tr>';
			})
			->implode();

		return $this->ci->parser->parse('tests/tests', [
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
	protected function print_cli_result()
	{
		$results_collection = $this->results();

		$padding        = $this->cli_padding($results_collection);
		$separator_line = $this->cli_separator_line($padding);
		$title_line     = $this->cli_title_line($padding);

		$results = $results_collection->map(function($result) use ($padding) {
			return ' | '.collect($result)
				->only(['n', 'Test Name', 'Test Datatype', 'Expected Datatype', 'Result', 'File Name', 'Line Number'])
				->map(function($result, $result_key) use ($padding) {
					$padding['Result'] += 9;
					return str_pad($result, array_get($padding, $result_key, 10));
				})
				->implode(' | ').' | ';
		})
		->implode("\n");

		echo "\n{$separator_line}{$title_line}{$separator_line}{$results}\n{$separator_line}";
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
				$max_length = max($results_collection->pluck($item_name)
					->map(function($test) {return strlen($test);})
					->all()
				);

				return $max_length > $item_length ? $max_length : $item_length;
			})->all();
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
		return ' +-'.collect($padding)
			->map(function($item_length) {return str_pad('', $item_length, '-'); })
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
		return ' | '.collect($padding)
			->map(function($item_length, $item_key) {return str_pad($item_key, $item_length); })
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
				preg_match('/File: \[([a-zA-Z\d_\.]*)\], Line: \[(\d*)\]/', $result['Notes'], $matches);

				if (count($matches))
				{
					$result['File Name']   = array_get($matches, 1);
					$result['Line Number'] = array_get($matches, 2);
					$result['Notes']       = '';
				}

				return $result;
			})
			// numera las lineas
			->map(function($result) use (&$cant) {
				return [
					'n'                 => ++$cant,
					'Test Name'         => $result['Test Name'],
					'Test Datatype'     => $result['Test Datatype'],
					'Expected Datatype' => $result['Expected Datatype'],
					'Result'            => $result['Result'],
					'File Name'         => $result['File Name'],
					'Line Number'       => $result['Line Number'],
					'Notes'             => $result['Notes'],
				];
			})
			// formatea campo Result para HTML o CLI
			->map(function($result) use ($is_cli) {
				$result['Result'] = $this->format_result($result['Result'], $is_cli);

				return $result;
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
			$color = $value === lang('ut_passed') ? "\033[32m" : "\033[31m";

			return "{$color}{$value}\033[0m";
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
	public function all_files()
	{
		collect(scandir(APPPATH.'/tests'))
			->filter(function($folder) { return substr($folder, -4) === '.php'; })
			->map(function($folder)    { return substr($folder, 0, strlen($folder) - 4); })
			->each(function($folder)   { (new $folder())->all_methods(); });

		$this->print_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta un test
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @param  mixed $result Resultado esperado
	 * @return mixed
	 */
	public function test($test, $result)
	{
		$file = explode('/', array_get(collect(debug_backtrace())->get(0), 'file'));
		$file = array_get($file, count($file)-1);
		$line = array_get(collect(debug_backtrace())->get(0), 'line');

		$test_name = array_get(collect(debug_backtrace())->get(1), 'function');
		$test_name = str_replace('_', ' ', substr($test_name, 5, strlen($test_name)));

		return $this->unit->run($test, $result, $test_name, "File: [{$file}], Line: [{$line}]");
	}

}
