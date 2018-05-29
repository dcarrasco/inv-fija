<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

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
class test_controller_base extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_load_lang_controller()
	{
		$controller = $this->load_controller('Controller_base');

		$controller->lang = $this->ci->lang;
		$controller->lang_controller = 'adminbd';
		$controller->load_lang_controller();
		$this->assert_true(collect($this->ci->lang->is_loaded)->has('adminbd_lang.php'));
		$this->assert_false(collect($this->ci->lang->is_loaded)->has('acl_lang.php'));

		$controller->lang_controller = ['adminbd', 'acl', 'db'];
		$controller->load_lang_controller();
		$this->assert_true(
			collect($this->ci->lang->is_loaded)->has('adminbd_lang.php')
			&& collect($this->ci->lang->is_loaded)->has('acl_lang.php')
			&& collect($this->ci->lang->is_loaded)->has('db_lang.php')
		);
	}

	public function test_set_menu_modulo()
	{
		$controller = $this->load_controller('Controller_base');

		$controller->menu_opciones = [
			'menu1' => ['url'=>'{{route}}/ruta1', 'texto'=>'lang::ut_passed'],
			'menu2' => ['url'=>'ruta2', 'texto'=>'lang::clave_no_existe2'],
			'menu3' => ['url'=>'{{otra}}/ruta3', 'texto'=>'lang::clave_no_existe3'],
		];

		$menu = $controller->set_menu_modulo();

		$this->assert_equals(array_get($menu, 'menu1.url'), 'tests/ruta1');
		$this->assert_equals(array_get($menu, 'menu1.texto'), 'Passed');

		$this->assert_equals(array_get($menu, 'menu2.url'), 'ruta2');
		$this->assert_equals(array_get($menu, 'menu2.texto'), FALSE);

		$this->assert_equals(array_get($menu, 'menu3.url'), '{{otra}}/ruta3');
		$this->assert_equals(array_get($menu, 'menu3.texto'), FALSE);

		$controller->menu_opciones = [];
		$controller->set_menu_modulo();
		$this->assert_empty($controller->menu_opciones);
	}

	public function test_get_menu_modulo()
	{
		$opciones = [
			'menu1' => ['url'=>'{{route}}/ruta1', 'texto'=>'lang::ut_passed'],
			'menu2' => ['url'=>'ruta2', 'texto'=>'lang::clave_no_existe2'],
			'menu3' => ['url'=>'{{otra}}/ruta3', 'texto'=>'lang::clave_no_existe3'],
		];

		$controller = $this->load_controller('Controller_base');
		$controller->menu_opciones = $opciones;

		$this->assert_equals($controller->get_menu_modulo('aaa'), ['menu' => $opciones, 'mod_selected' => 'aaa']);
	}

}
// End of file test_controller_base.php
// Location: ./tests/test_controller_base.php
