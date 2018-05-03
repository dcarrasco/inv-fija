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

namespace Toa;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Empresa Ciudad TOA
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
class Ps_vpi_toa extends ORM_Model {

	protected $db_table = 'config::bd_ps_vpi_toa';
	protected $label = 'PS VPI';
	protected $label_plural = 'PS VPI';
	protected $order_by = 'ps_id';

	protected $fields = [
		'ps_id' => [
			'label'          => 'PS ID',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 30,
			'texto_ayuda'    => 'ID del PS VPI.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		],
		'descripcion' => [
			'label'          => 'Descripcion del PS VPI',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 70,
			'texto_ayuda'    => 'Descripción del PS VPI. M&aacute;ximo 70 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
		'tip_material' => [
			'tipo'           => Orm_field::TIPO_HAS_MANY,
			'relation'       => [
				'model'         => \Toa\Tip_material_toa::class,
				'join_table'    => 'config::bd_ps_tip_material_toa',
				'id_one_table'  => ['ps_id'],
				'id_many_table' => ['id_tip_material'],
				//'conditions'    => ['id_app' => '@field_value:id_app'],
			],
			'texto_ayuda'    => 'Tipo de material TOA.',
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
	 * @return string Empresa
	 */
	public function __toString()
	{
		return (string) $this->descripcion;
	}

}

// End of file Ps_vpi_toa.php
// Location: ./models/Toa/Ps_vpi_toa.php
