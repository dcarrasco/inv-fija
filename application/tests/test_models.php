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
class test_models extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_models_acl()
	{
		$this->assert_is_object(new Acl\Acl);
		$this->assert_true(empty(Acl\Acl::create()->get_fields()));

		$this->assert_is_object(new Acl\app);
		$this->assert_false(empty(Acl\app::create()->get_fields()));

		$this->assert_is_object(new Acl\modulo);
		$this->assert_false(empty(Acl\modulo::create()->get_fields()));

		$this->assert_is_object(new Acl\rol);
		$this->assert_false(empty(Acl\rol::create()->get_fields()));

		$this->assert_is_object(new Acl\usuario);
		$this->assert_false(empty(Acl\usuario::create()->get_fields()));
	}

	public function test_models_inventario()
	{
		$this->assert_is_object(new Inventario\almacen);
		$this->assert_false(empty(Inventario\almacen::create()->get_fields()));

		$this->assert_is_object(new Inventario\auditor);
		$this->assert_false(empty(Inventario\auditor::create()->get_fields()));

		$this->assert_is_object(new Inventario\catalogo);
		$this->assert_false(empty(Inventario\catalogo::create()->get_fields()));

		$this->assert_is_object(new Inventario\centro);
		$this->assert_false(empty(Inventario\centro::create()->get_fields()));

		$this->assert_is_object(new Inventario\detalle_inventario);
		$this->assert_false(empty(Inventario\detalle_inventario::create()->get_fields()));

		$this->assert_is_object(new Inventario\familia);
		$this->assert_false(empty(Inventario\familia::create()->get_fields()));

		$this->assert_is_object(new Inventario\inventario);
		$this->assert_false(empty(Inventario\inventario::create()->get_fields()));

		$this->assert_is_object(new Inventario\Inventario_reporte);
		$this->assert_false(empty(Inventario\Inventario_reporte::create()->get_fields()));

		$this->assert_is_object(new Inventario\tipo_inventario);
		$this->assert_false(empty(Inventario\tipo_inventario::create()->get_fields()));

		$this->assert_is_object(new Inventario\tipo_ubicacion);
		$this->assert_false(empty(Inventario\tipo_ubicacion::create()->get_fields()));

		$this->assert_is_object(new Inventario\ubicacion);
		$this->assert_false(empty(Inventario\ubicacion::create()->get_fields()));

		$this->assert_is_object(new Inventario\unidad_medida);
		$this->assert_false(empty(Inventario\unidad_medida::create()->get_fields()));
	}

	public function test_models_stock()
	{
		$this->assert_is_object(new Stock\almacen_sap);
		$this->assert_false(empty(Stock\almacen_sap::create()->get_fields()));

		$this->assert_is_object(new Stock\Analisis_series);
		$this->assert_true(empty(Stock\Analisis_series::create()->get_fields()));

		$this->assert_is_object(new Stock\Clase_movimiento);
		$this->assert_false(empty(Stock\Clase_movimiento::create()->get_fields()));

		$this->assert_is_object(new Stock\clasifalmacen_sap);
		$this->assert_false(empty(Stock\clasifalmacen_sap::create()->get_fields()));

		$this->assert_is_object(new Stock\Log_gestor);
		$this->assert_true(empty(Stock\Log_gestor::create()->get_fields()));

		$this->assert_is_object(new Stock\proveedor);
		$this->assert_false(empty(Stock\proveedor::create()->get_fields()));

		$this->assert_is_object(new Stock\Reporte_stock);
		$this->assert_true(empty(Stock\Reporte_stock::create()->get_fields()));

		$this->assert_is_object(new Stock\stock_sap);
		$this->assert_true(empty(Stock\stock_sap::create()->get_fields()));

		$this->assert_is_object(new Stock\stock_sap_fija);
		$this->assert_true(empty(Stock\stock_sap_fija::create()->get_fields()));

		$this->assert_is_object(new Stock\stock_sap_movil);
		$this->assert_true(empty(Stock\stock_sap_movil::create()->get_fields()));

		$this->assert_is_object(new Stock\tipo_clasifalm);
		$this->assert_false(empty(Stock\tipo_clasifalm::create()->get_fields()));

		$this->assert_is_object(new Stock\Tipo_movimiento);
		$this->assert_false(empty(Stock\Tipo_movimiento::create()->get_fields()));

		$this->assert_is_object(new Stock\Tipo_movimiento_cmv);
		$this->assert_false(empty(Stock\Tipo_movimiento_cmv::create()->get_fields()));

		$this->assert_is_object(new Stock\tipoalmacen_sap);
		$this->assert_false(empty(Stock\tipoalmacen_sap::create()->get_fields()));

		$this->assert_is_object(new Stock\usuario_sap);
		$this->assert_false(empty(Stock\usuario_sap::create()->get_fields()));
	}

	public function test_models_toa()
	{
		$this->assert_is_object(new Toa\Asignacion_toa);
		$this->assert_true(empty(Toa\Asignacion_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Ciudad_toa);
		$this->assert_false(empty(Toa\Ciudad_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Clave_cierre);
		$this->assert_false(empty(Toa\Clave_cierre::create()->get_fields()));

		$this->assert_is_object(new Toa\Consumo_toa);
		$this->assert_true(empty(Toa\Consumo_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Empresa_ciudad_toa);
		$this->assert_false(empty(Toa\Empresa_ciudad_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Empresa_toa);
		$this->assert_false(empty(Toa\Empresa_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Panel);
		$this->assert_true(empty(Toa\Panel::create()->get_fields()));

		$this->assert_is_object(new Toa\Ps_vpi_toa);
		$this->assert_false(empty(Toa\Ps_vpi_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Stock);
		$this->assert_true(empty(Toa\Stock::create()->get_fields()));

		$this->assert_is_object(new Toa\Tecnico_toa);
		$this->assert_false(empty(Toa\Tecnico_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Tip_material_toa);
		$this->assert_false(empty(Toa\Tip_material_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Tipo_trabajo_toa);
		$this->assert_false(empty(Toa\Tipo_trabajo_toa::create()->get_fields()));

		$this->assert_is_object(new Toa\Uso);
		$this->assert_true(empty(Toa\Uso::create()->get_fields()));
	}

	public function test_models_otros()
	{
		$this->assert_is_object(new Adminbd);
		$this->assert_true(empty(Adminbd::create()->get_fields()));

		$this->assert_is_object(new Catalogo_foto);
		$this->assert_false(empty(Catalogo_foto::create()->get_fields()));

		$this->assert_is_object(new Despachos);
		$this->assert_true(empty(Despachos::create()->get_fields()));
	}

}
