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
		parent::__construct();
	}

	public function test_acl_config()
	{
		$acl_config = $this->load_controller('inventario_digitacion');

		// $this->assert_not_empty($acl_config->ajax_act_agr_materiales('hoja')->final_output);

		$this->ci->output->set_output(NULL);
	}

}
