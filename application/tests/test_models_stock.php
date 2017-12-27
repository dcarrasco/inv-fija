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
class test_models_stock extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_almacen_sap()
	{
		$almacen_sap = new Stock\Almacen_sap;

		$this->assert_is_object($almacen_sap);
		$this->assert_not_empty($almacen_sap->get_fields());
		$this->assert_count($almacen_sap->get_fields(), 7);
		$this->assert_is_string((string) $almacen_sap);
	}

	public function test_analisis_series()
	{
		$analisis_series = new Stock\Analisis_series;

		$this->assert_is_object($analisis_series);
		$this->assert_empty($analisis_series->get_fields());
		$this->assert_not_empty($analisis_series->rules);
	}

	public function test_clase_movimiento()
	{
		$clase_movimiento = new Stock\Clase_movimiento;

		$this->assert_is_object($clase_movimiento);
		$this->assert_not_empty($clase_movimiento->get_fields());
		$this->assert_count($clase_movimiento->get_fields(), 2);
		$this->assert_is_string((string) $clase_movimiento);
	}

	public function test_clasifalmacen_sap()
	{
		$clasifalmacen_sap = new Stock\Clasifalmacen_sap;

		$this->assert_is_object($clasifalmacen_sap);
		$this->assert_not_empty($clasifalmacen_sap->get_fields());
		$this->assert_count($clasifalmacen_sap->get_fields(), 8);
		$this->assert_is_string((string) $clasifalmacen_sap);
	}

	public function test_log_gestor()
	{
		$log_gestor = new Stock\Log_gestor;

		$this->assert_is_object($log_gestor);
		$this->assert_empty($log_gestor->get_fields());
	}

	public function test_proveedor()
	{
		$proveedor = new Stock\Proveedor;

		$this->assert_is_object($proveedor);
		$this->assert_not_empty($proveedor->get_fields());
		$this->assert_count($proveedor->get_fields(), 2);
		$this->assert_is_string((string) $proveedor);
	}

	public function test_reporte_stock()
	{
		$reporte_stock = new Stock\Reporte_stock;

		$this->assert_is_object($reporte_stock);
		$this->assert_empty($reporte_stock->get_fields());
	}

	public function test_stock_sap()
	{
		$stock_sap = new Stock\Stock_sap;

		$this->assert_is_object($stock_sap);
		$this->assert_empty($stock_sap->get_fields());
	}

	public function test_stock_sap_fija()
	{
		$stock_sap_fija = new Stock\Stock_sap_fija;

		$this->assert_is_object($stock_sap_fija);
		$this->assert_empty($stock_sap_fija->get_fields());
	}

	public function test_stock_sap_movil()
	{
		$stock_sap_movil = new Stock\Stock_sap_movil;

		$this->assert_is_object($stock_sap_movil);
		$this->assert_empty($stock_sap_movil->get_fields());
	}

	public function test_tipo_clasifalm()
	{
		$tipo_clasifalm = new Stock\Tipo_clasifalm;

		$this->assert_is_object($tipo_clasifalm);
		$this->assert_not_empty($tipo_clasifalm->get_fields());
		$this->assert_count($tipo_clasifalm->get_fields(), 3);
		$this->assert_is_string((string) $tipo_clasifalm);
	}

	public function test_tipo_movimiento()
	{
		$tipo_movimiento = new Stock\Tipo_movimiento;

		$this->assert_is_object($tipo_movimiento);
		$this->assert_not_empty($tipo_movimiento->get_fields());
		$this->assert_count($tipo_movimiento->get_fields(), 2);
		$this->assert_is_string((string) $tipo_movimiento);
	}

	public function test_tipo_movimiento_cmv()
	{
		$tipo_movimiento_cmv = new Stock\Tipo_movimiento_cmv;

		$this->assert_is_object($tipo_movimiento_cmv);
		$this->assert_not_empty($tipo_movimiento_cmv->get_fields());
		$this->assert_count($tipo_movimiento_cmv->get_fields(), 3);
		$this->assert_is_string((string) $tipo_movimiento_cmv);
	}

	public function test_tipoalmacen_sap()
	{
		$tipoalmacen_sap = new Stock\Tipoalmacen_sap;

		$this->assert_is_object($tipoalmacen_sap);
		$this->assert_not_empty($tipoalmacen_sap->get_fields());
		$this->assert_count($tipoalmacen_sap->get_fields(), 5);
		$this->assert_is_string((string) $tipoalmacen_sap);
	}

	public function test_usuario_sap()
	{
		$usuario_sap = new Stock\Usuario_sap;

		$this->assert_is_object($usuario_sap);
		$this->assert_not_empty($usuario_sap->get_fields());
		$this->assert_count($usuario_sap->get_fields(), 2);
		$this->assert_is_string((string) $usuario_sap);
	}

}
