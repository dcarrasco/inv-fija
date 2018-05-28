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
 * Clase Modelo Ciudad TOA
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
class Clave_cierre extends ORM_Model {

	protected $db_table = 'config::bd_claves_cierre_toa';
	protected $label = 'Clave cierre TOA';
	protected $label_plural = 'Claves cierre TOA';
	protected $order_by = 'clave';

	protected $fields = [
		'clave' => [
			'label'          => 'ID de la ciudad',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'ID de la clave de cierre. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		],
		'rev_clave' => [
			'label'          => 'Rev clave',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Rev clave. M&aacute;ximo 10 caracteres.',
			'es_obligatorio' => FALSE,
		],
		'descripcion' => [
			'label'          => 'Rev clave',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 255,
			'texto_ayuda'    => 'Descripcion de la clave de cierre. M&aacute;ximo 255 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'agrupacion_cs' => [
			'label'          => 'Agrupacion CS',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 255,
			'texto_ayuda'    => 'Agrupacion CS. M&aacute;ximo 255 caracteres.',
			'es_obligatorio' => FALSE,
		],
		'ambito' => [
			'label'          => 'Ambito',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 255,
			'texto_ayuda'    => '&Aacute;mbito de la clave de cierre. M&aacute;ximo 255 caracteres.',
			'es_obligatorio' => FALSE,
		],
		'tip_material' => [
			'tipo'           => Orm_field::TIPO_HAS_MANY,
			'relation'       => [
				'model'         => \Toa\Tip_material_toa::class,
				'join_table'    => 'config::bd_claves_cierre_tip_material_toa',
				'id_one_table'  => ['clave'],
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
	 * @return string Ciudad
	 */
	public function __toString()
	{
		return (string) $this->clave.' - '.$this->descripcion;
	}

}
// End of file Clave_cierre.php
// Location: ./models/Toa/Clave_cierre.php
