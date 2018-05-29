<?php

use test\test_case;
use Inventario\Ubicacion;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class ubicacion_test extends TestCase {

	public function test_new()
	{
		$this->assertInternalType('object', Ubicacion::create());
	}

	public function test_has_fields()
	{
		$this->assertNotEmpty(Ubicacion::create()->get_fields());
		$this->assertCount(4, Ubicacion::create()->get_fields());
	}

	// public function test_total_ubicacion_tipo_ubicacion()
	// {
	// 	$ubicacion = Ubicacion::create();
	// 	app('db')->mock_set_return_result([0,1,2,3,4,5]);

	// 	$this->assertEquals($ubicacion->total_ubicacion_tipo_ubicacion(), 6);
	// }

	// public function test_get_ubicacion_tipo_ubicacion()
	// {
	// 	$ubicacion = Ubicacion::create();
	// 	app('db')->mock_set_return_result([0,1,2,3,4,5]);

	// 	$this->assertEquals($ubicacion->get_ubicacion_tipo_ubicacion(), [0,1,2,3,4,5]);
	// }

	// public function test_get_combo_tipos_ubicacion()
	// {
	// 	$ubicacion = Ubicacion::create();
	// 	app('db')->mock_set_return_result([
	// 		['llave'=>'llave1', 'valor'=>'valor1'],
	// 		['llave'=>'llave2', 'valor'=>'valor2'],
	// 		['llave'=>'llave3', 'valor'=>'valor3'],
	// 	]);

	// 	$this->assertEquals($ubicacion->get_combo_tipos_ubicacion(), [
	// 		''=>'Seleccione tipo de ubicacion...',
	// 		'llave1'=>'valor1',
	// 		'llave2'=>'valor2',
	// 		'llave3'=>'valor3',
	// 	]);
	// }

	// public function test_get_ubicaciones_libres()
	// {
	// 	$ubicacion = Ubicacion::create();
	// 	app('db')->mock_set_return_result([
	// 		['llave'=>'llave1', 'valor'=>'valor1'],
	// 		['llave'=>'llave2', 'valor'=>'valor2'],
	// 		['llave'=>'llave3', 'valor'=>'valor3'],
	// 	]);

	// 	$this->assertEquals($ubicacion->get_ubicaciones_libres(), [
	// 		'llave1'=>'valor1',
	// 		'llave2'=>'valor2',
	// 		'llave3'=>'valor3',
	// 	]);
	// }

	public function test_get_validation_add()
	{
		$this->assertCount(3, Ubicacion::create()->get_validation_add());
		$this->assertCount(3, Ubicacion::create()->get_validation_add()[0]);
		$this->assertCount(3, Ubicacion::create()->get_validation_add()[1]);
		$this->assertCount(3, Ubicacion::create()->get_validation_add()[2]);
	}

	public function test_get_validation_edit()
	{
		$ubicacion = Ubicacion::create();

		$data_collection = [
			(object) ['id' => 'id_1'],
			(object) ['id' => 'id_2'],
			(object) ['id' => 'id_3'],
		];

		$this->assertEquals($ubicacion->get_validation_edit($data_collection), [
			['field' => "tipo_inventario[id_1]", 'label' => "Tipo de inventario", 'rules' => 'trim|required'],
			['field' => "tipo_ubicacion[id_1]", 'label' => "Tipo Ubicacion", 'rules' => 'trim|required'],
			['field' => "ubicacion[id_1]", 'label' => "Ubicacion", 'rules' => 'trim|required'],
			['field' => "tipo_inventario[id_2]", 'label' => "Tipo de inventario", 'rules' => 'trim|required'],
			['field' => "tipo_ubicacion[id_2]", 'label' => "Tipo Ubicacion", 'rules' => 'trim|required'],
			['field' => "ubicacion[id_2]", 'label' => "Ubicacion", 'rules' => 'trim|required'],
			['field' => "tipo_inventario[id_3]", 'label' => "Tipo de inventario", 'rules' => 'trim|required'],
			['field' => "tipo_ubicacion[id_3]", 'label' => "Tipo Ubicacion", 'rules' => 'trim|required'],
			['field' => "ubicacion[id_3]", 'label' => "Ubicacion", 'rules' => 'trim|required'],
		]);
	}


}



