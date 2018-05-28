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
 * Clase Modelo Unidad de medida
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
class Unidad_medida extends ORM_Model {

	protected $db_table = 'config::bd_unidades';
	protected $label = 'Unidad de medida';
	protected $label_plural = 'Unidades de medida';
	protected $order_by = 'unidad';

	protected $fields = [
		'unidad' => [
			'label'          => 'Unidad',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Unidad de medida. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
		'desc_unidad' => [
			'label'          => 'Descripci&oacute;n unidad de medida',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Descripci&oacute;n de la unidad de medida. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string de la unidad de medida
	 *
	 * @return string Unidad de medida
	 */
	public function __toString()
	{
		return (string) $this->desc_unidad;
	}

}

// End of file unidad_medida.php
// Location: ./models/Inventario/unidad_medida.php
