<?php
namespace Stock;
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
class Tipo_movimiento extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipoclasif Identificador del modulo
	 * @return void
	 */
	public function __construct($id = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tipos_movimientos'),
				'label'        => 'Tipo de movimiento',
				'label_plural' => 'Tipos de movimiento',
				'order_by'     => 'tipo_movimiento',
			],
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'tipo_movimiento' => [
					'label'          => 'Tipo de movimiento',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Tipo de movimiento. M&aacute;ximo 50 caracteres',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
			],
		];

		parent::__construct($id);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->tipo_movimiento;
	}

}
/* End of file Tipo_movimiento.php */
/* Location: ./application/models/Stock/Tipo_movimiento.php */
