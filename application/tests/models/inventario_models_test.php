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
class inventario_models_test extends TestCase {

	public function test_almacen()
	{
		$almacen = new Inventario\almacen;

		$this->assertInternalType('object', $almacen);
		$this->assertNotEmpty($almacen->get_fields());
		$this->assertCount(1, $almacen->get_fields());
		$this->assertInternalType('string', (string) $almacen);
	}

	public function test_auditor()
	{
		$auditor = new Inventario\auditor;

		$this->assertInternalType('object', $auditor);
		$this->assertNotEmpty($auditor->get_fields());
		$this->assertCount(3, $auditor->get_fields());
		$this->assertInternalType('string', (string) $auditor);
	}

	public function test_catalogo()
	{
		$catalogo = new Inventario\Catalogo;

		$this->assertInternalType('object', $catalogo);
		$this->assertNotEmpty($catalogo->get_fields());
		$this->assertCount(5, $catalogo->get_fields());
		$this->assertInternalType('string', (string) $catalogo);
	}

	public function test_centro()
	{
		$centro = new Inventario\centro;

		$this->assertInternalType('object', $centro);
		$this->assertNotEmpty($centro->get_fields());
		$this->assertCount(1, $centro->get_fields());
		$this->assertInternalType('string', (string) $centro);
	}

	public function test_familia()
	{
		$familia = new Inventario\Familia;

		$this->assertInternalType('object', $familia);
		$this->assertNotEmpty($familia->get_fields());
		$this->assertCount(3, $familia->get_fields());
		$this->assertInternalType('string', (string) $familia);
	}

	public function test_inventario_reporte()
	{
		$inventario_reporte = new Inventario\Inventario_reporte;

		$this->assertInternalType('object', $inventario_reporte);
		$this->assertNotEmpty($inventario_reporte->get_fields());
		$this->assertCount(4, $inventario_reporte->get_fields());
		$this->assertInternalType('string', (string) $inventario_reporte);
		$this->assertNotEmpty($inventario_reporte->rules_reporte);
		$this->assertNotEmpty($inventario_reporte->rules_imprime_inventario());
	}

	public function test_tipo_inventario()
	{
		$tipo_inventario = new Inventario\Tipo_inventario;

		$this->assertInternalType('object', $tipo_inventario);
		$this->assertNotEmpty($tipo_inventario->get_fields());
		$this->assertCount(2, $tipo_inventario->get_fields());
		$this->assertInternalType('string', (string) $tipo_inventario);
	}

	public function test_tipo_ubicacion()
	{
		$tipo_ubicacion = new Inventario\Tipo_ubicacion;

		$this->assertInternalType('object', $tipo_ubicacion);
		$this->assertNotEmpty($tipo_ubicacion->get_fields());
		$this->assertCount(3, $tipo_ubicacion->get_fields());
		$this->assertInternalType('string', (string) $tipo_ubicacion);
	}

	public function test_unidad_medida()
	{
		$unidad_medida = new Inventario\Unidad_medida;

		$this->assertInternalType('object', $unidad_medida);
		$this->assertNotEmpty($unidad_medida->get_fields());
		$this->assertCount(2, $unidad_medida->get_fields());
		$this->assertInternalType('string', (string) $unidad_medida);
	}

}
