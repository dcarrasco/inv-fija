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
trait has_html_output {

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
				return [
					'n' => array_get($result, 'n'),
					'File Name' => array_get($result, 'File Name'),
					'Line Number' => array_get($result, 'Line Number'),
					'Test Name' => array_get($result, 'Test Name'),
					'Test Datatype' => array_get($result, 'Test Datatype'),
					'Expected Datatype' => array_get($result, 'Expected Datatype'),
					'Result' => $this->format_html_result(array_get($result, 'Result')),
					'Notes' => array_get($result, 'Result') === lang('ut_passed')
						? '' : str_replace("\n", '<br>', array_get($result, 'Notes.error_message')),
				];
			})
			->map(function($result) {
				$class = strpos(array_get($result, 'Result'), lang('ut_passed')) === FALSE
					? ' class="danger"'
					: '';

				return "<tr{$class}><td>".collect($result)->implode('</td><td>').'</td></tr>';
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
	 * Formatea el resultado del test (passed/failed)
	 *
	 * @param  string  $value  Valor del resultado del test
	 * @return string
	 */
	protected function format_html_result($value = '')
	{
		$label_class = $value === lang('ut_passed') ? 'success' : 'danger';

		return "<span class=\"label label-{$label_class}\">{$value}</span>";
	}


}
