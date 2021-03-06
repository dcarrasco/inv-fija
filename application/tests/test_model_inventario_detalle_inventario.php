<?php

use test\test_case;
use Inventario\Detalle_inventario;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_inventario_detalle_inventario extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_new()
	{
		$this->assert_is_object(Detalle_inventario::create());
	}

	public function test_has_fields()
	{
		$this->assert_not_empty(Detalle_inventario::create()->get_fields());
		$this->assert_count(Detalle_inventario::create()->get_fields(), 21);
	}

	public function test_string()
	{
		$this->assert_is_string((string) Detalle_inventario::create());
	}

	public function test_rules_ajustes()
	{
		$detalle = Detalle_inventario::create();

		$data_collection = collect([
			(object) ['id' => 'id_1', 'ubicacion' => 'ubicacion_1', 'catalogo' => 'catalogo_1'],
			(object) ['id' => 'id_2', 'ubicacion' => 'ubicacion_2', 'catalogo' => 'catalogo_2'],
			(object) ['id' => 'id_3', 'ubicacion' => 'ubicacion_3', 'catalogo' => 'catalogo_3'],
		]);

		$this->assert_equals($detalle->rules_ajustes($data_collection), [
			['field' => "stock_ajuste_id_1", 'label' => "Cantidad (ubicacion ubicacion_1, material catalogo_1)", 'rules' => 'trim|integer'],
			['field' => "observacion_id_1", 'label' => 'Observacion', 'rules' => 'trim'],
			['field' => "stock_ajuste_id_2", 'label' => "Cantidad (ubicacion ubicacion_2, material catalogo_2)", 'rules' => 'trim|integer'],
			['field' => "observacion_id_2", 'label' => 'Observacion', 'rules' => 'trim'],
			['field' => "stock_ajuste_id_3", 'label' => "Cantidad (ubicacion ubicacion_3, material catalogo_3)", 'rules' => 'trim|integer'],
			['field' => "observacion_id_3", 'label' => 'Observacion', 'rules' => 'trim'],
		]);
	}

	public function test_rules_digitacion()
	{
		$detalle = Detalle_inventario::create();

		$data_collection = collect([
			(object) ['id' => 'id_1', 'ubicacion' => 'ubicacion_1', 'catalogo' => 'catalogo_1'],
			(object) ['id' => 'id_2', 'ubicacion' => 'ubicacion_2', 'catalogo' => 'catalogo_2'],
			(object) ['id' => 'id_3', 'ubicacion' => 'ubicacion_3', 'catalogo' => 'catalogo_3'],
		]);

		$this->assert_equals($detalle->rules_digitacion($data_collection), [
			['field' => "stock_fisico_id_1", 'label' => "Cantidad (ubicacion ubicacion_1, material catalogo_1)", 'rules' => 'trim|required|integer|greater_than[-1]'],
			['field' => "hu_id_1", 'label' => 'HU', 'rules' => 'trim'],
			['field' => "observacion_id_1", 'label' => 'Observacion', 'rules' => 'trim'],
			['field' => "stock_fisico_id_2", 'label' => "Cantidad (ubicacion ubicacion_2, material catalogo_2)", 'rules' => 'trim|required|integer|greater_than[-1]'],
			['field' => "hu_id_2", 'label' => 'HU', 'rules' => 'trim'],
			['field' => "observacion_id_2", 'label' => 'Observacion', 'rules' => 'trim'],
			['field' => "stock_fisico_id_3", 'label' => "Cantidad (ubicacion ubicacion_3, material catalogo_3)", 'rules' => 'trim|required|integer|greater_than[-1]'],
			['field' => "hu_id_3", 'label' => 'HU', 'rules' => 'trim'],
			['field' => "observacion_id_3", 'label' => 'Observacion', 'rules' => 'trim'],
		]);
	}

	public function test_rules_upload()
	{
		$detalle = Detalle_inventario::create();

		$this->assert_is_array($detalle->rules_upload());
		$this->assert_count($detalle->rules_upload(), 2);
		$this->assert_count($detalle->rules_upload()[0], 3);
		$this->assert_count($detalle->rules_upload()[1], 3);
	}


}
