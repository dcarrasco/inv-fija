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
class test_controllers_acl extends test_case {

	public function __construct()
	{
		$this->mock = ['db'];
		parent::__construct();
	}

	public function test_acl_config()
	{
		$acl_config = $this->load_controller('acl_config');

		$this->assert_is_string($acl_config->llave_modulo);
		$this->assert_not_empty($acl_config->llave_modulo);

		$this->assert_count($acl_config->menu_opciones, 4);
		$this->assert_not_empty($acl_config->lang_controller);

		app('db')->mock_set_return_result([
			['id'=>'1', 'id_app'=>'app1', 'modulo'=>'modulo1'],
			['id'=>'2', 'id_app'=>'app1', 'modulo'=>'modulo2'],
			['id'=>'3', 'id_app'=>'app1', 'modulo'=>'modulo3'],
		]);

		$this->assert_equals($acl_config->get_select_modulo()->final_output, "<option value=\"1\">modulo1</option>\n<option value=\"2\">modulo2</option>\n<option value=\"3\">modulo3</option>\n");
	}

}
