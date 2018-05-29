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
class stock_models_test extends TestCase {

	public function test_clase_movimiento()
	{
		$clase_movimiento = new Stock\Clase_movimiento;

		$this->assertInternalType('object', $clase_movimiento);
		$this->assertNotEmpty($clase_movimiento->get_fields());
		$this->assertCount(2, $clase_movimiento->get_fields());
		$this->assertInternalType('string', (string) $clase_movimiento);
	}

	public function test_clasifalmacen_sap()
	{
		$clasifalmacen_sap = new Stock\Clasifalmacen_sap;

		$this->assertInternalType('object', $clasifalmacen_sap);
		$this->assertNotEmpty($clasifalmacen_sap->get_fields());
		$this->assertCount(8, $clasifalmacen_sap->get_fields());
		$this->assertInternalType('string', (string) $clasifalmacen_sap);
	}

	public function test_log_gestor()
	{
		$log_gestor = new Stock\Log_gestor;

		$this->assertInternalType('object', $log_gestor);
		$this->assertEmpty($log_gestor->get_fields());
	}

	public function test_proveedor()
	{
		$proveedor = new Stock\Proveedor;

		$this->assertInternalType('object', $proveedor);
		$this->assertNotEmpty($proveedor->get_fields());
		$this->assertCount(2, $proveedor->get_fields());
		$this->assertInternalType('string', (string) $proveedor);
	}

	public function test_reporte_stock()
	{
		$reporte_stock = new Stock\Reporte_stock;

		$this->assertInternalType('object', $reporte_stock);
		$this->assertEmpty($reporte_stock->get_fields());
	}

	public function test_stock_sap()
	{
		$stock_sap = new Stock\Stock_sap;

		$this->assertInternalType('object', $stock_sap);
		$this->assertEmpty($stock_sap->get_fields());
	}

	public function test_stock_sap_fija()
	{
		$stock_sap_fija = new Stock\Stock_sap_fija;

		$this->assertInternalType('object', $stock_sap_fija);
		$this->assertEmpty($stock_sap_fija->get_fields());
	}

	public function test_stock_sap_movil()
	{
		$stock_sap_movil = new Stock\Stock_sap_movil;

		$this->assertInternalType('object', $stock_sap_movil);
		$this->assertEmpty($stock_sap_movil->get_fields());
	}

	public function test_tipo_clasifalm()
	{
		$tipo_clasifalm = new Stock\Tipo_clasifalm;

		$this->assertInternalType('object', $tipo_clasifalm);
		$this->assertNotEmpty($tipo_clasifalm->get_fields());
		$this->assertCount(3, $tipo_clasifalm->get_fields());
		$this->assertInternalType('string', (string) $tipo_clasifalm);
	}

	public function test_tipo_movimiento()
	{
		$tipo_movimiento = new Stock\Tipo_movimiento;

		$this->assertInternalType('object', $tipo_movimiento);
		$this->assertNotEmpty($tipo_movimiento->get_fields());
		$this->assertCount(2, $tipo_movimiento->get_fields());
		$this->assertInternalType('string', (string) $tipo_movimiento);
	}

	public function test_tipo_movimiento_cmv()
	{
		$tipo_movimiento_cmv = new Stock\Tipo_movimiento_cmv;

		$this->assertInternalType('object', $tipo_movimiento_cmv);
		$this->assertNotEmpty($tipo_movimiento_cmv->get_fields());
		$this->assertCount(3, $tipo_movimiento_cmv->get_fields());
		$this->assertInternalType('string', (string) $tipo_movimiento_cmv);
	}

	public function test_tipoalmacen_sap()
	{
		$tipoalmacen_sap = new Stock\Tipoalmacen_sap;

		$this->assertInternalType('object', $tipoalmacen_sap);
		$this->assertNotEmpty($tipoalmacen_sap->get_fields());
		$this->assertCount(5, $tipoalmacen_sap->get_fields());
		$this->assertInternalType('string', (string) $tipoalmacen_sap);
	}

	public function test_usuario_sap()
	{
		$usuario_sap = new Stock\Usuario_sap;

		$this->assertInternalType('object', $usuario_sap);
		$this->assertNotEmpty($usuario_sap->get_fields());
		$this->assertCount(2, $usuario_sap->get_fields());
		$this->assertInternalType('string', (string) $usuario_sap);
	}

}
