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
class toa_models_test extends TestCase {

	public function test_ciudad_toa()
	{
		$ciudad_toa = new Toa\Ciudad_toa;

		$this->assertInternalType('object', $ciudad_toa);
		$this->assertNotEmpty($ciudad_toa->get_fields());
		$this->assertCount(3, $ciudad_toa->get_fields());
		$this->assertInternalType('string', (string) $ciudad_toa);
	}

	public function test_clave_cierre()
	{
		$clave_cierre = new Toa\Clave_cierre;

		$this->assertInternalType('object', $clave_cierre);
		$this->assertNotEmpty($clave_cierre->get_fields());
		$this->assertCount(6, $clave_cierre->get_fields());
		$this->assertInternalType('string', (string) $clave_cierre);
	}

	public function test_consumo_toa()
	{
		$consumo_toa = new Toa\Consumo_toa;

		$this->assertInternalType('object', $consumo_toa);
		$this->assertEmpty($consumo_toa->get_fields());
	}

	public function test_empresa_ciudad_toa()
	{
		$empresa_ciudad_toa = new Toa\Empresa_ciudad_toa;

		$this->assertInternalType('object', $empresa_ciudad_toa);
		$this->assertNotEmpty($empresa_ciudad_toa->get_fields());
		$this->assertCount(3, $empresa_ciudad_toa->get_fields());
		$this->assertInternalType('string', (string) $empresa_ciudad_toa);
	}

	public function test_empresa_toa()
	{
		$empresa_toa = new Toa\Empresa_toa;

		$this->assertInternalType('object', $empresa_toa);
		$this->assertNotEmpty($empresa_toa->get_fields());
		$this->assertCount(4, $empresa_toa->get_fields());
		$this->assertInternalType('string', (string) $empresa_toa);
	}

	public function test_panel()
	{
		$panel = new Toa\Panel;

		$this->assertInternalType('object', $panel);
		$this->assertEmpty($panel->get_fields());
	}

	public function test_ps_vpi_toa()
	{
		$ps_vpi_toa = new Toa\Ps_vpi_toa;

		$this->assertInternalType('object', $ps_vpi_toa);
		$this->assertNotEmpty($ps_vpi_toa->get_fields());
		$this->assertCount(3, $ps_vpi_toa->get_fields());
		$this->assertInternalType('string', (string) $ps_vpi_toa);
	}

	public function test_stock()
	{
		$stock = new Toa\Stock;

		$this->assertInternalType('object', $stock);
		$this->assertEmpty($stock->get_fields());
	}

	public function test_tecnico_toa()
	{
		$tecnico_toa = new Toa\Tecnico_toa;

		$this->assertInternalType('object', $tecnico_toa);
		$this->assertNotEmpty($tecnico_toa->get_fields());
		$this->assertCount(5, $tecnico_toa->get_fields());
		$this->assertInternalType('string', (string) $tecnico_toa);
	}

	public function test_tip_material_toa()
	{
		$tip_material_toa = new Toa\Tip_material_toa;

		$this->assertInternalType('object', $tip_material_toa);
		$this->assertNotEmpty($tip_material_toa->get_fields());
		$this->assertCount(7, $tip_material_toa->get_fields());
		$this->assertInternalType('string', (string) $tip_material_toa);
	}

	public function test_tipo_trabajo_toa()
	{
		$tipo_trabajo_toa = new Toa\Tipo_trabajo_toa;

		$this->assertInternalType('object', $tipo_trabajo_toa);
		$this->assertNotEmpty($tipo_trabajo_toa->get_fields());
		$this->assertCount(2, $tipo_trabajo_toa->get_fields());
		$this->assertInternalType('string', (string) $tipo_trabajo_toa);
	}

	public function test_uso()
	{
		$uso = new Toa\Uso;

		$this->assertInternalType('object', $uso);
		$this->assertEmpty($uso->get_fields());
	}

}
