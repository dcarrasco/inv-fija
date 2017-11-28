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

	public function test_get_tipo()
	{
		$expected = Orm_field::TIPO_CHAR;

		$this->assert_equals($this->get_field()->get_tipo(), $expected);
	}

	public function test_get_label()
	{
		$expected = 'label_prueba';

		$this->assert_equals($this->get_field()->get_label(), $expected);
	}

	public function test_get_nombre_bd()
	{
		$expected = 'prueba';

		$this->assert_equals($this->get_field()->get_nombre_bd(), $expected);
	}

	public function test_get_mostrar_lista()
	{
		$this->assert_true($this->get_field()->get_mostrar_lista());
	}

	public function test_get_es_id()
	{
		$this->assert_true($this->get_field()->get_es_id());
	}

	public function test_get_es_unico()
	{
		$this->assert_true($this->get_field()->get_es_unico());
	}

	public function test_get_es_obligatorio()
	{
		$this->assert_true($this->get_field()->get_es_obligatorio());
	}

	public function test_get_es_autoincrement()
	{
		$this->assert_false($this->get_field()->get_es_autoincrement());
	}

	public function test_get_texto_ayuda()
	{
		$expected = 'texto_ayuda_prueba';

		$this->assert_equals($this->get_field()->get_texto_ayuda(), $expected);
	}

	public function test_get_relation()
	{
		$expected = [];

		$this->assert_equals($this->get_field()->get_relation(), $expected);
	}

	public function xx_test_form_field()
	{
		$expected = form_input([
			'name' => 'prueba',
		]);

		$this->assert_equals($this->get_field()->form_field(), $expected);
	}

	public function test_cast_value_char()
	{
		$expected = 'valor';

		$this->assert_equals($this->get_field()->cast_value('valor'), $expected);
	}

	public function test_cast_value_integer()
	{
		$expected = 123;
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT]);

		$this->assert_equals($field->cast_value('123'), $expected);
	}

	public function test_cast_value_boolean()
	{
		$expected = 0;
		$field = $this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN]);

		$this->assert_equals($field->cast_value('FALSE'), $expected);
	}

	public function test_cast_value_datetime()
	{
		$expected = '2017-10-10 00:00:00';
		$field = $this->get_field(['tipo' => ORM_field::TIPO_DATETIME]);

		$this->assert_equals($field->cast_value('20171010'), $expected);
	}

	public function test_get_formatted_value_char()
	{
		$expected = 'prueba';
		$field = $this->get_field();

		$this->assert_equals($field->get_formatted_value('prueba'), $expected);
	}

	public function test_get_formatted_value_integer_sin_formato()
	{
		$expected = '1234';
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT]);

		$this->assert_equals($field->get_formatted_value('1234'), $expected);
	}

	public function test_get_formatted_value_integer_con_formato_monto()
	{
		$expected = '$&nbsp;1.234';
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'monto,0']);

		$this->assert_equals($field->get_formatted_value('1234'), $expected);
	}

	public function test_get_formatted_value_integer_con_formato_cantidad()
	{
		$expected = '1.234,00';
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'cantidad,2']);

		$this->assert_equals($field->get_formatted_value('1234'), $expected);
	}

	public function test_get_formatted_value_integer_con_formato_otro()
	{
		$expected = 'XX1234XX';
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'otro,0']);

		$this->assert_equals($field->get_formatted_value('1234'), $expected);
	}

	public function test_get_formatted_value_choices_con_formato()
	{
		$expected = 'Valor Traducido';
		$field = $this->get_field([
			'choices' => [
				'valor'      => 'Valor Traducido',
				'otro_valor' => 'Otro Valor Traducido',
			],
		]);

		$this->assert_equals($field->get_formatted_value('valor'), $expected);
	}

	public function test_get_formatted_value_boolean_true_con_formato()
	{
		$expected = lang('orm_radio_yes');
		$field = $this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN]);

		$this->assert_equals($field->get_formatted_value(1), $expected);
	}

	public function test_get_formatted_value_boolean_false_con_formato()
	{
		$expected = lang('orm_radio_no');
		$field = $this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN]);

		$this->assert_equals($field->get_formatted_value(0), $expected);
	}


}
