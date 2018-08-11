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

	public function setUp()
	{
		$model = new Orm_model;
		$this->model_test = $model->set_fields($this->get_campos());
		$this->model_test->__construct();
		$this->model_test = $this->model_test->fill($this->get_values());
	}

	public function get_campos()
	{
		return [
			'campo01' => [
				'label'          => 'label_campo01',
				'tipo'           => Orm_field::TIPO_CHAR,
				'largo'          => 10,
				'texto_ayuda'    => 'texto_ayuda_campo01',
				'es_id'          => TRUE,
				'es_obligatorio' => TRUE,
				'es_unico'       => TRUE,
			],
			'campo02' => [
				'label'          => 'label_campo02',
				'tipo'           => Orm_field::TIPO_CHAR,
				'largo'          => 10,
				'texto_ayuda'    => 'texto_ayuda_campo02',
				'es_id'          => TRUE,
				'es_obligatorio' => TRUE,
				'es_unico'       => TRUE,
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
	}

	public function get_values()
	{
		return [
			'campo01' => '01',
			'campo02' => '02',
			'campo03' => '3',
			'campo04' => FALSE,
		];
	}


	public function test_create()
	{
		$this->assertInstanceOf(Orm_model::class, Orm_model::create());
		$this->assertInstanceOf(Orm_model::class, Orm_model::create(['campo01' => 'aaa']));
	}

	public function test_get_model_nombre()
	{
		$this->assertEquals('model\orm_model', $this->model_test->get_model_nombre());
		$this->assertEquals('orm_model', $this->model_test->get_model_nombre(FALSE));
	}

	public function test_get_label()
	{
		$this->assertEquals('Orm_model', $this->model_test->get_label());
	}

	public function test_get_label_plural()
	{
		$this->assertEquals('Orm_models', $this->model_test->get_label_plural());
	}

	public function test_set_fields()
	{
		$this->assertEquals(['a'=>'a', 'b'=>'b'],
			$this->model_test->set_fields(['a'=>'a', 'b'=>'b'])->get_fields());
	}

	public function test_get_fields()
	{
		$model = new Orm_model;
		$this->assertEquals($model->config_fields($this->get_campos()), $this->model_test->get_fields());
	}

	public function test_get_field()
	{
		$this->assertNull($this->model_test->get_field('a'));

		$model = new Orm_model;
		$campo = array_get($model->config_fields($this->get_campos()), 'campo01');
		$this->assertEquals($campo, $this->model_test->get_field('campo01'));
	}

	// -------------------------------------------------------------------------
	// model_has_form
	// -------------------------------------------------------------------------

	// public function test_has_form_form_item()
	// {
	// 	$model = $this->model_test->fill($this->get_data01());

	// 	$this->assertInternalType('string', $model->form_item('campo01'));
	// 	$this->assertContains('<div class="form-group', $model->form_item('campo01'));
	// 	$this->assertContains('<label for', $model->form_item('campo01'));
	// 	$this->assertContains('Label_campo01', $model->form_item('campo01'));
	// 	$this->assertContains('<span class="text-danger">*</span>', $model->form_item('campo01'));
	// 	$this->assertContains('<span class="help-block">', $model->form_item('campo01'));
	// 	$this->assertContains('texto_ayuda_campo01', $model->form_item('campo01'));
	// }

	// public function test_has_form_get_field_label()
	// {
	// 	$model = $this->model_test->fill($this->get_data01());

	// 	$this->assertEquals($model->get_field('campo01')->get_label(), 'label_campo01');
	// }

	// public function test_has_form_get_field_texto_ayuda()
	// {
	// 	$model = $this->model_test->fill($this->get_data01());

	// 	$this->assertEquals($model->get_field('campo01')->get_texto_ayuda(), 'texto_ayuda_campo01');
	// }

	// public function test_has_form_field_es_obligatorio()
	// {
	// 	$model = $this->model_test->fill($this->get_data01());

	// 	$this->assertTrue($model->get_field('campo01')->get_es_obligatorio());
	// 	$this->assertFalse($model->get_field('campo03')->get_es_obligatorio());
	// }

	// public function test_has_form_get_field_marca_obligatorio()
	// {
	// 	$model = $this->model_test->fill($this->get_data01());

	// 	$this->assertEquals($model->get_marca_obligatorio('campo01'), ' <span class="text-danger">*</span>');
	// 	$this->assertEquals($model->get_marca_obligatorio('campo03'), '');
	// }

	// -------------------------------------------------------------------------
	// model_uses_database
	// -------------------------------------------------------------------------

	public function test_get_order_by()
	{
		$this->assertEquals('', $this->model_test->get_order_by());
	}

	public function test_get_filtro()
	{
		$this->assertEquals('', $this->model_test->get_filtro());
	}

	public function test_set_filtro()
	{
		$this->model_test->set_filtro('filtro');
		$this->assertEquals('filtro', $this->model_test->get_filtro());
	}

	public function test_get_db_table()
	{
		$this->assertEquals('model_orm_model', $this->model_test->get_db_table());
		$this->assertEquals('tabla', $this->model_test->get_db_table('tabla'));
	}

	// -------------------------------------------------------------------------
	// model_has_attributes
	// -------------------------------------------------------------------------

	public function test_has_attributes_get_campo_id()
	{
		$this->assertEquals(['campo01', 'campo02'], $this->model_test->get_campo_id());
	}

	public function test_has_attributes_get_value()
	{
		$this->assertNull($this->model_test->get_value('campo'));
		$this->assertEquals('01', $this->model_test->get_value('campo01'));
	}

	public function test_has_attributes_set_value()
	{
		$this->assertEquals('valor', $this->model_test->set_value('campo01', 'valor')->get_value('campo01'));
	}

	public function test_get_list_fields()
	{
		$this->assertEquals(array_keys($this->get_campos()), $this->model_test->get_list_fields());
	}

	public function test_has_attributes_get_values()
	{
		$this->assertEquals($this->get_values(), $this->model_test->get_values());
	}

	public function test_has_attributes_determina_campo_id()
	{
		$this->assertEquals(['campo01', 'campo02'], $this->model_test->determina_campo_id());
	}

	public function test_has_attributes_get_id()
	{
		$this->assertEquals('01~02', $this->model_test->get_id());
	}

	public function test_has_attributes_get_field_value()
	{
		$this->assertEquals('01', $this->model_test->get_field_value('campo01'));
	}

	public function test_has_attributes_fill()
	{
		$this->model_test = $this->model_test->fill($this->get_values());
		$this->assertEquals($this->get_values(), $this->model_test->get_values());
	}

	// -------------------------------------------------------------------------
	// model_has_pagination
	// -------------------------------------------------------------------------

	public function test_get_page_results()
	{
		$this->assertEquals(12, $this->model_test->get_page_results());
	}

	public function test_get_pagination_links()
	{
		$this->assertEquals('', $this->model_test->get_pagination_links());
	}

	// -------------------------------------------------------------------------
	// model_has_persistance
	// -------------------------------------------------------------------------

	public function test_has_persistance_array_id()
	{
		$this->assertEquals(['campo01'=>'0101', 'campo02'=>'0202'], $this->model_test->array_id('0101~0202'));
	}

	public function test_has_persistance_values_id_fields()
	{
		$this->assertEquals(['campo01'=>'01', 'campo02'=>'02'], $this->model_test->values_id_fields());
	}

	public function test_has_persistance_values_not_id_fields()
	{
		$this->assertEquals(['campo03'=>3, 'campo04'=>false], $this->model_test->values_not_id_fields());
	}

	public function test_has_persistance_has_autoincrement_id()
	{
		$this->assertFalse($this->model_test->has_autoincrement_id());
	}


	// -------------------------------------------------------------------------

	// public function test_fill_array()
	// {
	// 	$this->assertEquals($this->model_test->fill($this->get_data01())->get_values(), $this->get_data01());

	// 	$fill = array_merge($this->get_data01(), ['campo05' => 'campo05', 'campo06' => 6]);
	// 	$this->assertEquals($this->model_test->fill($fill)->get_values(), $this->get_data01());
	// }

	// public function test_has_attributes_get_campo()
	// {
	// 	$this->assertEquals($this->model_test->fill(['campo01' => 'value01'])->campo01, 'value01');
	// 	$this->assertEquals($this->model_test->fill(['campo01' => 'value01'])->campo02, '');
	// }

	// public function test_has_attributes_determina_campo_id()
	// {
		// $this->assertEquals($this->get_method('_determina_campo_id'), ['campo01', 'campo02']);
	// }

	// public function test_has_attributes_fill()
	// {
	// 	$data01 = $this->get_data01();
	// 	$data01['campo_xxx'] = 'xxx';

	// 	$model = $this->model_test->fill($data01);

	// 	$this->assertEquals($model->get_values(), $this->get_data01());
	// }

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
