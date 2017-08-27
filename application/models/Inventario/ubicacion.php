<?php
namespace Inventario;

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

use \ORM_Model;
use \ORM_Field;

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
class Ubicacion extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct($id_ubicacion = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_ubic_tipoubic'),
				'label'        => 'Ubicaci&oacute;n',
				'label_plural' => 'Ubicaciones',
				'order_by'     => 'tipo_inventario, id_tipo_ubicacion, ubicacion',
			],
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'tipo_inventario' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => tipo_inventario::class],
					'texto_ayuda'    => 'Seleccione un tipo de inventario.',
				],
				'id_tipo_ubicacion' => [
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'es_obligatorio' => TRUE,
					'relation'       => ['model' => tipo_ubicacion::class],
					'texto_ayuda'    => 'Seleccione un tipo de ubicaci&oacute;n.',
				],
				'ubicacion' => [
					'label'          => 'Ubicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 45,
					'texto_ayuda'    => 'M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
				],
			],
		];

		parent::__construct($id_ubicacion);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el total de los tipos de ubicacion
	 *
	 * @return integer Cantidad de tipos de ubicaciones
	 */
	public function total_ubicacion_tipo_ubicacion()
	{
		return $this->db->count_all(config('bd_ubic_tipoubic'));
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
			->get(config('bd_ubic_tipoubic'), $limit, $offset)
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
			->get(config('bd_tipo_ubicacion'))
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
			->from(config('bd_detalle_inventario') . ' d')
			->join(config('bd_inventarios') . ' i', 'd.id_inventario=i.id')
			->join(config('bd_ubic_tipoubic') . ' u', 'd.ubicacion=u.ubicacion and i.tipo_inventario=u.tipo_inventario', 'left')
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
			$this->db->insert(config('bd_ubic_tipoubic'), [
				'tipo_inventario'   => $tipo_inventario,
				'ubicacion'         => $ubicacion,
				'id_tipo_ubicacion' => $id_tipo_ubicacion,
			]);
		}
		else
		{
			$this->db->where('id', $id_ubicacion)
				->update(config('bd_ubic_tipoubic'), [
					'tipo_inventario'   => $tipo_inventario,
					'ubicacion'         => $ubicacion,
					'id_tipo_ubicacion' => $id_tipo_ubicacion,
				]);
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
		$this->db->delete(config('bd_ubic_tipoubic'), ['id' => $id_ubicacion]);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo de validacion para el ingreso de una ubicacion
	 *
	 * @return array Arreglo de validación
	 */
	public function get_validation_add()
	{
		return [
			[
				'field' => 'agr-tipo_inventario',
				'label' => 'Tipo de inventario',
				'rules' => 'trim|required'
			],
			[
				'field' => 'agr-ubicacion[]',
				'label' => 'Ubicaciones',
				'rules' => 'trim|required'
			],
			[
				'field' => 'agr-tipo_ubicacion',
				'label' => 'Tipo de ubicacion',
				'rules' => 'trim|required'
			],
		];
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo de validacion para la edicion de un conjunto de ubicaciones
	 *
	 * @return array Arreglo de validación
	 */
	public function get_validation_edit($arr_datos)
	{
		return collect($arr_datos)->map(function($registro) {
			$id = $registro['id'];
			return  [
				[
					'field' => "tipo_inventario[{$id}]",
					'label' => 'Tipo de inventario',
					'rules' => 'trim|required'
				],
				[
					'field' => "tipo_ubicacion[{$id}]",
					'label' => 'Tipo Ubicacion',
					'rules' => 'trim|required'
				],
				[
					'field' => "ubicacion[{$id}]",
					'label' => 'Ubicacion',
					'rules' => 'trim|required'
				],
			];
		})->flatten(1)
		->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la configuración del paginador de ubicaciones
	 * @return array
	 */
	public function pagination_config()
	{
		return [
			'total_rows'  => $this->total_ubicacion_tipo_ubicacion(),
			'per_page'    => $this->page_results,
			'base_url'    => site_url($this->router->class . '/ubicacion_tipo_ubicacion'),
			'uri_segment' => 3,
			'num_links'   => 5,

			'full_tag_open'   => '<ul class="pagination">',
			'flil_tag_close'  => '</ul>',

			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',

			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',
		];
	}


}
/* End of file ubicacion.php */
/* Location: ./application/models/ubicacion.php */
