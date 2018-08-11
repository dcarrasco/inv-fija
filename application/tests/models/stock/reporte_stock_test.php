<?php

use test\test_case;
use Stock\Reporte_stock;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class reporte_stock_test extends TestCase {

	public function test_new()
	{
		$this->assertInternalType('object', Reporte_stock::create());
	}

	public function test_rules_permanencia()
	{
		$this->assertInternalType('array', Reporte_stock::create()->rules_permanencia);
		$this->assertCount(9, Reporte_stock::create()->rules_permanencia);
	}

	public function test_rules_movhist()
	{
		$this->assertInternalType('array', Reporte_stock::create()->rules_movhist);
		$this->assertCount(9, Reporte_stock::create()->rules_movhist);
	}

	// public function test_get_fecha_reporte()
	// {
	// 	$reporte = Reporte_stock::create();

	// 	app('db')->mock_set_return_result((object) ['fecha_stock'=>'20171010']);
	// 	$this->assert_equals($reporte->get_fecha_reporte(),'2017-10-10');
	// }

	public function test_combo_estado_sap()
	{
		$this->assertInternalType('array', Reporte_stock::create()->combo_estado_sap());
		$this->assertCount(5, Reporte_stock::create()->combo_estado_sap());
	}

	// public function test_combo_tipo_alm_consumo()
	// {
	// 	$reporte = Reporte_stock::create();

	// 	app('db')->mock_set_return_result([
	// 		['llave' => 'llave1', 'valor' => 'valor1'],
	// 		['llave' => 'llave2', 'valor' => 'valor2'],
	// 		['llave' => 'llave3', 'valor' => 'valor3'],
	// 	]);
	// 	$this->assert_equals($reporte->combo_tipo_alm_consumo(), [
	// 		'llave1' => 'valor1',
	// 		'llave2' => 'valor2',
	// 		'llave3' => 'valor3',
	// 	]);

	// 	app('db')->mock_set_return_result([]);
	// 	$this->assert_equals($reporte->combo_tipo_alm_consumo(), []);
	// 	$this->assert_empty($reporte->combo_tipo_alm_consumo());
	// }

	public function test_combo_tipo_mat()
	{
		$this->assertInternalType('array', Reporte_stock::create()->combo_tipo_mat());
		$this->assertCount(4, Reporte_stock::create()->combo_tipo_mat());
	}

	public function test_get_config_reporte_permanencia()
	{
		$this->assertInternalType('array', Reporte_stock::create()->get_config_reporte_permanencia());
		$this->assertCount(3, Reporte_stock::create()->get_config_reporte_permanencia());
		$this->assertCount(3, Reporte_stock::create()->get_config_reporte_permanencia()['filtros']);
		$this->assertCount(3, Reporte_stock::create()->get_config_reporte_permanencia()['mostrar']);
	}

	public function test_get_combo_tipo_fecha()
	{
		$this->assertInternalType('array', Reporte_stock::create()->get_combo_tipo_fecha());
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_fecha());
		$this->assertCount(3, Reporte_stock::create()->get_combo_tipo_fecha('ANNO'));
		$this->assertCount(2, Reporte_stock::create()->get_combo_tipo_fecha('TRIMESTRE'));
		$this->assertCount(1, Reporte_stock::create()->get_combo_tipo_fecha('MES'));
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_fecha('OTRO'));
	}

	// public function test_get_combo_fechas()
	// {
	// 	$reporte = Reporte_stock::create();

	// 	app('db')->mock_set_return_result([
	// 		['llave' => 'llave1', 'valor' => 'valor1'],
	// 		['llave' => 'llave2', 'valor' => 'valor2'],
	// 		['llave' => 'llave3', 'valor' => 'valor3'],
	// 	]);
	// 	$this->assert_equals($reporte->get_combo_fechas(), [
	// 		'llave1' => 'valor1',
	// 		'llave2' => 'valor2',
	// 		'llave3' => 'valor3',
	// 	]);

	// 	app('db')->mock_set_return_result([]);
	// 	$this->assert_equals($reporte->get_combo_fechas(), []);
	// 	$this->assert_empty($reporte->get_combo_fechas());
	// }

	// public function test_get_combo_cmv()
	// {
	// 	$reporte = Reporte_stock::create();

	// 	app('db')->mock_set_return_result([
	// 		['llave' => 'llave1', 'valor' => 'valor1'],
	// 		['llave' => 'llave2', 'valor' => 'valor2'],
	// 		['llave' => 'llave3', 'valor' => 'valor3'],
	// 	]);
	// 	$this->assert_equals($reporte->get_combo_cmv(), [
	// 		'llave1' => 'valor1',
	// 		'llave2' => 'valor2',
	// 		'llave3' => 'valor3',
	// 	]);

	// 	app('db')->mock_set_return_result([]);
	// 	$this->assert_equals($reporte->get_combo_cmv(), []);
	// 	$this->assert_empty($reporte->get_combo_cmv());
	// }

	public function test_get_combo_tipo_alm()
	{
		$this->assertInternalType('array', Reporte_stock::create()->get_combo_tipo_alm());
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_alm());
		$this->assertCount(1, Reporte_stock::create()->get_combo_tipo_alm('MOVIL-TIPOALM'));
		$this->assertCount(1, Reporte_stock::create()->get_combo_tipo_alm('FIJA-TIPOALM'));
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_alm('OTRO'));
	}

	public function test_get_combo_tipo_mat()
	{
		$this->assertInternalType('array', Reporte_stock::create()->get_combo_tipo_mat());
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_mat());
		$this->assertCount(3, Reporte_stock::create()->get_combo_tipo_mat('TIPO'));
		$this->assertCount(2, Reporte_stock::create()->get_combo_tipo_mat('MARCA'));
		$this->assertCount(1, Reporte_stock::create()->get_combo_tipo_mat('MODELO'));
		$this->assertCount(4, Reporte_stock::create()->get_combo_tipo_mat('OTRO'));
	}

	// public function test_get_combo_materiales()
	// {
	// 	$reporte = Reporte_stock::create();

	// 	app('db')->mock_set_return_result([
	// 		['llave' => 'llave1', 'valor' => 'valor1'],
	// 		['llave' => 'llave2', 'valor' => 'valor2'],
	// 		['llave' => 'llave3', 'valor' => 'valor3'],
	// 	]);
	// 	$this->assert_equals($reporte->get_combo_materiales(), [
	// 		'llave1' => 'valor1',
	// 		'llave2' => 'valor2',
	// 		'llave3' => 'valor3',
	// 	]);

	// 	app('db')->mock_set_return_result([]);
	// 	$this->assert_equals($reporte->get_combo_materiales(), []);
	// 	$this->assert_empty($reporte->get_combo_materiales());
	// }


}

