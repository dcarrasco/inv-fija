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
class test_models_inventario extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_almacen()
	{
		$almacen = new Inventario\almacen;

		$this->assert_is_object($almacen);
		$this->assert_not_empty($almacen->get_fields());
		$this->assert_count($almacen->get_fields(), 1);
		$this->assert_is_string((string) $almacen);
	}

	public function test_auditor()
	{
		$auditor = new Inventario\auditor;

		$this->assert_is_object($auditor);
		$this->assert_not_empty($auditor->get_fields());
		$this->assert_count($auditor->get_fields(), 3);
		$this->assert_is_string((string) $auditor);
	}

	public function test_catalogo()
	{
		$catalogo = new Inventario\Catalogo;

		$this->assert_is_object($catalogo);
		$this->assert_not_empty($catalogo->get_fields());
		$this->assert_count($catalogo->get_fields(), 5);
		$this->assert_is_string((string) $catalogo);
	}

	public function test_centro()
	{
		$centro = new Inventario\centro;

		$this->assert_is_object($centro);
		$this->assert_not_empty($centro->get_fields());
		$this->assert_count($centro->get_fields(), 1);
		$this->assert_is_string((string) $centro);
	}

	public function test_familia()
	{
		$familia = new Inventario\Familia;

		$this->assert_is_object($familia);
		$this->assert_not_empty($familia->get_fields());
		$this->assert_count($familia->get_fields(), 3);
		$this->assert_is_string((string) $familia);
	}

	public function test_inventario_reporte()
	{
		$inventario_reporte = new Inventario\Inventario_reporte;

		$this->assert_is_object($inventario_reporte);
		$this->assert_not_empty($inventario_reporte->get_fields());
		$this->assert_count($inventario_reporte->get_fields(), 4);
		$this->assert_is_string((string) $inventario_reporte);
		$this->assert_not_empty($inventario_reporte->rules_reporte);
		$this->assert_not_empty($inventario_reporte->rules_imprime_inventario());
	}

	public function test_tipo_inventario()
	{
		$tipo_inventario = new Inventario\Tipo_inventario;

		$this->assert_is_object($tipo_inventario);
		$this->assert_not_empty($tipo_inventario->get_fields());
		$this->assert_count($tipo_inventario->get_fields(), 2);
		$this->assert_is_string((string) $tipo_inventario);
	}

	public function test_tipo_ubicacion()
	{
		$tipo_ubicacion = new Inventario\Tipo_ubicacion;

		$this->assert_is_object($tipo_ubicacion);
		$this->assert_not_empty($tipo_ubicacion->get_fields());
		$this->assert_count($tipo_ubicacion->get_fields(), 3);
		$this->assert_is_string((string) $tipo_ubicacion);
	}

	public function test_unidad_medida()
	{
		$unidad_medida = new Inventario\Unidad_medida;

		$this->assert_is_object($unidad_medida);
		$this->assert_not_empty($unidad_medida->get_fields());
		$this->assert_count($unidad_medida->get_fields(), 2);
		$this->assert_is_string((string) $unidad_medida);
	}

}
