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

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_almacen Identificador del almacen
	 * @return void
	 */
	public function __construct($id_almacen = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_almacenes_sap'),
				'model_label'        => 'Almac&eacute;n',
				'model_label_plural' => 'Almacenes',
				'model_order_by'     => 'centro, cod_almacen',
			),
			'campos' => array(
				'centro' => array(
						'label'          => 'Centro',
						'tipo'           => 'char',
						'largo'          => 10,
						'texto_ayuda'    => 'C&oacute;digo SAP del centro. M&aacute;ximo 10 caracteres.',
						'es_id'          => TRUE,
						'es_obligatorio' => TRUE,
				),
				'cod_almacen' => array(
						'label'          => 'Almac&eacute;n',
						'tipo'           => 'char',
						'largo'          => 10,
						'texto_ayuda'    => 'C&oacute;digo SAP del almac&eacuten. M&aacute;ximo 10 caracteres.',
						'es_id'          => TRUE,
						'es_obligatorio' => TRUE,
				),
				'des_almacen' => array(
						'label'          => 'Descripci&oacute;n Almac&eacute;n',
						'tipo'           => 'char',
						'largo'          => 50,
						'texto_ayuda'    => 'Descripci&oacute;n del almac&eacuten. M&aacute;ximo 50 caracteres.',
						'es_obligatorio' => TRUE,
				),
				'uso_almacen' => array(
						'label'          => 'Uso Almac&eacute;n',
						'tipo'           => 'char',
						'largo'          => 50,
						'texto_ayuda'    => 'Indica para que se usa el almac&eacute;n. M&aacute;ximo 50 caracteres.',
				),
				'responsable' => array(
						'label'          => 'Responsable',
						'tipo'           => 'char',
						'largo'          => 50,
						'texto_ayuda'    => 'Nombre del responsable del almac&eacuten. M&aacute;ximo 50 caracteres.',
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
						'onchange'       => form_onchange('tipo_op', 'tipos', 'stock_config/get_select_tipoalmacen'),
				),
				'tipos' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'tipoalmacen_sap',
						'conditions'    => array(
							'tipo_op' => '@field_value:tipo_op:MOVIL'
						),
						'join_table'    => $this->config->item('bd_tipoalmacen_sap'),
						'id_one_table'  => array('centro','cod_almacen'),
						'id_many_table' => array('id_tipo'),
					),
					'texto_ayuda'    => 'Tipos asociados al almac&eacuten.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_almacen)
		{
			$this->fill($id_almacen);
		}
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
		if ($filtro === '')
		{
			return $this->find('list', array('conditions' => array('tipo_op' => $tipo_op)));
		}
		else
		{
			$arr_result = $this->db
				->select("a.centro + '{$this->_separador_campos}' + a.cod_almacen as llave", FALSE)
				->select("a.centro + '-' + a.cod_almacen + ' ' + a.des_almacen as valor", FALSE)
				->order_by('a.centro, a.cod_almacen')
				->where('a.tipo_op', $tipo_op)
				->from($this->get_model_tabla() . ' a')
				->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'a.centro=ta.centro and a.cod_almacen=ta.cod_almacen')
				->where_in('ta.id_tipo', explode($this->_separador_campos, $filtro))
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
			->from($this->config->item('bd_stock_movil') . ' s')
			->join($this->config->item('bd_almacenes_sap') . ' a', 's.cod_bodega=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from ' . $this->config->item('bd_stock_movil') . ')', NULL, FALSE)
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.cod_bodega')
			->get()
			->result_array();

		$alm_fija = $this->db
			->distinct()
			->select('s.centro, s.almacen as cod_bodega')
			->from($this->config->item('bd_stock_fija') . ' s')
			->join($this->config->item('bd_almacenes_sap') . ' a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from ' . $this->config->item('bd_stock_fija') . ')', NULL, FALSE)
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.almacen')
			->get()
			->result_array();

		return array_merge($alm_movil, $alm_fija);
	}

}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */