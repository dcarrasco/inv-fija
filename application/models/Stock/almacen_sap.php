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
 * Clase Modelo Almacén SAP
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
class Almacen_sap extends ORM_Model {

	protected $db_table = 'config::bd_almacenes_sap';
	protected $label = 'Almac&eacute;n SAP';
	protected $label_plural = 'Almacenes SAP';
	protected $order_by = 'centro, cod_almacen';

	protected $fields = [
		'centro' => [
			'label'          => 'Centro',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'C&oacute;digo SAP del centro. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
		],
		'cod_almacen' => [
			'label'          => 'Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'C&oacute;digo SAP del almac&eacuten. M&aacute;ximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
		],
		'des_almacen' => [
			'label'          => 'Descripci&oacute;n Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Descripci&oacute;n del almac&eacuten. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'uso_almacen' => [
			'label'          => 'Uso Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Indica para que se usa el almac&eacute;n. M&aacute;ximo 50 caracteres.',
		],
		'responsable' => [
			'label'          => 'Responsable',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre del responsable del almac&eacuten. M&aacute;ximo 50 caracteres.',
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
			'onchange'       => 'form_onchange::tipo_op:tipos:stock_config/get_select_tipoalmacen',
		],
		'tipos' => [
			'tipo'           => Orm_field::TIPO_HAS_MANY,
			'relation'       => [
				'model'         => tipoalmacen_sap::class,
				'join_table'    => 'config::bd_tipoalmacen_sap',
				'id_one_table'  => ['centro', 'cod_almacen'],
				'id_many_table' => ['id_tipo'],
				'conditions'    => ['tipo_op' => '@field_value:tipo_op:MOVIL'],
			],
			'texto_ayuda'    => 'Tipos asociados al almac&eacuten.',
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
	 * @return string Almacén
	 */
	public function __toString()
	{
		return (string) $this->centro . '-' . $this->cod_almacen . ' ' .$this->des_almacen;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con almacenes para usar en combo
	 *
	 * @param  string $tipo_op Indica si la operacion es fija o movil
	 * @param  string $filtro  Filtra almacenes por tipo
	 * @return array           Arreglo de almacenes
	 */
	public function get_combo_almacenes($tipo_op = '', $filtro = '')
	{
		return cached_query('combo_almacenes'.$tipo_op.$filtro, $this, 'get_combo_almacenes_db', [$tipo_op, $filtro]);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con almacenes para usar en combo
	 *
	 * @param  string $tipo_op Indica si la operacion es fija o movil
	 * @param  string $filtro  Filtra almacenes por tipo
	 * @return array           Arreglo de almacenes
	 */
	public function get_combo_almacenes_db($tipo_op = '', $filtro = '')
	{
		if ($filtro === '')
		{
			return $this->where('tipo_op', $tipo_op)->get_dropdown_list(FALSE);
		}
		else
		{
			$arr_result = $this->db
				->select("a.centro + '{$this->separador_campos}' + a.cod_almacen as llave", FALSE)
				->select("a.centro + '-' + a.cod_almacen + ' ' + a.des_almacen as valor", FALSE)
				->order_by('a.centro, a.cod_almacen')
				->where('a.tipo_op', $tipo_op)
				->from($this->get_db_table().' a')
				->join(config('bd_tipoalmacen_sap') . ' ta', 'a.centro=ta.centro and a.cod_almacen=ta.cod_almacen')
				->where_in('ta.id_tipo', explode($this->separador_campos, $filtro))
				->get()->result_array();

			return form_array_format($arr_result);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con almacenes no ingresados al sistema
	 *
	 * @return array Arreglo de almacenes no ingresados
	 */
	public function almacenes_no_ingresados()
	{
		$alm_movil = $this->db
			->distinct()
			->select('s.centro, s.cod_bodega')
			->from(config('bd_stock_movil').' s')
			->join(config('bd_almacenes_sap').' a', 's.cod_bodega=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from '.config('bd_stock_movil').')', NULL, FALSE)
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.cod_bodega')
			->get()
			->result_array();

		$alm_fija = $this->db
			->distinct()
			->select('s.centro, s.almacen as cod_bodega')
			->from(config('bd_stock_fija').' s')
			->join(config('bd_almacenes_sap').' a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from '.config('bd_stock_fija').')', NULL, FALSE)
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.almacen')
			->get()
			->result_array();

		return array_merge($alm_movil, $alm_fija);
	}

}

// End of file almacen_sap.php
// Location: ./models/Stock/almacen_sap.php
