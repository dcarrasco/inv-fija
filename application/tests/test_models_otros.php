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
class test_models_otros extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_adminbd()
	{
		$adminbd = new Adminbd;

		$this->assert_is_object($adminbd);
		$this->assert_empty($adminbd->get_fields());
		$this->assert_not_empty($adminbd->rules_exportar_tablas);
		$this->assert_is_array($adminbd->table_list());
		$this->assert_not_empty($adminbd->table_list());
	}

	public function test_catalogo_foto()
	{
		$catalogo_foto = new Catalogo_foto;

		$this->assert_is_object($catalogo_foto);
		$this->assert_not_empty($catalogo_foto->get_fields());
		$this->assert_count($catalogo_foto->get_fields(), 2);
	}

	public function test_despachos()
	{
		$despachos = new Despachos;

		$this->assert_is_object($despachos);
		$this->assert_empty($despachos->get_fields());
	}

}
