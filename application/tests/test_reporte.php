<?php

use test\test_case;

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
		$this->assert_equals(
			(new Repo())->get_order_by('+campo1, -campo2, campo3'),
			'campo1 ASC, campo2 DESC, campo3 ASC'
		);

		$this->assert_equals((new Repo())->get_order_by('+campo1'), 'campo1 ASC');
		$this->assert_equals((new Repo())->get_order_by('-campo1'), 'campo1 DESC');
		$this->assert_equals((new Repo())->get_order_by('campo1'), 'campo1 ASC');
		$this->assert_equals((new Repo())->get_order_by(''), ' ASC');
	}

	public function test_result_to_month_table()
	{
		$expected = ['llave' => [
			'01'=>NULL,'02'=>NULL,'03'=>NULL,'04'=>10,'05'=>NULL,
			'06'=>20,'07'=>NULL,'08'=>NULL,'09'=>NULL,10=>NULL,
			11=>NULL,12=>NULL,13=>NULL,14=>NULL,15=>NULL,16=>30,17=>NULL,18=>NULL,19=>NULL,20=>NULL,
			21=>NULL,22=>NULL,23=>NULL,24=>NULL,25=>NULL,26=>NULL,27=>NULL,28=>NULL,
		]];

		$this->assert_equals(
			(new Repo())->result_to_month_table([
				['fecha' => '20170204', 'dato' => 10, 'llave' => 'llave'],
				['fecha' => '20170206', 'dato' => 20, 'llave' => 'llave'],
				['fecha' => '20170216', 'dato' => 30, 'llave' => 'llave'],
			])->all(),
			$expected
		);

		$this->assert_equals(
			(new Repo())->result_to_month_table([['campo1' => '20170204', 'campo2' => 10, 'campo3' => 'llave']])->all(),
			[''=>[''=>null]]
		);

	}

	public function test_formato_reporte_texto()
	{
		$reporte = (new Repo())->formato_reporte('casa', ['tipo' => 'texto']);

		$this->assert_equals($reporte, 'casa');
	}

	public function test_formato_reporte_fecha()
	{
		$reporte = (new Repo())->formato_reporte('20171001', ['tipo' => 'fecha']);

		$this->assert_equals($reporte, '2017-10-01');
	}

	public function test_formato_reporte_numero()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'numero']);

		$this->assert_equals($reporte, '12.345');
	}

	public function test_formato_reporte_valor()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'valor']);

		$this->assert_equals($reporte, '$&nbsp;12.345');
	}

	public function test_formato_reporte_valor_pmp()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'valor']);

		$this->assert_equals($reporte, '$&nbsp;12.345');
	}

	public function test_formato_reporte_numero_dif()
	{
		$this->assert_contains((new Repo())->formato_reporte(12345, ['tipo' => 'numero_dif']), '+12.345');
		$this->assert_contains((new Repo())->formato_reporte(-12345, ['tipo' => 'numero_dif']), '-12.345');
	}

	public function test_formato_reporte_link()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'link', 'href' => 'http://a/b/c/']);

		$this->assert_equals($reporte, '<a href="http://a/b/c/12345">12345</a>');
	}

	public function test_formato_reporte_link_registros()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo' => 'link_registro', 'href' => 'http://a/b/c', 'href_registros' => ['aa', 'bb', 'cc']], ['aa' => '11', 'bb' => '22', 'cc' => '33']);

		$this->assert_equals($reporte, '<a href="http://a/b/c/11/22/33">12345</a>');
	}

	public function test_formato_reporte_link_detalle_series()
	{
		$reporte = (new Repo())->formato_reporte(12345, ['tipo'=>'link_detalle_series', 'href'=>'http://a/b/c/'], ['centro'=>'CM11', 'almacen'=>'CH01', 'lote'=>'NUEVO', 'otro'=>'xx'], 'aa');

		$this->assert_equals($reporte, '<a href="http://a/b/c/?centro=CM11&almacen=CH01&lote=NUEVO&permanencia=aa">12.345</a>');
	}

	public function test_formato_reporte_otro()
	{
		$reporte = (new Repo())->formato_reporte('casa', ['tipo' => 'otrootro']);

		$this->assert_equals($reporte, 'casa');
	}

	public function test_formato_reporte_doi()
	{
		$this->assert_equals(
			(new Repo())->formato_reporte(NULL, ['tipo' => 'doi']),
			' <i class="fa fa-circle text-danger"></i>'
		);

		$this->assert_equals(
			(new Repo())->formato_reporte(2.3, ['tipo' => 'doi']),
			'2,3 <i class="fa fa-circle text-success"></i>'
		);

		$this->assert_equals(
			(new Repo())->formato_reporte(20, ['tipo' => 'doi']),
			'20 <i class="fa fa-circle text-success"></i>'
		);

		$this->assert_equals(
			(new Repo())->formato_reporte(70, ['tipo' => 'doi']),
			'70 <i class="fa fa-circle text-warning"></i>'
		);

		$this->assert_equals(
			(new Repo())->formato_reporte(170, ['tipo' => 'doi']),
			'170 <i class="fa fa-circle text-danger"></i>'
		);
	}

	public function test_genera_reporte()
	{
		$repo = new Repo();
		$reporte = collect(explode("\n", $repo->get_reporte()->genera_reporte()));

		$this->assert_count($reporte->filter(function($linea) {return substr($linea, 0, 22)==='<td class="text-muted"';})->all(), 3);
		$this->assert_count($reporte->filter(function($linea) {return substr($linea, 0, 6)==='<table';})->all(), 1);
		$this->assert_count($reporte->filter(function($linea) {return substr($linea, 0, 6)==='<thead';})->all(), 1);
		$this->assert_count($reporte->filter(function($linea) {return substr($linea, 0, 3)==='<tr';})->all(), 3+2);

		$this->assert_not_empty(collect($repo->campos)->pluck('sort')->implode());
		$this->assert_not_empty(collect($repo->campos)->pluck('img_orden')->implode());
	}


}

class Repo {
	use Reporte;

	public $campos;

	public function get_campos_reporte()
	{
		return [
			'campo1' => ['titulo' => 'titulo_campo1'],
			'valor1' => ['titulo' => 'titulo_valor1', 'tipo' => 'numero'],
			'valor2' => ['titulo' => 'titulo_valor2', 'tipo' => 'numero'],
		];
	}

	public function get_datos_reporte()
	{
		return [
			['campo1' => 'valor_campo1', 'valor1' => 100, 'valor2' => 200],
			['campo1' => 'valor_campo2', 'valor1' => 300, 'valor2' => 400],
			['campo1' => 'valor_campo3', 'valor1' => 500, 'valor2' => 600],
		];
	}

	public function get_reporte()
	{
		$this->datos_reporte = $this->get_datos_reporte();
		$this->campos_reporte = $this->get_campos_reporte();

		$this->campos = $this->set_order_campos($this->campos_reporte, 'campo1');

		return $this;
	}
}
