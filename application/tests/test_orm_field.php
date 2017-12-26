<?php

use test\test_case;
use Model\Orm_field;

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
		$this->assert_equals($this->get_field()->get_tipo(), Orm_field::TIPO_CHAR);
		$this->assert_equals($this->get_field(['tipo' => Orm_field::TIPO_INT])->get_tipo(), Orm_field::TIPO_INT);
	}

	public function test_get_label()
	{
		$this->assert_equals($this->get_field()->get_label(), 'label_prueba');
	}

	public function test_get_nombre_bd()
	{
		$this->assert_equals($this->get_field()->get_nombre_bd(), 'prueba');
	}

	public function test_get_mostrar_lista()
	{
		$this->assert_true($this->get_field()->get_mostrar_lista());
	}

	public function test_get_es_id()
	{
		$this->assert_true($this->get_field()->get_es_id());
		$this->assert_false($this->get_field(['es_id' => FALSE])->get_es_id());
	}

	public function test_get_es_unico()
	{
		$this->assert_true($this->get_field()->get_es_unico());
		$this->assert_false($this->get_field(['es_unico' => FALSE])->get_es_unico());
	}

	public function test_get_es_obligatorio()
	{
		$this->assert_true($this->get_field()->get_es_obligatorio());
		$this->assert_false($this->get_field(['es_obligatorio' => FALSE])->get_es_obligatorio());
	}

	public function test_get_es_autoincrement()
	{
		$this->assert_false($this->get_field()->get_es_autoincrement());
		$this->assert_true($this->get_field(['es_autoincrement' => TRUE])->get_es_autoincrement());
	}

	public function test_get_texto_ayuda()
	{
		$this->assert_equals($this->get_field()->get_texto_ayuda(), 'texto_ayuda_prueba');
	}

	public function test_get_relation()
	{
		$this->assert_equals($this->get_field()->get_relation(), []);
	}

	public function test_form_field_text_type()
	{
		$this->assert_contains($this->get_field()->form_field(), 'type="text"');
	}

	public function test_form_field_text_value_empty()
	{
		$this->assert_contains($this->get_field()->form_field(), 'value=""');
	}

	public function test_form_field_text_value_non_empty()
	{
		$this->assert_contains($this->get_field()->form_field('abc'), 'value="abc"');
	}

	public function test_form_field_text_id()
	{
		$this->assert_contains($this->get_field(['es_id'=>FALSE])->form_field('abc'), 'id="id_prueba"');
	}

	public function test_form_field_text_class()
	{
		$this->assert_contains($this->get_field(['es_id'=>FALSE])->form_field('abc'), 'class="form-control "');
	}

	public function test_form_field_text_maxlength()
	{
		$this->assert_contains($this->get_field(['largo'=>4, 'es_id'=>FALSE])->form_field('abc'), 'maxlength="4"');
	}

	public function test_form_field_text_size()
	{
		$this->assert_contains($this->get_field(['largo'=>4, 'es_id'=>FALSE])->form_field('abc'), 'size="4"');
	}

	public function test_form_field_text_placeholder()
	{
		$this->assert_contains($this->get_field(['es_id'=>FALSE, 'label'=>'label_label_label'])->form_field('abc'), 'placeholder="Ingrese label_label_label"');
	}

	public function test_form_field_choices()
	{
		$field = $this->get_field(['es_id'=>FALSE, 'es_unico'=>FALSE, 'label'=>'label_label_label', 'choices' => ['a'=>1,'b'=>2,'c'=>3]]);

		$this->assert_contains($field->form_field('abc'), '<select name="prueba"');
	}

	public function test_cast_value_char()
	{
		$this->assert_equals($this->get_field()->cast_value('valor'), 'valor');
	}

	public function test_cast_value_integer()
	{
		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_INT])->cast_value('123'), 123);
	}

	public function test_cast_value_boolean()
	{
		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN])->cast_value('FALSE'), 0);
	}

	public function test_cast_value_datetime()
	{
		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_DATETIME])->cast_value('20171010'), '2017-10-10 00:00:00');
	}

	public function test_get_formatted_value_char()
	{
		$this->assert_equals($this->get_field()->get_formatted_value('prueba'), 'prueba');
	}

	public function test_get_formatted_value_integer_sin_formato()
	{
		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_INT])->get_formatted_value('1234'), '1234');
	}

	public function test_get_formatted_value_integer_con_formato()
	{
		// monto
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'monto,0']);
		$this->assert_equals($field->get_formatted_value('1234'), '$&nbsp;1.234');

		// cantidad
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'cantidad,2']);
		$this->assert_equals($field->get_formatted_value('1234'), '1.234,00');

		// otro
		$field = $this->get_field(['tipo' => ORM_field::TIPO_INT, 'formato' => 'otro,0']);
		$this->assert_equals($field->get_formatted_value('1234'), 'XX1234XX');
	}

	public function test_get_formatted_value_choices_con_formato()
	{
		$field = $this->get_field([
			'choices' => [
				'valor'      => 'Valor Traducido',
				'otro_valor' => 'Otro Valor Traducido',
			],
		]);

		$this->assert_equals($field->get_formatted_value('valor'), 'Valor Traducido');
	}

	public function test_get_formatted_value_boolean_con_formato()
	{
		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN])->get_formatted_value(1), lang('orm_radio_yes'));

		$this->assert_equals($this->get_field(['tipo' => ORM_field::TIPO_BOOLEAN])->get_formatted_value(0), lang('orm_radio_no'));
	}


}
