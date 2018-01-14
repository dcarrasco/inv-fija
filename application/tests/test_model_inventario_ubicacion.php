<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;
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
class test_model_inventario_ubicacion extends test_case {

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
		$this->assert_is_object(Ubicacion::create());
	}

	public function test_has_fields()
	{
		$this->assert_not_empty(Ubicacion::create()->get_fields());
		$this->assert_count(Ubicacion::create()->get_fields(), 4);
	}

	public function test_total_ubicacion_tipo_ubicacion()
	{
		$ubicacion = Ubicacion::create($this->new_ci_object());
		$ubicacion->db->mock_set_return_result([0,1,2,3,4,5]);

		$this->assert_equals($ubicacion->total_ubicacion_tipo_ubicacion(), 6);
	}

	public function test_get_ubicacion_tipo_ubicacion()
	{
		$ubicacion = Ubicacion::create($this->new_ci_object());
		$ubicacion->db->mock_set_return_result([0,1,2,3,4,5]);

		$this->assert_equals($ubicacion->get_ubicacion_tipo_ubicacion(), [0,1,2,3,4,5]);
	}

	public function test_get_combo_tipos_ubicacion()
	{
		$ubicacion = Ubicacion::create($this->new_ci_object());
		$ubicacion->db->mock_set_return_result([
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
			['llave'=>'llave3', 'valor'=>'valor3'],
		]);

		$this->assert_equals($ubicacion->get_combo_tipos_ubicacion(), [
			''=>'Seleccione tipo de ubicacion...',
			'llave1'=>'valor1',
			'llave2'=>'valor2',
			'llave3'=>'valor3',
		]);
	}

	public function test_get_ubicaciones_libres()
	{
		$ubicacion = Ubicacion::create($this->new_ci_object());
		$ubicacion->db->mock_set_return_result([
			['llave'=>'llave1', 'valor'=>'valor1'],
			['llave'=>'llave2', 'valor'=>'valor2'],
			['llave'=>'llave3', 'valor'=>'valor3'],
		]);

		$this->assert_equals($ubicacion->get_ubicaciones_libres(), [
			'llave1'=>'valor1',
			'llave2'=>'valor2',
			'llave3'=>'valor3',
		]);
	}

	public function test_get_validation_add()
	{
		$this->assert_count(Ubicacion::create()->get_validation_add(), 3);
		$this->assert_count(Ubicacion::create()->get_validation_add()[0], 3);
		$this->assert_count(Ubicacion::create()->get_validation_add()[1], 3);
		$this->assert_count(Ubicacion::create()->get_validation_add()[2], 3);
	}

	public function test_get_validation_edit()
	{
		$ubicacion = Ubicacion::create($this->new_ci_object());

		$data_collection = [
			(object) ['id' => 'id_1'],
			(object) ['id' => 'id_2'],
			(object) ['id' => 'id_3'],
		];

		$this->assert_equals($ubicacion->get_validation_edit($data_collection), [
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



