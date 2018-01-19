<?php

use test\test_case;
use Stock\Almacen_sap;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_stock_almacen_sap extends test_case {

	public function __construct()
	{
		$this->mock = ['db'];

		parent::__construct();
	}

	public function test_new()
	{
		$this->assert_is_object(Almacen_sap::create());
	}

	public function test_has_fields()
	{
		$this->assert_not_empty(Almacen_sap::create()->get_fields());
		$this->assert_count(Almacen_sap::create()->get_fields(), 7);
	}

	public function test_to_string()
	{
		$almacen_sap = Almacen_sap::create();
		app('db')->mock_set_return_result([
			'centro' => 'centro',
			'cod_almacen' => 'cod_almacen',
			'des_almacen' => 'des_almacen',
		]);

		// $this->assert_equals((string) $almacen_sap->find('first'), 'centro-cod_almacen des_almacen');
	}

	public function test_get_combo_almacenes_db()
	{
		$almacen_sap = Almacen_sap::create();

		app('db')->mock_set_return_result([
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
			['llave'=>'llave3', 'valor'=>'valor3'],
		]);
		$this->assert_equals($almacen_sap->get_combo_almacenes_db('movil', 'filtro'), [
			'llave1'=>'valor1',
			'llave2'=>'valor2',
			'llave3'=>'valor3',
		]);

		app('db')->mock_set_return_result([
			['centro'=>'centro1', 'cod_almacen'=>'cod_alm1', 'des_almacen'=>'des_alm1'],
			['centro'=>'centro2', 'cod_almacen'=>'cod_alm2', 'des_almacen'=>'des_alm2'],
			['centro'=>'centro3', 'cod_almacen'=>'cod_alm3', 'des_almacen'=>'des_alm3'],
		]);
		$this->assert_equals($almacen_sap->get_combo_almacenes_db('movil'), [
			'centro1~cod_alm1'=>'centro1-cod_alm1 des_alm1',
			'centro2~cod_alm2'=>'centro2-cod_alm2 des_alm2',
			'centro3~cod_alm3'=>'centro3-cod_alm3 des_alm3',
		]);
	}

	public function test_almacenes_no_ingresados()
	{
		$almacen_sap = Almacen_sap::create();
		app('db')->mock_set_return_result([
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
		]);

		$this->assert_equals($almacen_sap->almacenes_no_ingresados(), [
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
		]);
	}


}
