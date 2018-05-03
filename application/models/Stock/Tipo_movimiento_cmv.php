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

	protected $db_table = 'config::bd_tipos_movs_cmv';
	protected $label = 'Tipo y clase de movimiento';
	protected $label_plural = 'Tipos y clases de movimiento';
	protected $order_by = 'id_tipo_movimiento, signo DESC, cmv';

	protected $fields = [
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
