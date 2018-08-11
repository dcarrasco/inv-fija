<?php

use test\test_case;
use Toa\Asignacion\Asignacion;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class asignacion_test extends TestCase {

	public function test_new()
	{
		$this->assertInternalType('object', new Asignacion);
		$this->assertInternalType('object', Asignacion::create());
	}

	public function test_fields()
	{
		$this->assertEmpty(Asignacion::create()->get_fields());
	}

	public function test_rules()
	{
		$this->assertNotEmpty(Asignacion::create()->rules);
		$this->assertCount(3, Asignacion::create()->rules);
	}

	public function test_tipos_reporte_asignaciones()
	{
		$this->assertNotEmpty(Asignacion::create()->tipos_reporte_asignaciones);
		$this->assertCount(5, Asignacion::create()->tipos_reporte_asignaciones);
	}

	public function test_movimientos_asignaciones()
	{
		$this->assertNotEmpty(Asignacion::create()->movimientos_asignaciones);
		$this->assertCount(2, Asignacion::create()->movimientos_asignaciones);
	}

	public function test_centros_asignacion()
	{
		$this->assertNotEmpty(Asignacion::create()->centros_asignacion);
		$this->assertCount(2, Asignacion::create()->centros_asignacion);
	}

	public function test_combo_unidades_asignacion()
	{
		$this->assertNotEmpty(Asignacion::create()->combo_unidades_asignacion);
		$this->assertCount(3, Asignacion::create()->combo_unidades_asignacion);
	}

	// public function test_select_fields()
	// {
	// 	$this->assertNotEmpty($this->get_property('select_fields'));
	// 	$this->assertCount(5, $this->get_property('select_fields'));
	// }

	// public function test_group_by_fields()
	// {
	// 	$this->assertNotEmpty($this->get_property('group_by_fields'));
	// 	$this->assertCount(5, $this->get_property('group_by_fields'));
	// }

	// public function test_order_fields()
	// {
	// 	$this->assertNotEmpty($this->get_property('order_fields'));
	// 	$this->assertCount(5, $this->get_property('order_fields'));
	// }

	// public function test_configuracion_campos()
	// {
	// 	$this->assertNotEmpty($this->get_property('configuracion_campos'));
	// 	$this->assertCount(18, $this->get_property('configuracion_campos'));
	// }

	// public function test_config_campos_reportes()
	// {
	// 	$this->assertNotEmpty($this->get_property('config_campos_reportes'));
	// 	$this->assertCount(5, $this->get_property('config_campos_reportes'));
	// }

	public function test_asignaciones_toa()
	{
		$asignacion = new Asignacion;
		$this->assertEmpty($asignacion->reporte());
		$this->assertEmpty($asignacion->reporte('tecnicos'));
		$this->assertEmpty($asignacion->reporte('tecnicos', '201701'));
		$this->assertEmpty($asignacion->reporte('tecnicos', '201701'));

		$this->assertEmpty($asignacion->listado());

		$this->assertEmpty($asignacion->detalle());
		$this->assertEmpty($asignacion->detalle('20170101'));

		// $asignacion = Asignacion::create();
		// app('db')->mock_set_return_result([
		// 	['fecha'=>'20170101', 'llave'=>'123456', 'dato'=>'12'],
		// 	['fecha'=>'20170102', 'llave'=>'123456', 'dato'=>'12'],
		// ]);

		// $this->assertEmpty($asignacion->control_asignaciones()->all());
		// $this->assertEmpty($asignacion->control_asignaciones(collect(['mes'=>'201701']))->all());
	}

}
