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
 * Clase Modelo Empresa TOA
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
class Empresa_toa extends ORM_Model {

	protected $db_table = 'config::bd_empresas_toa';
	protected $label = 'Empresa TOA';
	protected $label_plural = 'Empresas TOA';
	protected $order_by = 'empresa';

	protected $fields = [
		'id_empresa' => [
			'label'          => 'ID Empresa',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'ID de la empresa. M&aacute;ximo 20 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
		'empresa' => [
			'label'          => 'Nombre de la empresa',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre de la empresa. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
		'tipoalm' => [
			'tipo'     => Orm_field::TIPO_HAS_MANY,
			'relation' => [
				'model'         => \Stock\Tipoalmacen_sap::class,
				'join_table'    => 'config::bd_empresas_toa_tiposalm',
				'id_one_table'  => ['id_empresa'],
				'id_many_table' => ['id_tipo'],
				//'conditions'    => ['id_app' => '@field_value:id_app'),
			],
			'texto_ayuda' => 'Tipos de almacen asociados a empresa TOA.',
		],
		'ciudades' => [
			'tipo'     => Orm_field::TIPO_HAS_MANY,
			'relation' => [
				'model'         => Ciudad_toa::class,
				'join_table'    => 'config::bd_empresas_ciudades_toa',
				'id_one_table'  => ['id_empresa'],
				'id_many_table' => ['id_ciudad'],
				//'conditions'    => ['id_app' => '@field_value:id_app'],
			],
			'texto_ayuda' => 'Ciudades asociados a empresa TOA.',
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
		return (string) $this->empresa;
	}

}
// End of file Empresa_toa.php
// Location: ./models/Toa/Empresa_toa.php
