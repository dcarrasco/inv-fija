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

namespace Inventario;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Almacén
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
class Almacen extends ORM_Model {

	protected $db_table = 'config::bd_almacenes';
	protected $label = 'Almac&eacute;n';
	protected $label_plural = 'Almacenes';
	protected $order_by = 'almacen';

	protected $fields = [
		'almacen' => [
			'label'          => 'Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Nombre del almac&eacute;n. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_almacen Identificador del almacen
	 * @return void
	 */
	public function __construct($id_almacen = NULL)
	{
		parent::__construct($id_almacen);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Almacén
	 */
	public function __toString()
	{
		return (string) $this->almacen;
	}

}
// End of file almacen.php
// Location: ./models/Inventario/almacen.php
