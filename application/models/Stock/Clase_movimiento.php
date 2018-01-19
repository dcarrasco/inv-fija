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
class Clase_movimiento extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_cmv Identificador de la clase de movimiento
	 * @return void
	 */
	public function __construct($id_cmv = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_cmv_sap'),
				'label'        => 'Clase de movimiento',
				'label_plural' => 'Clases de movimiento',
				'order_by'     => 'cmv',
			],
			'campos' => [
				'cmv' => [
					'label'          => 'C&oacute;digo movimiento',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'C&oacute;digo del movimiento. M&aacute;ximo 10 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'des_cmv' => [
					'label'          => 'Descripci&oacute;n del movimiento',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del movimiento. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					//'es_unico'       => TRUE
				],
			],
		];

		parent::__construct($id_cmv);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return "{$this->cmv} {$this->des_cmv}";
	}

}
// End of file Clase_movimiento.php
// Location: ./models/Stock/Clase_movimiento.php
