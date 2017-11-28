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
class test_reporte extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_get_order_by()
	{
		$expected = 'campo1 ASC, campo2 DESC, campo3 ASC';
		$reporte = new Repo();

		$this->assert_equals($reporte->get_order_by('+campo1, -campo2, campo3'), $expected);
	}

	public function test_result_to_month_table()
	{
		$expected = ['llave' => [
			'01'=>NULL, '02'=>NULL, '03'=>NULL, '04'=>10, '05'=>NULL,
			'06'=>NULL, '07'=>NULL, '08'=>NULL, '09'=>NULL, 10=>NULL,
			11=>NULL, 12=>NULL, 13=>NULL, 14=>NULL, 15=>NULL,
			16=>NULL, 17=>NULL, 18=>NULL, 19=>NULL, 20=>NULL,
			21=>NULL, 22=>NULL, 23=>NULL, 24=>NULL, 25=>NULL,
			26=>NULL, 27=>NULL, 28=>NULL,
		]];
		$reporte = (new Repo())->result_to_month_table([['fecha' => '20170204', 'dato' => 10, 'llave' => 'llave']])->all();

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_texto()
	{
		$expected = 'casa';
		$reporte = (new Repo())->formato_reporte('casa', ['tipo' => 'texo']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_fecha()
	{
		$expected = '2017-10-01';
		$reporte = (new Repo())->formato_reporte('20171001', ['tipo' => 'fecha']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_numero()
	{
		$expected = '12.345';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'numero']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_valor()
	{
		$expected = '$&nbsp;12.345';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'valor']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_valor_pmp()
	{
		$expected = '$&nbsp;12.345';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'valor']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_numero_dif_positivo()
	{
		$expected = 'is_int';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'numero_dif']);

		$this->assert_equals(strpos($reporte, '+12.345'), $expected);
	}

	public function test_formato_reporte_numero_dif_negativo()
	{
		$expected = 'is_int';
		$reporte = (new Repo())->formato_reporte(-12345, ['tipo' => 'numero_dif']);

		$this->assert_equals(strpos($reporte, '-12.345'), $expected);
	}

	public function test_formato_reporte_link()
	{
		$expected = '<a href="http://a/b/c/12345">12345</a>';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'link', 'href' => 'http://a/b/c/']);

		$this->assert_equals($reporte, $expected);
	}

	public function test_formato_reporte_link_registros()
	{
		$expected = '<a href="http://a/b/c/11/22/33">12345</a>';
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'link_registro', 'href' => 'http://a/b/c', 'href_registros' => ['aa', 'bb', 'cc']], ['aa' => '11', 'bb' => '22', 'cc' => '33']);

		$this->assert_equals($reporte, $expected);
	}


}

class Repo {
	use Reporte;
}
