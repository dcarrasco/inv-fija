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

	public function test_varios_lang()
	{
		get_instance()->lang->load('acl_lang');
		$expected = 'Usuarios';

		$this->test(lang('acl_config_menu_usuarios'), $expected);
	}

	public function test_varios_print_message()
	{
		$this->test(print_message('prueba'), 'is_string');
	}

	public function test_varios_form_array_format()
	{
		$expected = ['a' => '1', 'b' => '2', 'c' => '3'];

		$test = [
			['llave' => 'a', 'valor' => '1', 'otro' => 'otro'],
			['llave' => 'b', 'valor' => '2', 'otro' => 'otro'],
			['llave' => 'c', 'valor' => '3', 'otro' => 'otro'],
		];
		$this->test(form_array_format($test), $expected);
	}

	public function test_varios_form_array_format_con_mensaje_ini()
	{
		$expected = ['' => 'mensaje ini', 'a' => '1', 'b' => '2', 'c' => '3'];

		$test = [
			['llave' => 'a', 'valor' => '1', 'otro' => 'otro'],
			['llave' => 'b', 'valor' => '2', 'otro' => 'otro'],
			['llave' => 'c', 'valor' => '3', 'otro' => 'otro'],
		];
		$this->test(form_array_format($test, 'mensaje ini'), $expected);
	}

	public function test_varios_fmt_cantidad_menor_a_1000()
	{
		$expected = '500';

		$this->test(fmt_cantidad(500), $expected);
	}

	public function test_varios_fmt_cantidad_mayor_a_1000()
	{
		$expected = '5.000';

		$this->test(fmt_cantidad(5000), $expected);
	}

	public function test_varios_fmt_cantidad_mayor_a_1000_con_decimales()
	{
		$expected = '5.000,43';

		$this->test(fmt_cantidad(5000.428, 2), $expected);
	}

	public function test_varios_fmt_cantidad_mostrar_cero()
	{
		$expected = '0';

		$this->test(fmt_cantidad(0, 0, TRUE), $expected);
	}

	public function test_varios_fmt_cantidad_no_mostrar_cero()
	{
		$expected = '';

		$this->test(fmt_cantidad(0, 0, FALSE), $expected);
	}

	public function test_varios_fmt_monto_menor_a_1000()
	{
		$expected = '$&nbsp;500';

		$this->test(fmt_monto(500), $expected);
	}

	public function test_varios_fmt_monto_mayor_a_1000()
	{
		$expected = '$&nbsp;5.000';

		$this->test(fmt_monto(5000), $expected);
	}

	public function test_varios_fmt_monto_mayor_a_1000_con_decimales()
	{
		$expected = '$&nbsp;5.000,43';

		$this->test(fmt_monto(5000.428, 'UN', '$', 2), $expected);
	}

	public function test_varios_fmt_monto_mostrar_cero()
	{
		$expected = '$&nbsp;0';

		$this->test(fmt_monto(0, 'UN', '$', 0, TRUE), $expected);
	}

	public function test_varios_fmt_monto_no_mostrar_cero()
	{
		$expected = '';

		$this->test(fmt_monto(0, 'UN', '$', 0, FALSE), $expected);
	}

	public function test_varios_fmt_monto_cambia_signo_moneda()
	{
		$expected = 'CLP&nbsp;1.222';

		$this->test(fmt_monto(1222, 'UN', 'CLP'), $expected);
	}

	public function test_varios_fmt_monto_despliega_millones()
	{
		$expected = 'MM$&nbsp;222';

		$this->test(fmt_monto(222123123, 'MM'), $expected);
	}

	public function test_varios_fmt_hora_segundos()
	{
		$expected = '00:00:33';

		$this->test(fmt_hora(33), $expected);
	}

	public function test_varios_fmt_hora_minutos()
	{
		$expected = '00:02:13';

		$this->test(fmt_hora(133), $expected);
	}

	public function test_varios_fmt_hora_horas()
	{
		$expected = '01:02:13';

		$this->test(fmt_hora(3733), $expected);
	}

	public function test_varios_fmt_fecha()
	{
		$expected = '2017-10-11';

		$this->test(fmt_fecha('20171011'), $expected);
	}

	public function test_varios_fmt_fecha_nuevo_formato()
	{
		$expected = '2017---10---11';

		$this->test(fmt_fecha('20171011', 'Y---m---d'), $expected);
	}

	public function test_varios_fmt_fecha_db()
	{
		$expected = '20171011';

		$this->test(fmt_fecha_db('2017-10-11'), $expected);
	}

	public function test_varios_fmt_fecha_db_nulo()
	{
		$expected = NULL;

		$this->test(fmt_fecha_db(), $expected);
	}

	public function test_varios_fmt_rut()
	{
		$expected = '13.888.999-8';

		$this->test(fmt_rut('138889998'), $expected);
	}

	public function test_varios_array_get_encontrado()
	{
		$expected = 3;
		$array = [1,2,3,4,5];

		$this->test(array_get($array, 2), $expected);
	}

	public function test_varios_array_get_no_encontrado()
	{
		$expected = NULL;
		$array = [1,2,3,4,5];

		$this->test(array_get($array, 10), $expected);
	}

	public function test_varios_array_get_default()
	{
		$expected = 100;
		$array = [1,2,3,4,5];

		$this->test(array_get($array, 10, 100), $expected);
	}

	public function test_varios_array_get_dot_notation()
	{
		$expected = 3;
		$array = [
			'uno' => [
				'dos' => [
					'tres' => 3,
					'cuatro' => [1,2,3,4,5],
				],
			],
		];

		$this->test(array_get($array, 'uno.dos.tres'), $expected);
	}

	public function test_varios_get_arr_dias_mes()
	{
		$expected = [
			'01' => NULL, '02' => NULL, '03' => NULL, '04' => NULL, '05' => NULL,
			'06' => NULL, '07' => NULL, '08' => NULL, '09' => NULL, 10  => NULL,
			11   => NULL, 12   => NULL, 13   => NULL, 14   => NULL, 15   => NULL,
			16   => NULL, 17   => NULL, 18   => NULL, 19   => NULL, 20   => NULL,
			21   => NULL, 22   => NULL, 23   => NULL, 24   => NULL, 25   => NULL,
			26   => NULL, 27   => NULL, 28   => NULL, 29   => NULL, 30   => NULL,
		];

		$this->test(get_arr_dias_mes('201704'), $expected);
	}

	public function test_varios_get_fecha_hasta_mismo_anno()
	{
		$expected = '20170401';

		$this->test(get_fecha_hasta('201703'), $expected);
	}

	public function test_varios_get_fecha_hasta_cambia_anno()
	{
		$expected = '20170101';

		$this->test(get_fecha_hasta('201612'), $expected);
	}

	public function test_varios_dias_de_la_semana()
	{
		$expected = 'Mi';

		$this->test(dias_de_la_semana(3), $expected);
	}

	public function test_varios_clase_cumplimiento_consumos_alto()
	{
		$expected = 'success';

		$this->test(clase_cumplimiento_consumos(.95), $expected);
	}

	public function test_varios_clase_cumplimiento_consumos_medio()
	{
		$expected = 'warning';

		$this->test(clase_cumplimiento_consumos(.75), $expected);
	}

	public function test_varios_clase_cumplimiento_consumos_bajo()
	{
		$expected = 'danger';

		$this->test(clase_cumplimiento_consumos(.45), $expected);
	}



}
