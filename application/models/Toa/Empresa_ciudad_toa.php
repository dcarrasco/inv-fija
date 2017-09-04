<?php
namespace Toa;

/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \ORM_Model;
use \ORM_Field;

/**
 * Clase Modelo Empresa Ciudad TOA
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Empresa_ciudad_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_empresa Identificador de la empresa
	 * @return void
	 */
	public function __construct($id_empresa = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_empresas_ciudades_toa'),
				'label'        => 'Empresa ciudad TOA',
				'label_plural' => 'Empresas ciudades TOA',
				'order_by'     => 'id_empresa, id_ciudad',
			],
			'campos' => [
				'id_empresa' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => empresa_toa::class],
					'texto_ayuda'    => 'Seleccione una empresa TOA.',
				],
				'id_ciudad' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => ciudad_toa::class],
					'texto_ayuda'    => 'Seleccione una Ciudad TOA.',
				],
				'almacenes' => [
					'tipo'     => Orm_field::TIPO_HAS_MANY,
					'relation' => [
						'model'         => \Stock\almacen_sap::class,
						'join_table'    => config('bd_empresas_ciudades_almacenes_toa'),
						'id_one_table'  => ['id_empresa', 'id_ciudad'],
						'id_many_table' => ['centro', 'cod_almacen'],
						'conditions'    => ['centro' => ['CH32','CH33']],
					],
					'texto_ayuda' => 'Almacenes asociados a la empresa - ciudad.',
				],
			],
		];

		parent::__construct($id_empresa);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Empresa
	 */
	public function __toString()
	{
		return (string) $this->id_empresa;
	}


	public function ciudades_por_empresa($id_empresa = NULL)
	{
		return $this->find('all', ['conditions' => ['id_empresa' => $id_empresa]])
			->map(function ($ciudad_empresa) {return $ciudad_empresa->id_ciudad;})
			->all();
	}


}
/* End of file empresa_toa.php */
/* Location: ./application/models/Toa/empresa_toa.php */
