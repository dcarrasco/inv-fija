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
class test_models_acl extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_app()
	{
		$app = new Acl\app;

		$this->assert_is_object($app);
		$this->assert_not_empty($app->get_fields());
		$this->assert_count($app->get_fields(), 6);
		$this->assert_is_string((string) $app);
	}

	public function test_modulo()
	{
		$modulo = new Acl\Modulo;

		$this->assert_is_object($modulo);
		$this->assert_not_empty($modulo->get_fields());
		$this->assert_count($modulo->get_fields(), 8);
		$this->assert_is_string((string) $modulo);
	}

	public function test_rol()
	{
		$rol = new Acl\rol;

		$this->assert_is_object($rol);
		$this->assert_not_empty($rol->get_fields());
		$this->assert_count($rol->get_fields(), 5);
		$this->assert_is_string((string) $rol);
	}

	public function test_usuario()
	{
		$usuario = new Acl\Usuario;

		$this->assert_is_object($usuario);
		$this->assert_not_empty($usuario->get_fields());
		$this->assert_count($usuario->get_fields(), 11);
		$this->assert_is_string((string) $usuario);
	}

}
