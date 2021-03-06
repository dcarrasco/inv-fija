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
 * Clase Modelo Centro
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
class Centro extends ORM_Model {

	protected $db_table = 'config::bd_centros';
	protected $label = 'Centro';
	protected $label_plural = 'Centros';
	protected $order_by = 'centro';

	protected $fields = [
		'centro' => [
			'label'          => 'Centro',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Nombre del centro. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
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
	 * Devuelve representación string del modelo
	 *
	 * @return string Centro
	 */
	public function __toString()
	{
		return (string) $this->centro;
	}

}

// End of file centro.php
// Location: ./models/Inventario/centro.php
