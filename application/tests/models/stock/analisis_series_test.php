<?php

use test\test_case;
use test\mock\mock_db;
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
class analisis_series_test extends TestCase {

	public function test_new()
	{
		$this->assertInternalType('object', Analisis_series::create());
	}

	public function test_has_fields()
	{
		$this->assertEmpty(Analisis_series::create()->get_fields());
	}

	// public function test_series_list_to_array()
	// {
	// 	$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

	// 	$this->assertEquals($this->get_method('_series_list_to_array', $series), [
	// 		'12345678901234',
	// 		'12345678901235',
	// 		'890123456789012',
	// 	]);
	// }

	// public function test_format_serie()
	// {
	// 	$this->assert_equals($this->get_method('format_serie', '012345678901234', 'SAP'), '12345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '12345678901234', 'SAP'), '12345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '312345678901234', 'SAP'), '312345678901234');

	// 	$this->assert_equals($this->get_method('format_serie', '012345678901234', 'trafico'), '012345678901230');
	// 	$this->assert_equals($this->get_method('format_serie', '12345678901234', 'trafico'), '012345678901230');
	// 	$this->assert_equals($this->get_method('format_serie', '312345678901234', 'trafico'), '312345678901230');

	// 	$this->assert_equals($this->get_method('format_serie', '012345678901234', 'SCL'), '012345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '12345678901234', 'SCL'), '012345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '312345678901234', 'SCL'), '312345678901234');

	// 	$this->assert_equals($this->get_method('format_serie', '123456789', 'celular'), '23456789');
	// 	$this->assert_equals($this->get_method('format_serie', '12345678', 'celular'), '12345678');

	// 	$this->assert_equals($this->get_method('format_serie', '012345678901234', 'otro'), '012345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '12345678901234', 'otro'), '12345678901234');
	// 	$this->assert_equals($this->get_method('format_serie', '312345678901234', 'otros'), '312345678901234');
	// }

	// public function test_get_dv_imei()
	// {
	// 	$this->assert_equals($this->get_method('_get_dv_imei', '012345678901234'), '012345678901237');
	// 	$this->assert_equals($this->get_method('_get_dv_imei', '12345678901234'), '012345678901237');
	// 	$this->assert_equals($this->get_method('_get_dv_imei', '223456789012345'), '223456789012346');
	// }

	// public function test_get_historia()
	// {
	// 	$analisis_series = Analisis_series::create();

	// 	$mock_db = new mock_db2;
	// 	App::mock('db', $mock_db);
	// 	$mock_db->mock_set_return_result([
	// 		['serie'=>'serie1', 'valor'=>'valor1'],
	// 		['serie'=>'serie2', 'valor'=>'valor2'],
	// 		['serie'=>'serie3', 'valor'=>'valor3'],
	// 	]);

	// 	$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

	// 	$this->assert_is_string($analisis_series->get_historia($series));

	// 	$this->assert_count(
	// 		collect(explode("\n", $analisis_series->get_historia($series)))
	// 			->filter(function($linea) {
	// 				return strpos($linea, 'table class') !== FALSE;
	// 			})
	// 			->all(), 3);
	// 	App::mock('db', new mock_db);
	// }

	// public function test_get_despacho()
	// {
	// 	$analisis_series = Analisis_series::create();

	// 	$mock_db = new mock_db2;
	// 	App::mock('db', $mock_db);
	// 	$mock_db->mock_set_return_result([
	// 		['serie'=>'serie1', 'valor'=>'valor1'],
	// 		['serie'=>'serie2', 'valor'=>'valor2'],
	// 		['serie'=>'serie3', 'valor'=>'valor3'],
	// 	]);

	// 	$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

	// 	$this->assert_is_string($analisis_series->get_despacho($series));

	// 	$this->assert_count(
	// 		collect(explode("\n", $analisis_series->get_despacho($series)))
	// 			->filter(function($linea) {
	// 				return strpos($linea, 'table class') !== FALSE;
	// 			})
	// 			->all(), 1);
	// 	App::mock('db', new mock_db);
	// }

	// public function test_get_stock_sap()
	// {
	// 	$analisis_series = Analisis_series::create();

	// 	$mock_db = new mock_db2;
	// 	App::mock('db', $mock_db);
	// 	$mock_db->mock_set_return_result([
	// 		['serie'=>'serie1', 'valor'=>'valor1'],
	// 		['serie'=>'serie2', 'valor'=>'valor2'],
	// 		['serie'=>'serie3', 'valor'=>'valor3'],
	// 	]);

	// 	$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

	// 	$this->assert_is_string($analisis_series->get_stock_sap($series));

	// 	$this->assert_count(
	// 		collect(explode("\n", $analisis_series->get_stock_sap($series)))
	// 			->filter(function($linea) {
	// 				return strpos($linea, 'table class') !== FALSE;
	// 			})
	// 			->all(), 1);

	// 	App::mock('db', new mock_db);
	// }

	// public function test_get_stock_scl()
	// {
	// 	$analisis_series = Analisis_series::create();

	// 	$analisis_series->db->mock_set_return_result([
	// 		['serie'=>'serie1', 'valor'=>'valor1'],
	// 		['serie'=>'serie2', 'valor'=>'valor2'],
	// 		['serie'=>'serie3', 'valor'=>'valor3'],
	// 	]);

	// 	$series = "012345678901234\r\n012345678901235\r\n890123456789012\r\n";

	// 	$this->assert_is_string($analisis_series->get_stock_scl($series));

	// 	$this->assert_count(
	// 		collect(explode("\n", $analisis_series->get_stock_scl($series)))
	// 			->filter(function($linea) {
	// 				return strpos($linea, 'table class') !== FALSE;
	// 			})
	// 			->all(), 1);
	// }

	// public function test_get_meses_trafico()
	// {
	// 	$analisis_series = Analisis_series::create();

	// 	app('db')->mock_set_return_result([
	// 		['mes' => '201712'],
	// 		['mes' => '201711'],
	// 		['mes' => '201710'],
	// 	]);

	// 	$this->assert_equals($analisis_series->get_meses_trafico(), [
	// 		'201712' => '2017-12',
	// 		'201711' => '2017-11',
	// 		'201710' => '2017-10',
	// 	]);

	// }

}

// class mock_db2 extends mock_db
// {
// 	public function get()
// 	{
// 		return $this->return_data;
// 	}
// }
