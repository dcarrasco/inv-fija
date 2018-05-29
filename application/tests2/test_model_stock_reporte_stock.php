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
class test_model_stock_reporte_stock extends test_case {

	public function __construct()
	{
		$this->mock = ['db'];
		parent::__construct();
	}

	public function test_new()
	{
		$this->assert_is_object(Reporte_stock::create());
	}

	public function test_rules_permanencia()
	{
		$this->assert_is_array(Reporte_stock::create()->rules_permanencia);
		$this->assert_count(Reporte_stock::create()->rules_permanencia, 9);
	}

	public function test_rules_movhist()
	{
		$this->assert_is_array(Reporte_stock::create()->rules_movhist);
		$this->assert_count(Reporte_stock::create()->rules_movhist, 9);
	}

	public function test_get_fecha_reporte()
	{
		$reporte = Reporte_stock::create();

		app('db')->mock_set_return_result((object) ['fecha_stock'=>'20171010']);
		$this->assert_equals($reporte->get_fecha_reporte(),'2017-10-10');
	}

	public function test_combo_estado_sap()
	{
		$this->assert_is_array(Reporte_stock::create()->combo_estado_sap());
		$this->assert_count(Reporte_stock::create()->combo_estado_sap(), 5);
	}

	public function test_combo_tipo_alm_consumo()
	{
		$reporte = Reporte_stock::create();

		app('db')->mock_set_return_result([
			['llave' => 'llave1', 'valor' => 'valor1'],
			['llave' => 'llave2', 'valor' => 'valor2'],
			['llave' => 'llave3', 'valor' => 'valor3'],
		]);
		$this->assert_equals($reporte->combo_tipo_alm_consumo(), [
			'llave1' => 'valor1',
			'llave2' => 'valor2',
			'llave3' => 'valor3',
		]);

		app('db')->mock_set_return_result([]);
		$this->assert_equals($reporte->combo_tipo_alm_consumo(), []);
		$this->assert_empty($reporte->combo_tipo_alm_consumo());
	}

	public function test_combo_tipo_mat()
	{
		$this->assert_is_array(Reporte_stock::create()->combo_tipo_mat());
		$this->assert_count(Reporte_stock::create()->combo_tipo_mat(), 4);
	}

	public function test_get_config_reporte_permanencia()
	{
		$this->assert_is_array(Reporte_stock::create()->get_config_reporte_permanencia());
		$this->assert_count(Reporte_stock::create()->get_config_reporte_permanencia(), 3);
		$this->assert_count(Reporte_stock::create()->get_config_reporte_permanencia()['filtros'], 3);
		$this->assert_count(Reporte_stock::create()->get_config_reporte_permanencia()['mostrar'], 3);
	}

	public function test_get_combo_tipo_fecha()
	{
		$this->assert_is_array(Reporte_stock::create()->get_combo_tipo_fecha());
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_fecha(), 4);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_fecha('ANNO'), 3);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_fecha('TRIMESTRE'), 2);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_fecha('MES'), 1);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_fecha('OTRO'), 4);
	}

	public function test_get_combo_fechas()
	{
		$reporte = Reporte_stock::create();

		app('db')->mock_set_return_result([
			['llave' => 'llave1', 'valor' => 'valor1'],
			['llave' => 'llave2', 'valor' => 'valor2'],
			['llave' => 'llave3', 'valor' => 'valor3'],
		]);
		$this->assert_equals($reporte->get_combo_fechas(), [
			'llave1' => 'valor1',
			'llave2' => 'valor2',
			'llave3' => 'valor3',
		]);

		app('db')->mock_set_return_result([]);
		$this->assert_equals($reporte->get_combo_fechas(), []);
		$this->assert_empty($reporte->get_combo_fechas());
	}

	public function test_get_combo_cmv()
	{
		$reporte = Reporte_stock::create();

		app('db')->mock_set_return_result([
			['llave' => 'llave1', 'valor' => 'valor1'],
			['llave' => 'llave2', 'valor' => 'valor2'],
			['llave' => 'llave3', 'valor' => 'valor3'],
		]);
		$this->assert_equals($reporte->get_combo_cmv(), [
			'llave1' => 'valor1',
			'llave2' => 'valor2',
			'llave3' => 'valor3',
		]);

		app('db')->mock_set_return_result([]);
		$this->assert_equals($reporte->get_combo_cmv(), []);
		$this->assert_empty($reporte->get_combo_cmv());
	}

	public function test_get_combo_tipo_alm()
	{
		$this->assert_is_array(Reporte_stock::create()->get_combo_tipo_alm());
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_alm(), 4);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_alm('MOVIL-TIPOALM'), 1);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_alm('FIJA-TIPOALM'), 1);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_alm('OTRO'), 4);
	}

	public function test_get_combo_tipo_mat()
	{
		$this->assert_is_array(Reporte_stock::create()->get_combo_tipo_mat());
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_mat(), 4);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_mat('TIPO'), 3);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_mat('MARCA'), 2);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_mat('MODELO'), 1);
		$this->assert_count(Reporte_stock::create()->get_combo_tipo_mat('OTRO'), 4);
	}

	public function test_get_combo_materiales()
	{
		$reporte = Reporte_stock::create();

		app('db')->mock_set_return_result([
			['llave' => 'llave1', 'valor' => 'valor1'],
			['llave' => 'llave2', 'valor' => 'valor2'],
			['llave' => 'llave3', 'valor' => 'valor3'],
		]);
		$this->assert_equals($reporte->get_combo_materiales(), [
			'llave1' => 'valor1',
			'llave2' => 'valor2',
			'llave3' => 'valor3',
		]);

		app('db')->mock_set_return_result([]);
		$this->assert_equals($reporte->get_combo_materiales(), []);
		$this->assert_empty($reporte->get_combo_materiales());
	}


}

