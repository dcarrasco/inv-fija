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
trait has_cli_output {

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
				$result['Result'] = $this->format_cli_result($result['Result']);
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

		echo $this->print_cli_header();

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
	 * Formatea el resultado del test (passed/failed)
	 *
	 * @param  string  $value  Valor del resultado del test
	 * @return string
	 */
	protected function format_cli_result($value = '')
	{
		$color = $value === lang('ut_passed') ? static::CLI_COLOR_F_VERDE : static::CLI_COLOR_F_ROJO;

		return "{$color}{$value}".static::CLI_COLOR_F_NORMAL;
	}

	// --------------------------------------------------------------------

	protected function print_cli_header()
	{
		$texto_intervalo = $this->process_duration->s > 0
			? ($this->process_duration->s + (property_exists($this->process_duration, 'f') ? $this->process_duration->f : 0)).' segundos'
			: (property_exists($this->process_duration, 'f') ? $this->process_duration->f*1000 : 0).' ms';

		return "Codeigniter Unit Testing 1.0.0 by DC\n\n"
				."Tiempo: {$texto_intervalo}, Memoria: ".byte_format(memory_get_usage())."\n";
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

		$tests = $results_passed
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

		echo $this->print_cli_header();

		echo "\n".$this->print_failures_details($results_collection)."\n";

		echo ($failed)
			? "{$color_failed} FAILURES! \n Tests: {$tests}, Assertions: {$cant}, Failures: {$failed}. {$color_reset}\n"
			: "{$color_passed} OK ({$tests} tests, {$passed} assertions). {$color_reset}\n";
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
						.array_get($result, 'Notes.error_message')."\n"
						.array_get($result, 'Notes.full_file').':'.array_get($result, 'Notes.line')."\n";
				})
				->implode("\n");
	}

	// --------------------------------------------------------------------

	public function print_cli_help()
	{
		if (is_cli())
		{
			$titulo = static::CLI_COLOR_F_AMARILLO;
			$opcion = static::CLI_COLOR_F_VERDE;
			$reset  = static::CLI_COLOR_F_NORMAL;

			echo "\n{$titulo}Uso:{$reset}\n"
				."  php public/index.php tests [opciones]\n\n"
				."{$titulo}Opciones:{$reset}\n"
				."  {$opcion}help{$reset}                     Muestra esta ayuda.\n"
				."  {$opcion}detalle{$reset}                  Muestra cada linea de test.\n"
				."  {$opcion}file [file_name]{$reset}           Muestra cada linea de test de un archivo.\n"
				."  {$opcion}coverage{$reset}                 Muestra el % de cobertura de los tests.\n"
				."  {$opcion}rake [detalle] [sort]{$reset}    Muestra estadisticas de la aplicacion.\n";
		}

	}



}
