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
class Tipo_movimiento extends ORM_Model {

	protected $db_table = 'config::bd_tipos_movimientos';
	protected $label = 'Tipo de movimiento';
	protected $label_plural = 'Tipos de movimiento';
	protected $order_by = 'tipo_movimiento';

	protected $fields = [
		'id'     => ['tipo' => Orm_field::TIPO_ID],
		'tipo_movimiento' => [
			'label'          => 'Tipo de movimiento',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Tipo de movimiento. M&aacute;ximo 50 caracteres',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_movimiento Identificador del tipo de movimiento
	 * @return void
	 */
	public function __construct($id_tipo_movimiento = NULL)
	{
		parent::__construct($id_tipo_movimiento);
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

// End of file Tipo_movimiento.php
// Location: ./models/Stock/Tipo_movimiento.php
