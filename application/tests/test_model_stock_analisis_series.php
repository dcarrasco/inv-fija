<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;
use Stock\Analisis_series;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_stock_analisis_series extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	protected function new_ci_object()
	{
		return [
			'session' => new mock_session,
			'db'      => new mock_db2,
			'input'   => new mock_input,
		];
	}

	public function test_new()
	{
		$this->assert_is_object(Analisis_series::create());
	}

	public function test_has_fields()
	{
		$this->assert_empty(Analisis_series::create()->get_fields());
	}

	public function test_get_historia()
	{
		$analisis_series = Analisis_series::create($this->new_ci_object());

		$analisis_series->db->mock_set_return_result([
			['serie'=>'serie1', 'valor'=>'valor1'],
			['serie'=>'serie2', 'valor'=>'valor2'],
			['serie'=>'serie3', 'valor'=>'valor3'],
		]);

		$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

		$this->assert_is_string($analisis_series->get_historia($series));

		$this->assert_count(
			collect(explode("\n", $analisis_series->get_historia($series)))
				->filter(function($linea) {
					return strpos($linea, 'table class') !== FALSE;
				})
				->all(), 3);
	}

	public function test_get_despacho()
	{
		$analisis_series = Analisis_series::create($this->new_ci_object());

		$analisis_series->db->mock_set_return_result([
			['serie'=>'serie1', 'valor'=>'valor1'],
			['serie'=>'serie2', 'valor'=>'valor2'],
			['serie'=>'serie3', 'valor'=>'valor3'],
		]);

		$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

		$this->assert_is_string($analisis_series->get_despacho($series));

		$this->assert_count(
			collect(explode("\n", $analisis_series->get_despacho($series)))
				->filter(function($linea) {
					return strpos($linea, 'table class') !== FALSE;
				})
				->all(), 1);
	}

	public function test_get_stock_sap()
	{
		$analisis_series = Analisis_series::create($this->new_ci_object());

		$analisis_series->db->mock_set_return_result([
			['serie'=>'serie1', 'valor'=>'valor1'],
			['serie'=>'serie2', 'valor'=>'valor2'],
			['serie'=>'serie3', 'valor'=>'valor3'],
		]);

		$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

		$this->assert_is_string($analisis_series->get_stock_sap($series));

		$this->assert_count(
			collect(explode("\n", $analisis_series->get_stock_sap($series)))
				->filter(function($linea) {
					return strpos($linea, 'table class') !== FALSE;
				})
				->all(), 1);
	}

	public function test_get_stock_scl()
	{
		$analisis_series = Analisis_series::create($this->new_ci_object());

		$analisis_series->db->mock_set_return_result([
			['serie'=>'serie1', 'valor'=>'valor1'],
			['serie'=>'serie2', 'valor'=>'valor2'],
			['serie'=>'serie3', 'valor'=>'valor3'],
		]);

		$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

		$this->assert_is_string($analisis_series->get_stock_scl($series));

		$this->assert_count(
			collect(explode("\n", $analisis_series->get_stock_scl($series)))
				->filter(function($linea) {
					return strpos($linea, 'table class') !== FALSE;
				})
				->all(), 1);
	}

	public function test_get_meses_trafico()
	{
		$analisis_series = Analisis_series::create(['db'=> new mock_db]);

		$analisis_series->db->mock_set_return_result([
			['mes' => '201712'],
			['mes' => '201711'],
			['mes' => '201710'],
		]);

		$this->assert_equals($analisis_series->get_meses_trafico(), [
			'201712' => '2017-12',
			'201711' => '2017-11',
			'201710' => '2017-10',
		]);

	}

}

class mock_db2 extends mock_db
{
	public function get()
	{
		return $this->return_result;
	}
}
