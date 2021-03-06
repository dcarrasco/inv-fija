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
 * Clase Modelo Tipo de Almacen SAP
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Tipoalmacen_sap extends ORM_Model {

	protected $db_table = 'config::bd_tiposalm_sap';
	protected $label = 'Tipo de Almac&eacute;n';
	protected $label_plural = 'Tipos de Almac&eacute;n';
	protected $order_by = 'tipo_op, tipo';

	protected $fields = [
		'id_tipo' => [
			'label'            => 'id',
			'tipo'             => Orm_field::TIPO_INT,
			'largo'            => 10,
			'texto_ayuda'      => '',
			'es_id'            => TRUE,
			'es_obligatorio'   => TRUE,
			'es_unico'         => TRUE,
			'es_autoincrement' => TRUE,
		],
		'tipo' => [
			'label'          => 'Tipo de Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Tipo del almac&eacute;n. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'tipo_op' => [
			'label'       => 'Tipo operaci&oacute;n',
			'tipo'        => Orm_field::TIPO_CHAR,
			'largo'       => 50,
			'texto_ayuda' => 'Seleccione el tipo de operaci&oacute;n.',
			'choices'     => [
				'MOVIL' => 'Operaci&oacute;n M&oacute;vil',
				'FIJA'  => 'Operaci&oacute;n Fija'
			],
			'es_obligatorio' => TRUE,
			'onchange'       => 'form_onchange::tipo_op:almacenes:stock_config/get_select_almacen',
		],
		'es_sumable' => [
			'label'          => 'Es sumable',
			'tipo'           => Orm_field::TIPO_BOOLEAN,
			'texto_ayuda'    => 'Indica si el tipo de almac&eacute;n se incluir&aacute; en la suma del stock.',
			'es_obligatorio' => TRUE,
			'default'        => 1,
		],
		'almacenes' => [
			'tipo'           => Orm_field::TIPO_HAS_MANY,
			'relation'       => [
				'model'         => almacen_sap::class,
				'join_table'    => 'config::bd_tipoalmacen_sap',
				'id_one_table'  => ['id_tipo'],
				'id_many_table' => ['centro', 'cod_almacen'],
				'conditions'    => ['tipo_op' => '@field_value:tipo_op:MOVIL'],
			],
			'texto_ayuda'    => 'Tipos asociados al almac&eacute;n.',
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
	 * Devuelve representación string del Tipo Almacen
	 *
	 * @return string Tipo almacen SAP
	 */
	public function __toString()
	{
		return (string) $this->tipo;
	}

	public function get_es_sumable()
	{
		return $this->get_value('es_sumable') ? 'Si' : 'No';
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de almacen y devuelve arreglo para poblar combo
	 *
	 * @param  string $tipo_op Tipo de operación (fija o movil)
	 * @return array           Arreglo de tipos de almacén
	 */
	public function get_combo_tiposalm($tipo_op = '')
	{
		return cached_query('combo_tiposalm'.$tipo_op, $this, 'get_combo_tiposalm_db', [$tipo_op]);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de almacen y devuelve arreglo para poblar combo
	 *
	 * @param  string $tipo_op Tipo de operación (fija o movil)
	 * @return array           Arreglo de tipos de almacén
	 */
	public function get_combo_tiposalm_db($tipo_op = '')
	{
		$arr_result = $this->db
			->select('id_tipo as llave, tipo as valor')
			->order_by('tipo')
			->where('tipo_op', $tipo_op)
			->get($this->get_tabla())
			->result_array();

		return form_array_format($arr_result);
	}

}
// End of file tipoalmacen_sap.php
// Location: ./models/Stock/tipoalmacen_sap.php
