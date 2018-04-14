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
 * Clase Modelo Tipo de Inventario
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
class Tipo_inventario extends ORM_Model {

	protected $db_table = 'config::bd_tipos_inventario';
	protected $label = 'Tipo de inventario';
	protected $label_plural = 'Tipos de inventario';
	protected $order_by = 'desc_tipo_inventario';

	protected $fields = [
		'id_tipo_inventario' => [
			'label'          => 'Tipo de inventario',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		],
		'desc_tipo_inventario' => [
			'label'          => 'Descripci&oacute;n tipo de inventario',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Descripci&oacute;n del tipo de inventario. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		],
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_inventario Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_inventario = NULL)
	{
		parent::__construct($id_tipo_inventario);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Tipo Inventario
	 */
	public function __toString()
	{
		return (string) $this->desc_tipo_inventario;
	}

}

// End of file tipo_inventario.php
// Location: ./models/Inventario/tipo_inventario.php
