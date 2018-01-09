<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;
use Inventario\Inventario;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_inventario_inventario extends test_case {

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
		$this->assert_is_object(Inventario::create());
	}

	public function test_has_fields()
	{
		$this->assert_not_empty(Inventario::create()->get_fields());
		$this->assert_count(Inventario::create()->get_fields(), 4);
	}

	public function test_string()
	{
		$this->assert_is_string((string) Inventario::create());
	}

	public function test_get_id_inventario_activo()
	{
		$inventario = Inventario::create($this->new_ci_object());

		$inventario->db->mock_set_return_result(['id'=>111]);
		$this->assert_equals($inventario->get_id_inventario_activo(), 111);
	}

	public function test_get_inventario_activo()
	{
		$inventario = Inventario::create($this->new_ci_object());

		$inventario->db->mock_set_return_result(['id'=>111, 'nombre' => 'prueba de inventario']);
		$this->assert_equals($inventario->get_inventario_activo()->__toString(), 'prueba de inventario');
	}

	public function test_get_max_hoja_inventario()
	{
		$inventario = Inventario::create($this->new_ci_object());

		$inventario->db->mock_set_return_result(['max_hoja'=>100]);
		$this->assert_equals($inventario->get_max_hoja_inventario(), 100);
	}

	public function test_get_combo_inventarios()
	{
		$inventario = Inventario::create($this->new_ci_object());

		$inventario->db->mock_set_return_result([
			['desc_tipo_inventario'=>'tipo1', 'id'=>1, 'nombre'=>'inventario1'],
			['desc_tipo_inventario'=>'tipo1', 'id'=>2, 'nombre'=>'inventario2'],
			['desc_tipo_inventario'=>'tipo2', 'id'=>3, 'nombre'=>'inventario3'],
		]);

		$this->assert_equals($inventario->get_combo_inventarios(), [
			'tipo1' => [1 => 'inventario1', 2 => 'inventario2'],
			'tipo2' => [3 => 'inventario3'],
		]);
	}


}



