<?php
namespace Inventario;

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

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_almacen Identificador del almacen
	 * @return void
	 */
	public function __construct($id_almacen = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_almacenes'),
				'label'        => 'Almac&eacute;n',
				'label_plural' => 'Almacenes',
				'order_by'     => 'almacen',
			],
			'campos' => [
				'almacen' => [
					'label'          => 'Almac&eacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'Nombre del almac&eacute;n. M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
			],
		];

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
/* End of file almacen.php */
/* Location: ./application/models/Inventario/almacen.php */
