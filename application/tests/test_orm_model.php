<?php

use test\test_case;
use Model\Orm_model;
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
class test_orm_model extends test_case {

	public function __construct()
	{
		$this->mock = ['db'];
		$this->test_class = model_test::class;

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

	public function test_create()
	{
		$this->assert_is_object(model_test::create());
		$this->assert_equals(json_encode(model_test::create()), json_encode(new model_test));
	}

	public function test_get_model_nombre()
	{
		$this->assert_equals($this->get_model()->get_model_nombre(), 'model_test');
	}

	public function test_get_tabla()
	{
		$this->assert_equals($this->get_model()->get_tabla(), 'model_tabla');
	}

	public function test_get_label()
	{
		$this->assert_equals($this->get_model()->get_label(), 'model_label');
	}

	public function test_get_label_plural()
	{
		$this->assert_equals($this->get_model()->get_label_plural(), 'model_label_plural');
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
			'formato'        => 'cantidad,0',
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
		$model = $this->get_model();
		$model->set_filtro('filtro');

		$this->assert_equals($model->get_filtro(), 'filtro');
	}

	public function test_get_mostrar_lista()
	{
		$this->assert_true($this->get_model()->get_mostrar_lista('campo01'));
		$this->assert_false($this->get_model()->get_mostrar_lista('campo_no_existe'));
	}

	public function test_fill_array()
	{
		$this->assert_equals($this->get_model()->fill($this->get_data01())->get_fields_values(), $this->get_data01());

		$fill = array_merge($this->get_data01(), ['campo05' => 'campo05', 'campo06' => 6]);
		$this->assert_equals($this->get_model()->fill($fill)->get_fields_values(), $this->get_data01());
	}

	public function test_has_attributes_get_campo()
	{
		$this->assert_equals($this->get_model()->fill(['campo01' => 'value01'])->campo01, 'value01');
		$this->assert_null($this->get_model()->fill(['campo01' => 'value01'])->campo02);
	}

	public function test_has_attributes_get_campo_id()
	{
		$this->assert_equals($this->get_model()->get_campo_id(), ['campo01', 'campo02']);
	}

	public function test_has_attributes_get_fields_values()
	{
		$expected = $this->get_data01();
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_fields_values(), $expected);
	}

	public function test_has_attributes_determina_campo_id()
	{
		$this->assert_equals($this->get_method('_determina_campo_id'), ['campo01', 'campo02']);
	}

	public function test_has_attributes_get_id()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_id(), 'value01~value02');
	}

	public function test_has_attributes_get_field_value_char()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo01'), 'value01');
		$this->assert_equals($model->get_field_value('campo01', FALSE), 'value01');
	}

	public function test_has_attributes_get_field_value_integer()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo03'), '3');
		$this->assert_equals($model->get_field_value('campo03', FALSE), 3);

		$model->campo03 = 3000;
		$this->assert_equals($model->get_field_value('campo03'), '3.000');
		$this->assert_equals($model->get_field_value('campo03', FALSE), 3000);
	}

	public function test_has_attributes_get_field_value_boolean()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_value('campo04'), lang('orm_radio_yes'));
		$this->assert_equals($model->get_field_value('campo04', FALSE), 1);
	}

	public function test_has_attributes_get_field_value_boolean_not_formatted()
	{
	}

	public function test_has_attributes_fill_from_array()
	{
		$data01 = $this->get_data01();
		$data01['campo_xxx'] = 'xxx';

		$model = $this->get_model()->fill_from_array($data01);

		$this->assert_equals($model->get_fields_values(), $this->get_data01());
	}

	public function test_has_form_form_item()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_is_string($model->form_item('campo01'));
		$this->assert_contains($model->form_item('campo01'), '<div class="form-group');
		$this->assert_contains($model->form_item('campo01'), '<label for');
		$this->assert_contains($model->form_item('campo01'), 'Label_campo01');
		$this->assert_contains($model->form_item('campo01'), '<span class="text-danger">*</span>');
		$this->assert_contains($model->form_item('campo01'), '<span class="help-block">');
		$this->assert_contains($model->form_item('campo01'), 'texto_ayuda_campo01');
	}

	public function test_has_form_get_field_label()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_label('campo01'), 'label_campo01');
	}

	public function test_has_form_get_field_texto_ayuda()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_texto_ayuda('campo01'), 'texto_ayuda_campo01');
	}

	public function test_has_form_field_es_obligatorio()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_true($model->field_es_obligatorio('campo01'));
		$this->assert_false($model->field_es_obligatorio('campo03'));
	}

	public function test_has_form_get_field_marca_obligatorio()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assert_equals($model->get_field_marca_obligatorio('campo01'), ' <span class="text-danger">*</span>');
		$this->assert_equals($model->get_field_marca_obligatorio('campo03'), '');
	}

	public function test_has_persistance_find()
	{
		$model = model_test::create();

		$rs_1 = ['campo01' => 'valor11', 'campo02' => 'valor21', 'campo03' => 31, 'campo04' => 1];
		$rs_2 = ['campo01' => 'valor12', 'campo02' => 'valor22', 'campo03' => 32, 'campo04' => 1];
		$rs_3 = ['campo01' => 'valor13', 'campo02' => 'valor23', 'campo03' => 33, 'campo04' => 1];

		app('db')->mock_set_return_result($rs_1);
		$this->assert_equals($model->find('first')->get_fields_values(), $rs_1);

		app('db')->mock_set_return_result([$rs_1, $rs_2, $rs_3]);
		$this->assert_equals(json_encode($model->find('all')->get(0)), json_encode(model_test::create()->fill_from_array($rs_1)));
		$this->assert_equals(json_encode($model->find('all')->get(1)), json_encode(model_test::create()->fill_from_array($rs_2)));
		$this->assert_equals(json_encode($model->find('all')->get(2)), json_encode(model_test::create()->fill_from_array($rs_3)));
		$this->assert_equals($model->find('count'), 3);

		$rs_list = ['valor11~valor21' => 'valor11', 'valor12~valor22' => 'valor12', 'valor13~valor23' => 'valor13'];
		$this->assert_equals($model->find('list', ['opc_ini'=>FALSE]), $rs_list);
		$this->assert_equals($model->find('list'), array_merge(['' => 'Seleccione model_label...'], $rs_list));
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
					'formato'        => 'cantidad,0',
				],
				'campo04' => [
					'label'          => 'label_campo04',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'texto_ayuda_campo04',
				],
			],
		];

		parent::__construct($id);
	}

	public function __toString()
	{
		return $this->campo01;
	}
}
