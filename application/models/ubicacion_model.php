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
 * Clase Modelo Ubicacion
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Ubicacion_model extends CI_Model {

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el total de los tipos de ubicacion
	 *
	 * @return integer Cantidad de tipos de ubicaciones
	 */
	public function total_ubicacion_tipo_ubicacion()
	{
		return $this->db->count_all($this->config->item('bd_ubic_tipoubic'));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve ubicaciones y tipos de ubicaciones
	 *
	 * @param  integer $limit  Cantidad de registros por pagina
	 * @param  integer $offset Numero de pàgina
	 * @return array           Ubicaciones y tipos de ubicaciones
	 */
	public function get_ubicacion_tipo_ubicacion($limit = 0, $offset =0)
	{
		return $this->db
			->order_by('tipo_inventario ASC, id_tipo_ubicacion ASC, ubicacion ASC')
			->get($this->config->item('bd_ubic_tipoubic'), $limit, $offset)
			->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve tipos de ubicacion para un tipo de inventario, para poblar combo
	 *
	 * @param  string $tipo_inventario Tipo de inventario
	 * @return array                   Tipo de ubicacion
	 */
	public function get_combo_tipos_ubicacion($tipo_inventario = '')
	{
		$arr_rs = $this->db
			->select('id as llave, tipo_ubicacion as valor')
			->order_by('tipo_ubicacion')
			->where('tipo_inventario', $tipo_inventario)
			->get($this->config->item('bd_tipo_ubicacion'))
			->result_array();

		return form_array_format($arr_rs, 'Seleccione tipo de ubicacion...');

	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve aquellas ubicaciones que no estan asociadas a un tipo de ubicacion
	 *
	 * @param  string $tipo_inventario Tipo de inventario
	 * @return array                  Arreglo de ubicaciones
	 */
	public function get_ubicaciones_libres($tipo_inventario = '')
	{
		$arr_rs = $this->db
			->distinct()
			->select('d.ubicacion as llave, d.ubicacion as valor')
			->from($this->config->item('bd_detalle_inventario') . ' d')
			->join($this->config->item('bd_inventarios') . ' i', 'd.id_inventario=i.id')
			->join($this->config->item('bd_ubic_tipoubic') . ' u', 'd.ubicacion=u.ubicacion and i.tipo_inventario=u.tipo_inventario', 'left')
			->where('i.tipo_inventario', $tipo_inventario)
			->where('u.id_tipo_ubicacion is null')
			->order_by('d.ubicacion')
			->get()->result_array();

		return form_array_format($arr_rs);
	}

	// --------------------------------------------------------------------

	/**
	 * Guarda registro de ubicacion y tpo de ubica
	 *
	 * @param  integer $id_ubicacion      ID de la ubicacion
	 * @param  string  $tipo_inventario   Tipo de inventario de la ubicacion
	 * @param  string  $ubicacion         Nombre o descripcion de la ubicacion
	 * @param  integer $id_tipo_ubicacion ID del tipo de ubicacion
	 * @return void
	 */
	public function guardar_ubicacion_tipo_ubicacion($id_ubicacion = 0, $tipo_inventario = '', $ubicacion = '', $id_tipo_ubicacion = 0)
	{
		if ($id_ubicacion === 0)
		{
			$this->db
				->insert(
					$this->config->item('bd_ubic_tipoubic'),
					array(
						'tipo_inventario'   => $tipo_inventario,
						'ubicacion'         => $ubicacion,
						'id_tipo_ubicacion' => $id_tipo_ubicacion,
					)
				);
		}
		else
		{
			$this->db
				->where('id', $id_ubicacion)
				->update(
					$this->config->item('bd_ubic_tipoubic'),
					array(
						'tipo_inventario'   => $tipo_inventario,
						'ubicacion'         => $ubicacion,
						'id_tipo_ubicacion' => $id_tipo_ubicacion,
					)
				);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Borrar ubicacion
	 *
	 * @param  integer $id_ubicacion ID de la ubicacion
	 *
	 * @return void
	 */
	public function borrar_ubicacion_tipo_ubicacion($id_ubicacion = 0)
	{
		$this->db->delete($this->config->item('bd_ubic_tipoubic'), array('id' => $id_ubicacion));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo de validacion para el ingreso de una ubicacion
	 *
	 * @return array Arreglo de validación
	 */
	public function get_validation_add()
	{
		return array(
			array(
				'field' => 'agr-tipo_inventario',
				'label' => 'Tipo de inventario',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'agr-ubicacion[]',
				'label' => 'Ubicaciones',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'agr-tipo_ubicacion',
				'label' => 'Tipo de ubicacion',
				'rules' => 'trim|required'
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo de validacion para la edicion de un conjunto de ubicaciones
	 *
	 * @return array Arreglo de validación
	 */
	public function get_validation_edit($arr_datos)
	{
		$arr_validacion = array();

		foreach($arr_datos as $registro)
		{
			array_push($arr_validacion, array(
				'field' => $registro['id'].'-tipo_inventario',
				'label' => 'Tipo de inventario',
				'rules' => 'trim|required'
			));
			array_push($arr_validacion, array(
				'field' => $registro['id'].'-tipo_ubicacion',
				'label' => 'Tipo Ubicacion',
				'rules' => 'trim|required'
			));
			array_push($arr_validacion, array(
				'field' => $registro['id'].'-ubicacion',
				'label' => 'Ubicacion',
				'rules' => 'trim|required'
			));
		}

		return $arr_validacion;
	}



}
/* End of file ubicacion_model.php */
/* Location: ./application/models/ubicacion_model.php */