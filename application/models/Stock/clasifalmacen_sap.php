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
 * Clase Modelo Clasificacion de Almacenes
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
class Clasifalmacen_sap extends ORM_Model {

	protected $db_table = 'config::bd_clasifalm_sap';
	protected $label = 'Clasificaci&oacute;n de Almac&eacute;n';
	protected $label_plural = 'Clasificaciones de Almac&eacute;n';
	protected $order_by = 'tipo_op, orden, clasificacion';

	protected $fields = [
		'id_clasif' => [
			'label'            => 'id',
			'tipo'             => Orm_field::TIPO_INT,
			'largo'            => 10,
			'texto_ayuda'      => '',
			'es_id'            => TRUE,
			'es_obligatorio'   => TRUE,
			'es_unico'         => TRUE,
			'es_autoincrement' => TRUE,
		],
		'clasificacion' => [
			'label'          => 'Clasificaci&oacute;n de Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'orden' => [
			'label'          => 'Orden de la clasificaci&oacute;n',
			'tipo'           => Orm_field::TIPO_INT,
			'largo'          => 10,
			'texto_ayuda'    => 'Orden de la clasificaci&oacute;n del almac&eacute;n.',
			'es_obligatorio' => TRUE,
		],
		'dir_responsable' => [
			'label'          => 'Direcci&oacute;n responsable',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'Seleccione la direcci&oacute;n responsable',
			'choices'        => [
				'*'          => 'Por material',
				'TERMINALES' => 'Terminales',
				'REDES'      => 'Redes',
				'EMPRESAS'   => 'Empresas',
				'LOGISTICA'  => 'Log&iacute;stica',
				'TTPP'       => 'Telefon&iacute;a P&uacute;blica',
				'MARKETING'  => 'Marketing',
			],
			'es_obligatorio' => TRUE,
		],
		'estado_ajuste' => [
			'label'          => 'Estado de ajuste materiales',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'Indica confiabilidad de existencia del material.',
			'choices'        => [
				'EXISTE'     => 'Existe',
				'NO_EXISTE'  => 'No existe',
				'NO_SABEMOS' => 'No sabemos',
			],
			'es_obligatorio' => TRUE,
		],
		'id_tipoclasif' => [
			'tipo'     => Orm_field::TIPO_HAS_ONE,
			'relation' => ['model' => tipo_clasifalm::class],
		],
		'tipo_op' => [
			'label'          => 'Tipo operaci&oacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Seleccione el tipo de operaci&oacute;n.',
			'choices'        => [
				'MOVIL' => 'Operaci&oacute;n M&oacute;vil',
				'FIJA'  => 'Operaci&oacute;n Fija'
			],
			'es_obligatorio' => TRUE,
			'onchange'       => 'form_onchange::tipo_op:tiposalm:stock_config/get_select_tipoalmacen',
		],
		'tiposalm' => [
			'tipo'           => Orm_field::TIPO_HAS_MANY,
			'relation'       => [
				'model'         => tipoalmacen_sap::class,
				'join_table'    => 'config::bd_clasif_tipoalm_sap',
				'id_one_table'  => ['id_clasif'],
				'id_many_table' => ['id_tipo'],
				'conditions'    => ['tipo_op' => '@field_value:tipo_op:MOVIL'],
			],
			'texto_ayuda'    => 'Tipos de almac&eacute;n asociados a la clasificaci&oacute;n.',
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
		return (string) $this->clasificacion;
	}

}

// End of file clasifalmacen_sap.php
// Location: ./models/Stock/clasifalmacen_sap.php
