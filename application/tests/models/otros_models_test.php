<?php

use test\test_case;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class otros_models_test extends TestCase {

	public function test_adminbd()
	{
		$adminbd = new Adminbd;

		$this->assertInternalType('object', $adminbd);
		$this->assertEmpty($adminbd->get_fields());
		$this->assertNotEmpty($adminbd->rules_exportar_tablas);
		$this->assertInternalType('array', $adminbd->table_list());
		$this->assertNotEmpty($adminbd->table_list());
	}

	public function test_catalogo_foto()
	{
		$catalogo_foto = new Catalogo_foto;

		$this->assertInternalType('object', $catalogo_foto);
		$this->assertNotEmpty($catalogo_foto->get_fields());
		$this->assertCount(2, $catalogo_foto->get_fields());
	}

	public function test_despachos()
	{
		$despachos = new Despachos;

		$this->assertInternalType('object', $despachos);
		$this->assertEmpty($despachos->get_fields());
	}

}
