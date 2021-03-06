<?php

use test\test_case;
use Toa\Consumo_toa;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_toa_consumo_toa extends test_case {

	public function __construct()
	{
		$this->mock = ['db'];

		parent::__construct();
	}

	public function test_new()
	{
		$this->assert_is_object(Consumo_toa::create());
	}

	public function test_has_fields()
	{
		$this->assert_empty(Consumo_toa::create()->get_fields());
	}

	public function test_movimientos_consumo()
	{
		$this->assert_is_array(Consumo_toa::create()->movimientos_consumo);
		$this->assert_not_empty(Consumo_toa::create()->movimientos_consumo);
	}

	public function test_centros_consumo()
	{
		$this->assert_is_array(Consumo_toa::create()->centros_consumo);
		$this->assert_not_empty(Consumo_toa::create()->centros_consumo);
	}

	public function test_tipos_reporte_consumo()
	{
		$this->assert_is_array(Consumo_toa::create()->tipos_reporte_consumo);
		$this->assert_not_empty(Consumo_toa::create()->tipos_reporte_consumo);
	}

	public function test_combo_unidades_consumo()
	{
		$this->assert_is_array(Consumo_toa::create()->combo_unidades_consumo);
		$this->assert_not_empty(Consumo_toa::create()->combo_unidades_consumo);
	}

	public function test_combo_unidades_materiales_tipo_trabajo()
	{
		$this->assert_is_array(Consumo_toa::create()->combo_unidades_materiales_tipo_trabajo);
		$this->assert_not_empty(Consumo_toa::create()->combo_unidades_materiales_tipo_trabajo);
	}

	public function test_consumos_validation()
	{
		$this->assert_is_array(Consumo_toa::create()->consumos_validation);
		$this->assert_not_empty(Consumo_toa::create()->consumos_validation);
	}

	public function test_rules_controles_clientes()
	{
		$this->assert_is_array(Consumo_toa::create()->rules_controles_clientes);
		$this->assert_not_empty(Consumo_toa::create()->rules_controles_clientes);
	}

	public function test_rules_controles_consumos()
	{
		$this->assert_is_array(Consumo_toa::create()->rules_controles_consumos);
		$this->assert_not_empty(Consumo_toa::create()->rules_controles_consumos);
	}

	public function test_rules_controles_materiales()
	{
		$this->assert_is_array(Consumo_toa::create()->rules_controles_materiales);
		$this->assert_not_empty(Consumo_toa::create()->rules_controles_materiales);
	}

	public function test_combo_movimientos_consumo()
	{
		$consumo_toa = Consumo_toa::create();

		app('db')->mock_set_return_result([
			['cmv'=>'cmv1', 'des_cmv'=>'des_cmv1'],
			['cmv'=>'cmv2', 'des_cmv'=>'des_cmv2'],
			['cmv'=>'cmv3', 'des_cmv'=>'des_cmv3'],
		]);
		$this->assert_equals($consumo_toa->combo_movimientos_consumo(), [
			'000'=>'Todos los movimientos',
			'cmv1'=>'cmv1 des_cmv1',
			'cmv2'=>'cmv2 des_cmv2',
			'cmv3'=>'cmv3 des_cmv3',
		]);

	}


}



