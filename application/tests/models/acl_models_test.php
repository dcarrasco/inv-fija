<?php

use test\test_case;
use test\mock_object;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class acl_models_test extends TestCase {

	public function test_app()
	{
		$app = new Acl\app;

		$this->assertInternalType('object', $app);
		$this->assertNotEmpty($app->get_fields());
		$this->assertCount(6, $app->get_fields());
		$this->assertInternalType('string', (string) $app);
	}

	public function test_modulo()
	{
		$modulo = new Acl\Modulo;

		$this->assertInternalType('object', $modulo);
		$this->assertNotEmpty($modulo->get_fields());
		$this->assertCount(8, $modulo->get_fields());
		$this->assertInternalType('string', (string) $modulo);
	}

	public function test_rol()
	{
		$rol = new Acl\rol;

		$this->assertInternalType('object', $rol);
		$this->assertNotEmpty($rol->get_fields());
		$this->assertCount(5, $rol->get_fields());
		$this->assertInternalType('string', (string) $rol);
	}

	public function test_usuario()
	{
		$usuario = new Acl\Usuario;

		$this->assertInternalType('object', $usuario);
		$this->assertNotEmpty($usuario->get_fields());
		$this->assertCount(11, $usuario->get_fields());
		$this->assertInternalType('string', (string) $usuario);
	}

}
