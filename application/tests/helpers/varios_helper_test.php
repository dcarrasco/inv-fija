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
class varios_helper_test extends TestCase {

	public function setUp()
	{
		$this->resetInstance();
	}

	public function test_menu_app()
	{
		$this->assertEquals([], menu_app());
	}

	public function test_titulo_modulo()
	{
		$this->assertInternalType('string', titulo_modulo());
		$this->assertContains('<i class=', titulo_modulo());
	}

	public function test_menu_modulo()
	{
		$this->assertEquals([], menu_modulo());
	}

	public function test_app_render_view()
	{
		$this->assertNull(app_render_view());
	}

	public function test_lang()
	{
		get_instance()->lang->load('acl_lang');

		$this->assertEquals(lang('acl_config_menu_usuarios'), 'Usuarios');
	}

	public function test_print_message()
	{
		$this->assertEquals('', print_message());
		$this->assertInternalType('string', print_message(''));
		$this->assertInternalType('string', print_message('prueba'));
		$this->assertContains('prueba', print_message('prueba'));
		$this->assertContains('info-sign', print_message('prueba'));
		$this->assertContains('warning-sign', print_message('prueba', 'warning'));
		$this->assertContains('exclamation-sign', print_message('prueba', 'danger'));
		$this->assertContains('exclamation-sign', print_message('prueba', 'error'));
		$this->assertContains('ok-sign', print_message('prueba', 'success'));
	}

	// public function test_set_message()
	// {
	// 	MonkeyPatch::patchMethod('CI_Session', ['set_flashdata' => 'set_flashdata']);
	// 	$this->assertEquals('', set_message());
	// }

	public function test_print_validation_errors()
	{
		// $this->resetInstance();
		// $this->CI->errors = collect();
		// dump($this->CI);
		$this->assertEquals('', print_validation_errors('a'));
	}

	public function test_form_array_format()
	{
		$test = [
			['llave' => 'a', 'valor' => '1', 'otro' => 'otro'],
			['llave' => 'b', 'valor' => '2', 'otro' => 'otro'],
			['llave' => 'c', 'valor' => '3', 'otro' => 'otro'],
		];

		$this->assertEquals(form_array_format($test), ['a'=>'1', 'b'=>'2', 'c'=>'3']);
		$this->assertEquals(form_array_format($test, 'mensaje ini'), [''=>'mensaje ini', 'a'=>'1','b' =>'2', 'c'=>'3']);
	}

	public function test_fmt_cantidad()
	{
		$this->assertEquals(fmt_cantidad(500), '500');
		$this->assertEquals(fmt_cantidad(5000), '5.000');
		$this->assertEquals(fmt_cantidad(5000.428, 2), '5.000,43');
		$this->assertEquals(fmt_cantidad(0, 0, TRUE), '0');
		$this->assertEquals(fmt_cantidad(0, 0, FALSE), '');
	}

	public function test_fmt_monto()
	{
		$this->assertEquals(fmt_monto(500), '$&nbsp;500');
		$this->assertEquals(fmt_monto(5000), '$&nbsp;5.000');
		$this->assertEquals(fmt_monto(5000.428, 'UN', '$', 2), '$&nbsp;5.000,43');
		$this->assertEquals(fmt_monto(0, 'UN', '$', 0, TRUE), '$&nbsp;0');
		$this->assertEquals(fmt_monto(0, 'UN', '$', 0, FALSE), '');
		$this->assertEquals(fmt_monto(1222, 'UN', 'CLP'), 'CLP&nbsp;1.222');
		$this->assertEquals(fmt_monto(222123123, 'MM'), 'MM$&nbsp;222');
		$this->assertEquals(fmt_monto('casa'), NULL);
	}

	public function test_fmt_hora()
	{
		$this->assertEquals(fmt_hora(33), '00:00:33');
		$this->assertEquals(fmt_hora(60), '00:01:00');
		$this->assertEquals(fmt_hora(133), '00:02:13');
		$this->assertEquals(fmt_hora(3733), '01:02:13');
	}

	public function test_fmt_fecha()
	{
		$this->assertEquals(fmt_fecha('20171011'), '2017-10-11');
		$this->assertEquals(fmt_fecha('20171011', 'Y---m---d'), '2017---10---11');
	}

	public function test_fmt_fecha_db()
	{
		$this->assertEquals(fmt_fecha_db('2017-10-11'), '20171011');
		$this->assertNull(fmt_fecha_db());
	}

	public function test_fmt_rut()
	{
		$this->assertEquals(fmt_rut('138889998'), '13.888.999-8');
		$this->assertEquals(fmt_rut('13888999-8'), '13.888.999-8');
		$this->assertEquals(fmt_rut(), NULL);

	}

	public function test_array_get()
	{
		$this->assertEquals(array_get([1,2,3,4,5], 2), 3);
		$this->assertNull(array_get([1,2,3,4,5], 10));
		$this->assertEquals(array_get([1,2,3,4,5], 10, 100), 100);
		$this->assertEquals(
			array_get(['uno' => ['dos' => ['tres' => 3, 'cuatro' => [1,2,3,4,5]]]], 'uno.dos.tres'),
			3
		);
		$this->assertEquals(
			array_get(['uno' => ['dos' => ['tres' => 3, 'cuatro' => [1,2,3,4,5]]]], 'uno.dos.cuatro.2'),
			3
		);
	}

	public function test_get_arr_dias_mes()
	{
		$expected = [
			'01' => NULL, '02' => NULL, '03' => NULL, '04' => NULL, '05' => NULL,
			'06' => NULL, '07' => NULL, '08' => NULL, '09' => NULL, 10 => NULL,
			11 => NULL, 12 => NULL, 13 => NULL, 14 => NULL, 15 => NULL,
			16 => NULL, 17 => NULL, 18 => NULL, 19 => NULL, 20 => NULL,
			21 => NULL, 22 => NULL, 23 => NULL, 24 => NULL, 25 => NULL,
			26 => NULL, 27 => NULL, 28 => NULL, 29 => NULL, 30 => NULL,
		];

		$this->assertEquals(get_arr_dias_mes('201704'), $expected);
	}

	public function test_get_fecha_hasta_mismo_anno()
	{
		$this->assertEquals(get_fecha_hasta('201703'), '20170401');
		$this->assertEquals(get_fecha_hasta('201612'), '20170101');
	}

	public function test_dias_de_la_semana()
	{
		$this->assertEquals(dias_de_la_semana(0), 'Do');
		$this->assertEquals(dias_de_la_semana(3), 'Mi');
		$this->assertEquals(dias_de_la_semana(6), 'Sa');
	}

	public function test_clase_cumplimiento_consumos()
	{
		$this->assertEquals(clase_cumplimiento_consumos(.95), 'success');
		$this->assertEquals(clase_cumplimiento_consumos(.75), 'warning');
		$this->assertEquals(clase_cumplimiento_consumos(.45), 'danger');
	}


}
