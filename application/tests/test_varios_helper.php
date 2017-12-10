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
class test_varios_helper extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_lang()
	{
		get_instance()->lang->load('acl_lang');

		$this->assert_equals(lang('acl_config_menu_usuarios'), 'Usuarios');
	}

	public function test_print_message()
	{
		$this->assert_is_string(print_message('prueba'));
	}

	public function test_form_array_format()
	{
		$test = [
			['llave' => 'a', 'valor' => '1', 'otro' => 'otro'],
			['llave' => 'b', 'valor' => '2', 'otro' => 'otro'],
			['llave' => 'c', 'valor' => '3', 'otro' => 'otro'],
		];

		$this->assert_equals(form_array_format($test), ['a'=>'1', 'b'=>'2', 'c'=>'3']);
		$this->assert_equals(form_array_format($test, 'mensaje ini'), [''=>'mensaje ini', 'a'=>'1','b' =>'2', 'c'=>'3']);
	}

	public function test_fmt_cantidad()
	{
		$this->assert_equals(fmt_cantidad(500), '500');
		$this->assert_equals(fmt_cantidad(5000), '5.000');
		$this->assert_equals(fmt_cantidad(5000.428, 2), '5.000,43');
		$this->assert_equals(fmt_cantidad(0, 0, TRUE), '0');
		$this->assert_equals(fmt_cantidad(0, 0, FALSE), '');
	}

	public function test_fmt_monto()
	{
		$this->assert_equals(fmt_monto(500), '$&nbsp;500');
		$this->assert_equals(fmt_monto(5000), '$&nbsp;5.000');
		$this->assert_equals(fmt_monto(5000.428, 'UN', '$', 2), '$&nbsp;5.000,43');
		$this->assert_equals(fmt_monto(0, 'UN', '$', 0, TRUE), '$&nbsp;0');
		$this->assert_equals(fmt_monto(0, 'UN', '$', 0, FALSE), '');
		$this->assert_equals(fmt_monto(1222, 'UN', 'CLP'), 'CLP&nbsp;1.222');
		$this->assert_equals(fmt_monto(222123123, 'MM'), 'MM$&nbsp;222');
	}

	public function test_fmt_hora()
	{
		$this->assert_equals(fmt_hora(33), '00:00:33');
		$this->assert_equals(fmt_hora(133), '00:02:13');
		$this->assert_equals(fmt_hora(3733), '01:02:13');
	}

	public function test_fmt_fecha()
	{
		$this->assert_equals(fmt_fecha('20171011'), '2017-10-11');
		$this->assert_equals(fmt_fecha('20171011', 'Y---m---d'), '2017---10---11');
	}

	public function test_fmt_fecha_db()
	{
		$this->assert_equals(fmt_fecha_db('2017-10-11'), '20171011');
		$this->assert_null(fmt_fecha_db());
	}

	public function test_fmt_rut()
	{
		$this->assert_equals(fmt_rut('138889998'), '13.888.999-8');

	}

	public function test_array_get()
	{
		$this->assert_equals(array_get([1,2,3,4,5], 2), 3);
		$this->assert_null(array_get([1,2,3,4,5], 10));
		$this->assert_equals(array_get([1,2,3,4,5], 10, 100), 100);
		$this->assert_equals(
			array_get(['uno' => ['dos' => ['tres' => 3, 'cuatro' => [1,2,3,4,5]]]], 'uno.dos.tres'),
			3
		);
	}

	public function test_get_arr_dias_mes()
	{
		$expected = [
			'01' => NULL, '02' => NULL, '03' => NULL, '04' => NULL, '05' => NULL,
			'06' => NULL, '07' => NULL, '08' => NULL, '09' => NULL, 10  => NULL,
			11   => NULL, 12   => NULL, 13   => NULL, 14   => NULL, 15   => NULL,
			16   => NULL, 17   => NULL, 18   => NULL, 19   => NULL, 20   => NULL,
			21   => NULL, 22   => NULL, 23   => NULL, 24   => NULL, 25   => NULL,
			26   => NULL, 27   => NULL, 28   => NULL, 29   => NULL, 30   => NULL,
		];

		$this->assert_equals(get_arr_dias_mes('201704'), $expected);
	}

	public function test_get_fecha_hasta_mismo_anno()
	{
		$this->assert_equals(get_fecha_hasta('201703'), '20170401');
		$this->assert_equals(get_fecha_hasta('201612'), '20170101');
	}

	public function test_dias_de_la_semana()
	{
		$this->assert_equals(dias_de_la_semana(3), 'Mi');
	}

	public function test_clase_cumplimiento_consumos()
	{
		$this->assert_equals(clase_cumplimiento_consumos(.95), 'success');
		$this->assert_equals(clase_cumplimiento_consumos(.75), 'warning');
		$this->assert_equals(clase_cumplimiento_consumos(.45), 'danger');
	}


}
