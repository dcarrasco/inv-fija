<?php
use Model\Orm_field;
use Model\Orm_model;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class Orm_model_test extends TestCase {

	// public function __construct()
	// {
	// 	$this->mock = ['db'];
	// 	$this->test_class = model_test::class;

	//     parent::__construct();
	// }

	// public function setUp()
	// {
	// 	$this->model_test = new model_test();
	// }

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
		$this->assertInstanceOf(Orm_model::class, model_test::create());
		$this->assertEquals(json_encode(model_test::create()), json_encode(new model_test));
	}

	public function test_get_model_nombre()
	{
		$this->assertEquals($this->get_model()->get_model_nombre(), 'model_test');
	}

	public function test_get_db_table()
	{
		$this->assertEquals($this->get_model()->get_db_table(), 'model_tabla');
	}

	public function test_get_label()
	{
		$this->assertEquals($this->get_model()->get_label(), 'model_label');
	}

	public function test_get_label_plural()
	{
		$this->assertEquals($this->get_model()->get_label_plural(), 'model_label_plural');
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

		$this->assertEquals(collect($model->get_fields())->map(function($campo) {return (array) $campo;})->all(), $expected);
	}

	public function test_get_filtro()
	{
		$model = $this->get_model();
		$model->set_filtro('filtro');

		$this->assertEquals($model->get_filtro(), 'filtro');
	}

	public function test_get_list_fields()
	{
		$this->assertEquals($this->get_model()->get_list_fields(), ['campo01', 'campo02', 'campo03', 'campo04']);
	}

	public function test_fill_array()
	{
		$this->assertEquals($this->get_model()->fill($this->get_data01())->get_values(), $this->get_data01());

		$fill = array_merge($this->get_data01(), ['campo05' => 'campo05', 'campo06' => 6]);
		$this->assertEquals($this->get_model()->fill($fill)->get_values(), $this->get_data01());
	}

	public function test_has_attributes_get_campo()
	{
		$this->assertEquals($this->get_model()->fill(['campo01' => 'value01'])->campo01, 'value01');
		$this->assertEquals($this->get_model()->fill(['campo01' => 'value01'])->campo02, '');
	}

	public function test_has_attributes_get_campo_id()
	{
		$this->assertEquals($this->get_model()->get_campo_id(), ['campo01', 'campo02']);
	}

	public function test_has_attributes_get_values()
	{
		$expected = $this->get_data01();
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_values(), $expected);
	}

	// public function test_has_attributes_determina_campo_id()
	// {
		// $this->assertEquals($this->get_method('_determina_campo_id'), ['campo01', 'campo02']);
	// }

	public function test_has_attributes_get_id()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_id(), 'value01~value02');
	}

	public function test_has_attributes_get_field_value_char()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_field_value('campo01'), 'value01');
		$this->assertEquals($model->get_field_value('campo01', FALSE), 'value01');
	}

	public function test_has_attributes_get_field_value_integer()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_field_value('campo03'), '3');
		$this->assertEquals($model->get_field_value('campo03', FALSE), 3);

		$model->campo03 = 3000;
		$this->assertEquals($model->get_field_value('campo03'), '3.000');
		$this->assertEquals($model->get_field_value('campo03', FALSE), 3000);
	}

	public function test_has_attributes_get_field_value_boolean()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_field_value('campo04'), lang('orm_radio_yes'));
		$this->assertEquals($model->get_field_value('campo04', FALSE), 1);
	}

	public function test_has_attributes_fill()
	{
		$data01 = $this->get_data01();
		$data01['campo_xxx'] = 'xxx';

		$model = $this->get_model()->fill($data01);

		$this->assertEquals($model->get_values(), $this->get_data01());
	}

	public function test_has_form_form_item()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertInternalType('string', $model->form_item('campo01'));
		$this->assertContains('<div class="form-group', $model->form_item('campo01'));
		$this->assertContains('<label for', $model->form_item('campo01'));
		$this->assertContains('Label_campo01', $model->form_item('campo01'));
		$this->assertContains('<span class="text-danger">*</span>', $model->form_item('campo01'));
		$this->assertContains('<span class="help-block">', $model->form_item('campo01'));
		$this->assertContains('texto_ayuda_campo01', $model->form_item('campo01'));
	}

	public function test_has_form_get_field_label()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_field('campo01')->get_label(), 'label_campo01');
	}

	public function test_has_form_get_field_texto_ayuda()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_field('campo01')->get_texto_ayuda(), 'texto_ayuda_campo01');
	}

	public function test_has_form_field_es_obligatorio()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertTrue($model->get_field('campo01')->get_es_obligatorio());
		$this->assertFalse($model->get_field('campo03')->get_es_obligatorio());
	}

	public function test_has_form_get_field_marca_obligatorio()
	{
		$model = $this->get_model()->fill($this->get_data01());

		$this->assertEquals($model->get_marca_obligatorio('campo01'), ' <span class="text-danger">*</span>');
		$this->assertEquals($model->get_marca_obligatorio('campo03'), '');
	}

	// public function test_has_persistance_find()
	// {
	// 	$model = model_test::create();

	// 	$rs_1 = [0 => ['campo01' => 'valor11', 'campo02' => 'valor21', 'campo03' => 31, 'campo04' => 1]];
	// 	$rs_2 = ['campo01' => 'valor12', 'campo02' => 'valor22', 'campo03' => 32, 'campo04' => 1];
	// 	$rs_3 = ['campo01' => 'valor13', 'campo02' => 'valor23', 'campo03' => 33, 'campo04' => 1];

	// 	app('db')->mock_set_return_result($rs_1);
	// 	$this->assertEquals($model->get_first()->get_values(), $rs_1[0]);

	// 	app('db')->mock_set_return_result([$rs_1[0], $rs_2, $rs_3]);
	// 	$this->assertEquals(json_encode($model->get()->get(0)), json_encode(model_test::create()->fill($rs_1)));
	// 	$this->assertEquals(json_encode($model->get()->get(1)), json_encode(model_test::create()->fill($rs_2)));
	// 	$this->assertEquals(json_encode($model->get()->get(2)), json_encode(model_test::create()->fill($rs_3)));

	// 	app('db')->mock_set_return_result(['cantidad' => 3]);
	// 	$this->assertEquals($model->count(), 3);

	// 	app('db')->mock_set_return_result([$rs_1[0], $rs_2, $rs_3]);
	// 	$rs_list = ['valor11~valor21' => 'valor11', 'valor12~valor22' => 'valor12', 'valor13~valor23' => 'valor13'];
	// 	$this->assertEquals($model->get_dropdown_list(FALSE), $rs_list);
	// 	$this->assertEquals($model->get_dropdown_list(), array_merge(['' => 'Seleccione model_label...'], $rs_list));
	// }

	// public function test_has_persistance_array_id()
	// {
	// 	$rs_1 = [0 => ['campo01' => 'valor11', 'campo02' => 'valor21', 'campo03' => 31, 'campo04' => 1]];
	// 	app('db')->mock_set_return_result($rs_1);
	// 	$model = model_test::create()->get_first();

	// 	$this->assertEquals($this->get_method('array_id', $model->get_id()), ['campo01'=>'valor11', 'campo02'=>'valor21']);
	// }

	// public function test_has_persistance_values_id_fields()
	// {
	// 	$rs_1 = [0 => ['campo01' => 'valor11', 'campo02' => 'valor21', 'campo03' => 31, 'campo04' => 1]];
	// 	app('db')->mock_set_return_result($rs_1);
	// 	$model = model_test::create()->get_first();

	// 	$method = new ReflectionMethod($this->test_class, 'values_id_fields');
	// 	$method->setAccessible(TRUE);

	// 	$this->assertEquals($method->invoke($model, $model->get_id()), ['campo01'=>'valor11', 'campo02'=>'valor21']);
	// }

	// public function test_has_persistance_values_not_id_fields()
	// {
	// 	$rs_1 = [0 => ['campo01' => 'valor11', 'campo02' => 'valor21', 'campo03' => 31, 'campo04' => 1]];
	// 	app('db')->mock_set_return_result($rs_1);
	// 	$model = model_test::create()->get_first();

	// 	$method = new ReflectionMethod($this->test_class, 'values_not_id_fields');
	// 	$method->setAccessible(TRUE);

	// 	$this->assertEquals($method->invoke($model, $model->get_id()), ['campo03'=>31, 'campo04'=>1]);
	// }

	// public function test_has_persistance_has_autoincrement_id()
	// {
	// 	$this->assertFalse($this->get_method('has_autoincrement_id'));
	// }

	// public function test_has_persistance_is_new_record()
	// {
	// 	app('db')->mock_set_return_result(NULL);
	// 	$this->assertTrue($this->get_method('is_new_record'));

	// 	app('db')->mock_set_return_result([1]);
	// 	$this->assertFalse($this->get_method('is_new_record'));
	// }

	// public function test_has_relationship_junta_campos_select()
	// {
	// 	$this->assertEquals($this->get_method('_junta_campos_select', ['campo01', 'campo02', 'campo03']), "campo01+'~'+campo02+'~'+campo03");
	// }

}

class model_test extends ORM_Model {

	protected $db_table     = 'model_tabla';
	protected $label        = 'model_label';
	protected $label_plural = 'model_label_plural';
	protected $order_by     = 'model_order_by';

	protected $fields = [
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
		];

	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	public function __toString()
	{
		return $this->campo01;
	}
}
