<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
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

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_alm_sap Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_alm_sap = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_tiposalm_sap'),
				'model_label'        => 'Tipo Almac&eacute;n',
				'model_label_plural' => 'Tipos de Almac&eacute;n',
				'model_order_by'     => 'tipo_op, tipo',
			),
			'campos' => array(
				'id_tipo' => array(
						'label'            => 'id',
						'tipo'             => 'int',
						'largo'            => 10,
						'texto_ayuda'      => '',
						'es_id'            => TRUE,
						'es_obligatorio'   => TRUE,
						'es_unico'         => TRUE,
						'es_autoincrement' => TRUE,
				),
				'tipo' => array(
					'label'          => 'Tipo de Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Tipo del almac&eacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'tipo_op' => array(
					'label'          => 'Tipo operaci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Seleccione el tipo de operaci&oacute;n.',
					'choices'        => array(
						'MOVIL' => 'Operaci&oacute;n M&oacute;vil',
						'FIJA'  => 'Operaci&oacute;n Fija'
					),
					'es_obligatorio' => TRUE,
					'onchange'       => form_onchange('tipo_op', 'almacenes', 'stock_config/get_select_almacen'),
				),
				'es_sumable' => array(
					'label'          => 'Es sumable',
					'tipo'           => 'boolean',
					'texto_ayuda'    => 'Indica si el tipo de almac&eacute;n se incluir&aacute; en la suma del stock.',
					'es_obligatorio' => TRUE,
					'default'        => 1,
				),
				'almacenes' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'almacen_sap',
						'join_table'    => $this->config->item('bd_tipoalmacen_sap'),
						'id_one_table'  => array('id_tipo'),
						'id_many_table' => array('centro', 'cod_almacen'),
						'conditions'    => array('tipo_op' => '@field_value:tipo_op:MOVIL')
					),
					'texto_ayuda'    => 'Tipos asociados al almac&eacute;n.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tipo_alm_sap)
		{
			$this->fill($id_tipo_alm_sap);
		}
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


	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de almacen y devuelve arreglo para poblar combo
	 *
	 * @param  string $tipo_op Tipo de operación (fija o movil)
	 * @return array           Arreglo de tipos de almacén
	 */
	public function get_combo_tiposalm($tipo_op = '')
	{
		$arr_result = $this->db
			->select('id_tipo as llave, tipo as valor')
			->order_by('tipo')
			->where('tipo_op', $tipo_op)
			->get($this->get_model_tabla())
			->result_array();

		return form_array_format($arr_result);
	}


}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */