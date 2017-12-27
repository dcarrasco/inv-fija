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
class test_models_toa extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_asignacion_toa()
	{
		$asignacion_toa = new Toa\Asignacion_toa;

		$this->assert_is_object($asignacion_toa);
		$this->assert_empty($asignacion_toa->get_fields());

		$this->assert_not_empty($asignacion_toa->rules);
		$this->assert_not_empty($asignacion_toa->tipos_reporte_asignaciones);
		$this->assert_not_empty($asignacion_toa->movimientos_asignaciones);
		$this->assert_not_empty($asignacion_toa->centros_asignacion);

		$this->assert_empty($asignacion_toa->asignaciones_toa());
		$this->assert_empty($asignacion_toa->asignaciones_toa('tecnicos'));
		$this->assert_empty($asignacion_toa->asignaciones_toa('tecnicos', '201701'));

		$this->assert_empty($asignacion_toa->documentos_asignaciones_toa());

		$this->assert_empty($asignacion_toa->detalle_asignacion_toa());
		$this->assert_empty($asignacion_toa->detalle_asignacion_toa('20170101'));

		$this->assert_empty($asignacion_toa->control_asignaciones());
		$this->assert_empty($asignacion_toa->control_asignaciones('20170101'));
	}

	public function test_ciudad_toa()
	{
		$ciudad_toa = new Toa\Ciudad_toa;

		$this->assert_is_object($ciudad_toa);
		$this->assert_not_empty($ciudad_toa->get_fields());
		$this->assert_equals(count($ciudad_toa->get_fields()), 3);
		$this->assert_is_string((string) $ciudad_toa);
	}

	public function test_clave_cierre()
	{
		$clave_cierre = new Toa\Clave_cierre;

		$this->assert_is_object($clave_cierre);
		$this->assert_not_empty($clave_cierre->get_fields());
		$this->assert_equals(count($clave_cierre->get_fields()), 6);
		$this->assert_is_string((string) $clave_cierre);
	}

	public function test_consumo_toa()
	{
		$consumo_toa = new Toa\Consumo_toa;

		$this->assert_is_object($consumo_toa);
		$this->assert_empty($consumo_toa->get_fields());
	}

	public function test_empresa_ciudad_toa()
	{
		$empresa_ciudad_toa = new Toa\Empresa_ciudad_toa;

		$this->assert_is_object($empresa_ciudad_toa);
		$this->assert_not_empty($empresa_ciudad_toa->get_fields());
		$this->assert_equals(count($empresa_ciudad_toa->get_fields()), 3);
		$this->assert_is_string((string) $empresa_ciudad_toa);
	}

	public function test_empresa_toa()
	{
		$empresa_toa = new Toa\Empresa_toa;

		$this->assert_is_object($empresa_toa);
		$this->assert_not_empty($empresa_toa->get_fields());
		$this->assert_equals(count($empresa_toa->get_fields()), 4);
		$this->assert_is_string((string) $empresa_toa);
	}

	public function test_panel()
	{
		$panel = new Toa\Panel;

		$this->assert_is_object($panel);
		$this->assert_empty($panel->get_fields());
	}

	public function test_ps_vpi_toa()
	{
		$ps_vpi_toa = new Toa\Ps_vpi_toa;

		$this->assert_is_object($ps_vpi_toa);
		$this->assert_not_empty($ps_vpi_toa->get_fields());
		$this->assert_equals(count($ps_vpi_toa->get_fields()), 3);
		$this->assert_is_string((string) $ps_vpi_toa);
	}

	public function test_stock()
	{
		$stock = new Toa\Stock;

		$this->assert_is_object($stock);
		$this->assert_empty($stock->get_fields());
	}

	public function test_tecnico_toa()
	{
		$tecnico_toa = new Toa\Tecnico_toa;

		$this->assert_is_object($tecnico_toa);
		$this->assert_not_empty($tecnico_toa->get_fields());
		$this->assert_equals(count($tecnico_toa->get_fields()), 5);
		$this->assert_is_string((string) $tecnico_toa);
	}

	public function test_tip_material_toa()
	{
		$tip_material_toa = new Toa\Tip_material_toa;

		$this->assert_is_object($tip_material_toa);
		$this->assert_not_empty($tip_material_toa->get_fields());
		$this->assert_equals(count($tip_material_toa->get_fields()), 7);
		$this->assert_is_string((string) $tip_material_toa);
	}

	public function test_tipo_trabajo_toa()
	{
		$tipo_trabajo_toa = new Toa\Tipo_trabajo_toa;

		$this->assert_is_object($tipo_trabajo_toa);
		$this->assert_not_empty($tipo_trabajo_toa->get_fields());
		$this->assert_equals(count($tipo_trabajo_toa->get_fields()), 2);
		$this->assert_is_string((string) $tipo_trabajo_toa);
	}

	public function test_uso()
	{
		$uso = new Toa\Uso;

		$this->assert_is_object($uso);
		$this->assert_empty($uso->get_fields());
	}

}
