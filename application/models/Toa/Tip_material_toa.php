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
 * Clase Modelo Tipo Material de trabajos TOA
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
class Tip_material_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tecnico Identificador del tecnico
	 * @return void
	 */
	public function __construct($id_tip_material = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tip_material_toa'),
				'label'        => 'Tipo de Material',
				'label_plural' => 'Tipos de Material',
				'order_by'     => 'desc_tip_material',
			],
			'campos' => [
				'id'                => ['tipo' => Orm_field::TIPO_ID],
				'desc_tip_material' => [
					'label'          => 'Descripci&oacute;n tipo de material',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del tipo de material. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'color' => [
					'label'          => 'Color tipo material',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'Color o clase que identifica el tipo de material. M&aacute;ximo 50 caracteres.',
				],
				'uso_vpi' => [
					'label'          => 'Uso VPI',
					'tipo'           =>  Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica se el tipo de material es usado en control VPI.',
					'es_obligatorio' => TRUE,
					'default'        => 0
				],
				'tip_material' => [
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => [
						'model'         => \Inventario\catalogo::class,
						'join_table'    => config('bd_catalogo_tip_material_toa'),
						'id_one_table'  => ['id_tip_material'],
						'id_many_table' => ['id_catalogo'],
						//'conditions'    => ['id_app' => '@field_value:id_app'],
					],
					'texto_ayuda'    => 'Material TOA.',
				],
				'ps_vpi' => [
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => [
						'model'         => Ps_vpi_toa::class,
						'join_table'    => config('bd_ps_tip_material_toa'),
						'id_one_table'  => ['id_tip_material'],
						'id_many_table' => ['ps_id'],
						//'conditions'    => ['id_app' => '@field_value:id_app'],
					],
					'texto_ayuda'    => 'PS VPI.',
				],
				'clave_cierre' => [
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => [
						'model'         => Clave_cierre::class,
						'join_table'    => config('bd_claves_cierre_tip_material_toa'),
						'id_one_table'  => ['id_tip_material'],
						'id_many_table' => ['clave'],
						//'conditions'    => ['id_app' => '@field_value:id_app'],
					],
					'texto_ayuda'    => 'Claves cierre.',
				],
			],
		];

		parent::__construct($id_tip_material);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Técnico
	 */
	public function __toString()
	{
		return (string) $this->desc_tip_material;
	}

}
/* End of file Tip_material_toa.php */
/* Location: ./application/models/Toa/Tip_material_toa.php */
