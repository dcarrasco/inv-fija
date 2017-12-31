<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
namespace Stock;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Clase de Movimiento
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Tipo_movimiento_cmv extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_movimiento_cmv Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_movimiento_cmv = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tipos_movs_cmv'),
				'label'        => 'Tipo y clase de movimiento',
				'label_plural' => 'Tipos y clases de movimiento',
				'order_by'     => 'id_tipo_movimiento, signo DESC, cmv',
			],
			'campos' => [
				'id_tipo_movimiento' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => Tipo_movimiento::class],
					'texto_ayuda'    => 'Seleccione un tipo de movimiento.',
				],
				'cmv' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => Clase_movimiento::class],
					'texto_ayuda'    => 'Seleccione una clase de movimiento TOA.',
				],
				'signo' => [
					'label'          => 'Signo',
					'tipo'           => Orm_field::TIPO_INT,
					'largo'          => 10,
					'texto_ayuda'    => 'Signo del movimiento.',
					'es_obligatorio' => TRUE,
					'choices'     => [
						'1'  => 'Positivo',
						'-1' => 'Negativo',
					],
				],
			],
		];

		parent::__construct($id_tipo_movimiento_cmv);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->id_tipo_movimiento;
	}

}
// End of file Tipo_movimiento_cmv.php
// Location: ./models/Stock/Tipo_movimiento_cmv.php
