<?php

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_orm_field extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	protected function get_field($config = [])
	{

		return new Orm_field('prueba', array_merge([
			'label'          => 'label_prueba',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'texto_ayuda_prueba',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		], $config));
	}

	public function test_orm_field_get_tipo()
	{
		$expected = Orm_field::TIPO_CHAR;
		$field = $this->get_field();

		$this->test($field->get_tipo(), $expected);
	}

	public function test_orm_field_get_label()
	{
		$expected = 'label_prueba';
		$field = $this->get_field();

		$this->test($field->get_label(), $expected);
	}

	public function test_orm_field_get_nombre_bd()
	{
		$expected = 'prueba';
		$field = $this->get_field();

		$this->test($field->get_nombre_bd(), $expected);
	}

	public function test_orm_field_get_mostrar_lista()
	{
		$expected = TRUE;
		$field = $this->get_field();

		$this->test($field->get_mostrar_lista(), $expected);
	}

	public function test_orm_field_get_es_id()
	{
		$expected = TRUE;
		$field = $this->get_field();

		$this->test($field->get_es_id(), $expected);
	}

	public function test_orm_field_get_es_unico()
	{
		$expected = TRUE;
		$field = $this->get_field();

		$this->test($field->get_es_unico(), $expected);
	}

	public function test_orm_field_get_es_obligatorio()
	{
		$expected = TRUE;
		$field = $this->get_field();

		$this->test($field->get_es_obligatorio(), $expected);
	}

	public function test_orm_field_get_es_autoincrement()
	{
		$expected = FALSE;
		$field = $this->get_field();

		$this->test($field->get_es_autoincrement(), $expected);
	}

	public function test_orm_field_get_texto_ayuda()
	{
		$expected = 'texto_ayuda_prueba';
		$field = $this->get_field();

		$this->test($field->get_texto_ayuda(), $expected);
	}

	public function test_orm_field_get_relation()
	{
		$expected = [];
		$field = $this->get_field();

		$this->test($field->get_relation(), $expected);
	}

	public function xx_test_form_field()
	{
		$expected = form_input([
			'name' => 'prueba',
		]);
		$field = $this->get_field();

		$this->test($field->form_field(), $expected);
	}

}
