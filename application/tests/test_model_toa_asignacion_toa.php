<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;
use Toa\Asignacion_toa;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_toa_asignacion_toa extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	protected function new_ci_object()
	{
		return [
			'session' => new mock_session,
			'db'      => new mock_db,
			'input'   => new mock_input,
		];
	}

	protected function getProperty($property_name = '')
	{
		$asignacion_toa = Asignacion_toa::create();

		$prop = new ReflectionProperty('Toa\Asignacion_toa', $property_name);
		$prop->setAccessible(TRUE);

		return $prop->getValue($asignacion_toa);
	}

	public function test_new()
	{
		$this->assert_is_object(new Asignacion_toa);
		$this->assert_is_object(Asignacion_toa::create($this->new_ci_object()));
	}

	public function test_fields()
	{
		$this->assert_empty(Asignacion_toa::create()->get_fields());
	}

	public function test_rules()
	{
		$this->assert_not_empty(Asignacion_toa::create()->rules);
		$this->assert_count(Asignacion_toa::create()->rules, 3);
	}

	public function test_tipos_reporte_asignaciones()
	{
		$this->assert_not_empty(Asignacion_toa::create()->tipos_reporte_asignaciones);
		$this->assert_count(Asignacion_toa::create()->tipos_reporte_asignaciones, 5);
	}

	public function test_movimientos_asignaciones()
	{
		$this->assert_not_empty(Asignacion_toa::create()->movimientos_asignaciones);
		$this->assert_count(Asignacion_toa::create()->movimientos_asignaciones, 2);
	}

	public function test_centros_asignacion()
	{
		$this->assert_not_empty(Asignacion_toa::create()->centros_asignacion);
		$this->assert_count(Asignacion_toa::create()->centros_asignacion, 2);
	}

	public function test_combo_unidades_asignacion()
	{
		$this->assert_not_empty(Asignacion_toa::create()->combo_unidades_asignacion);
		$this->assert_count(Asignacion_toa::create()->combo_unidades_asignacion, 3);
	}

	public function test_select_fields()
	{
		$this->assert_not_empty($this->getProperty('select_fields'));
		$this->assert_count($this->getProperty('select_fields'), 5);
	}

	public function test_group_by_fields()
	{
		$this->assert_not_empty($this->getProperty('group_by_fields'));
		$this->assert_count($this->getProperty('group_by_fields'), 5);
	}

	public function test_order_fields()
	{
		$this->assert_not_empty($this->getProperty('order_fields'));
		$this->assert_count($this->getProperty('order_fields'), 5);
	}

	public function test_configuracion_campos()
	{
		$this->assert_not_empty($this->getProperty('configuracion_campos'));
		$this->assert_count($this->getProperty('configuracion_campos'), 18);
	}

	public function test_config_campos_reportes()
	{
		$this->assert_not_empty($this->getProperty('config_campos_reportes'));
		$this->assert_count($this->getProperty('config_campos_reportes'), 5);
	}

	public function test_asignaciones_toa()
	{
		$asignacion_toa = new Asignacion_toa;
		$this->assert_empty($asignacion_toa->asignaciones_toa());
		$this->assert_empty($asignacion_toa->asignaciones_toa('tecnicos'));
		$this->assert_empty($asignacion_toa->asignaciones_toa('tecnicos', '201701'));
		$this->assert_empty($asignacion_toa->asignaciones_toa('tecnicos', '201701'));

		$this->assert_empty($asignacion_toa->documentos_asignaciones_toa());

		$this->assert_empty($asignacion_toa->detalle_asignacion_toa());
		$this->assert_empty($asignacion_toa->detalle_asignacion_toa('20170101'));

		$asignacion_toa = Toa\Asignacion_toa::create($this->new_ci_object());
		$asignacion_toa->db->mock_set_return_result([
			['fecha'=>'20170101', 'llave'=>'123456', 'dato'=>'12'],
			['fecha'=>'20170102', 'llave'=>'123456', 'dato'=>'12'],
		]);

		$this->assert_empty($asignacion_toa->control_asignaciones()->all());
		$this->assert_empty($asignacion_toa->control_asignaciones(collect(['mes'=>'201701']))->all());
	}

}
