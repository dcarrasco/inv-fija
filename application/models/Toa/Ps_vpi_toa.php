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
class Ps_vpi_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_empresa Identificador de la empresa
	 * @return void
	 */
	public function __construct($id_ps = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_ps_vpi_toa'),
				'label'        => 'PS VPI',
				'label_plural' => 'PS VPI',
				'order_by'     => 'ps_id',
			],
			'campos' => [
				'ps_id' => [
					'label'          => 'PS ID',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'ID del PS VPI.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
				'descripcion' => [
					'label'          => 'Descripcion del PS VPI',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 70,
					'texto_ayuda'    => 'Descripción del PS VPI. M&aacute;ximo 70 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'tip_material' => [
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => [
						'model'         => \Toa\Tip_material_toa::class,
						'join_table'    => config('bd_ps_tip_material_toa'),
						'id_one_table'  => ['ps_id'],
						'id_many_table' => ['id_tip_material'],
						//'conditions'    => ['id_app' => '@field_value:id_app'],
					],
					'texto_ayuda'    => 'Tipo de material TOA.',
				],
			],
		];

		parent::__construct($id_ps);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Empresa
	 */
	public function __toString()
	{
		return (string) $this->descripcion;
	}

}
/* End of file ps_vpi_toa.php */
/* Location: ./application/models/toa/ps_vpi_toa.php */
