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
class test_orm_model extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	protected function get_model($config = [])
	{
		return new model_test();
	}

	protected function get_data01()
	{
		return [
			'campo01' => 'value01',
			'campo02' => 'value02',
			'campo03' => 3,
			'campo04' => 1,
		];
	}

	public function test_get_model_nombre()
	{
		$expected = 'model_test';
		$model = $this->get_model();

		$this->assert_equals($model->get_model_nombre(), $expected);
	}

	public function test_get_tabla()
	{
		$expected = 'model_tabla';
		$model = $this->get_model();

		$this->assert_equals($model->get_tabla(), $expected);
	}

	public function test_get_label()
	{
		$expected = 'model_label';
		$model = $this->get_model();

		$this->assert_equals($model->get_label(), $expected);
	}

	public function test_get_label_plural()
	{
		$expected = 'model_label_plural';
		$model = $this->get_model();

		$this->assert_equals($model->get_label_plural(), $expected);
	}

	public function test_get_fields()
	{
		$campo01_def = [
			'label'          => 'label_campo01',
			'tabla_bd'       => 'model_tabla',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'texto_ayuda_campo01',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		];

		$campo02_def = [
			'label'          => 'label_campo02',
			'tabla_bd'       => 'model_tabla',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'texto_ayuda_campo02',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		];

		$campo03_def = [
			'label'          => 'label_campo03',
			'tabla_bd'       => 'model_tabla',
			'tipo'           => Orm_field::TIPO_INT,
			'largo'          => 10,
			'texto_ayuda'    => 'texto_ayuda_campo03',
		];

		$campo04_def = [
			'label'          => 'label_campo04',
			'tabla_bd'       => 'model_tabla',
			'tipo'           => Orm_field::TIPO_BOOLEAN,
			'texto_ayuda'    => 'texto_ayuda_campo04',
		];

		$expected = [
			'campo01' => (array) new Orm_field('campo01', $campo01_def),
			'campo02' => (array) new Orm_field('campo02', $campo02_def),
			'campo03' => (array) new Orm_field('campo03', $campo03_def),
			'campo04' => (array) new Orm_field('campo04', $campo04_def),
		];
		$model = $this->get_model();

		$this->assert_equals(collect($model->get_fields())->map(function($campo) {return (array) $campo;})->all(), $expected);
	}

	public function test_get_filtro()
	{
		$expected = 'filtro';
		$model = $this->get_model();

		$model->set_filtro('filtro');

		$this->assert_equals($model->get_filtro(), $expected);
	}

	public function test_get_mostrar_lista_campo_existe()
	{
		$this->assert_true($this->get_model()->get_mostrar_lista('campo01'));
	}

	public function test_get_mostrar_lista_campo_no_existe()
	{
		$this->assert_false($this->get_model()->get_mostrar_lista());
	}

	public function test_fill_array()
	{
		$expected = $this->get_data01();
		$model = $this->get_model();

		$this->assert_equals($model->fill($this->get_data01())->get_fields_values(), $expected);
	}

	public function test_fill_array_non_existent_key()
	{
		$expected = $this->get_data01();
		$fill = array_merge($this->get_data01(), ['campo05' => 'campo05', 'campo06' => 6]);

		$this->assert_equals($this->get_model()->fill($fill)->get_fields_values(), $expected);
	}

	public function test_has_attributes_get_campo_existe()
	{
		$expected = 'value01';
		$model = $this->get_model()->fill(['campo01' => 'value01']);

		$this->assert_equals($model->campo01, $expected);
	}

	public function test_has_attributes_get_campo_no_existe()
	{
		$model = $this->get_model()->fill(['campo01' => 'value01']);

		$this->assert_null($model->campo02);
	}

	public function test_has_attributes_get_campo_id()
	{
		$expected = ['campo01', 'campo02'];
		$model = $this->get_model();

		$this->assert_equals($model->get_campo_id(), $expected);
	}

	public function test_has_attributes_get_fields_values()
	{
		$expected = $this->get_data01();
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_fields_values(), $expected);
	}

	public function test_has_attributes_get_id()
	{
		$expected = 'value01~value02';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_id(), $expected);
	}

	public function test_has_attributes_get_field_value_char()
	{
		$expected = 'value01';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo01'), $expected);
	}

	public function test_has_attributes_get_field_value_integer()
	{
		$expected = 3;
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo03'), $expected);
	}

	public function test_has_attributes_get_field_value_boolean_formatted()
	{
		$expected = lang('orm_radio_yes');
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo04'), $expected);
	}

	public function test_has_attributes_get_field_value_boolean_not_formatted()
	{
		$expected = 1;
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo04', FALSE), $expected);
	}

	public function test_has_attributes_fill_from_array()
	{
		$expected = $this->get_data01();

		$data01 = $this->get_data01();
		$data01['campo_xxx'] = 'xxx';

		$model = $this->get_model()->fill_from_array($data01);

		$this->assert_equals($model->get_fields_values(), $expected);
	}

	public function test_has_form_form_item()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_is_string($model->form_item('campo01'));
	}

	public function test_has_form_get_field_label()
	{
		$expected = 'label_campo01';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_label('campo01'), $expected);
	}

	public function test_has_form_get_field_texto_ayuda()
	{
		$expected = 'texto_ayuda_campo01';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_texto_ayuda('campo01'), $expected);
	}

	public function test_has_form_field_es_obligatorio()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_true($model->field_es_obligatorio('campo01'));
	}

	public function test_has_form_get_field_marca_obligatorio_TRUE()
	{
		$expected = ' <span class="text-danger">*</span>';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_marca_obligatorio('campo01'), $expected);
	}

	public function test_has_form_get_field_marca_obligatorio_FALSE()
	{
		$expected = '';
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_marca_obligatorio('campo03'), $expected);
	}


}

class model_test extends ORM_Model {

	public function __construct($id = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => 'model_tabla',
				'label'        => 'model_label',
				'label_plural' => 'model_label_plural',
				'order_by'     => 'model_order_by',
			],
			'campos' => [
				'campo01' => [
					'label'          => 'label_campo01',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'texto_ayuda_campo01',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'campo02' => [
					'label'          => 'label_campo02',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'texto_ayuda_campo02',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'campo03' => [
					'label'          => 'label_campo03',
					'tipo'           => Orm_field::TIPO_INT,
					'largo'          => 10,
					'texto_ayuda'    => 'texto_ayuda_campo03',
				],
				'campo04' => [
					'label'          => 'label_campo04',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'texto_ayuda_campo04',
				],
			],
		];

		parent::__construct();
	}
}