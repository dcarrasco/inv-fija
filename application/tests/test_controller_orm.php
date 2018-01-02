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
class test_controller_orm extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_index()
	{
		$controller = $this->load_controller('Orm_controller');
		$controller->model_namespace = "\\Acl\\";
	}

}
// End of file test_controller_orm.php
// Location: ./tests/test_controller_orm.php
