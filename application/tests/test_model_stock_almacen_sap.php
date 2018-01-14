<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;
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
		parent::__construct();
	}

	protected function new_ci_object()
	{
		return [
			'session' => new mock_session,
			'db'      => new mock_db,
			'input'   => new mock_input,
		];
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
		$this->assert_is_string((string) Almacen_sap::create());
	}

	public function test_get_combo_almacenes_db()
	{
		$almacen_sap = Almacen_sap::create($this->new_ci_object());

		$almacen_sap->db->mock_set_return_result([
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
			['llave'=>'llave3', 'valor'=>'valor3'],
		]);
		$this->assert_equals($almacen_sap->get_combo_almacenes_db('movil', 'filtro'), [
			'llave1'=>'valor1',
			'llave2'=>'valor2',
			'llave3'=>'valor3',
		]);

		$almacen_sap->db->mock_set_return_result([
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
		$almacen_sap = Almacen_sap::create($this->new_ci_object());
		$almacen_sap->db->mock_set_return_result([
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
