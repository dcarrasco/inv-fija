<?php

namespace Model;

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

use Collection;

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
trait Model_has_relationships {

	/**
	 * Arreglo con los datos de la relación
	 *
	 * @var  array
	 */
	protected $relation = [];

	/**
	 * Indicador si se recuperaron los datos de las relaciones
	 *
	 * @var boolean
	 */
	protected $got_relations = FALSE;

	/**
	 * Arreglo temporal de objetos recuperados de la BD
	 *
	 * @var array
	 */
	protected $relation_objects = NULL;

	// --------------------------------------------------------------------

	/**
	 * Recupera los objetos que son las relaciones del campo indicado (1:n o n:m)
	 *
	 * @param  string $campo Nombre del campo a recuperar sus relaciones
	 * @return array         Arreglo con la relación del campo
	 */
	public function get_relation_object($campo = '')
	{
		if ( ! $campo)
		{
			return NULL;
		}

		$model_fields = $this->fields;
		$relation = $model_fields[$campo]->get_relation();

		return $relation['model'];
	}

	// --------------------------------------------------------------------

	public function get_relation($campo = NULL)
	{
		if ($this->fields[$campo]->get_tipo() === Orm_field::TIPO_HAS_ONE)
		{
			$this->get_relation_fields();
			return array_get($this->fields[$campo]->get_relation(), 'model');
		}

		if ($this->fields[$campo]->get_tipo() === Orm_field::TIPO_HAS_MANY)
		{
			$this->get_relation_fields();
			return array_get($this->fields[$campo]->get_relation(), 'data', collect());
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los modelos dependientes (de las relaciones has_one y has_many)
	 *
	 * @param  Collection $relations_collection Coleccion de relacion donde se busca la relacion
	 * @return void
	 */
	public function get_relation_fields($relations_collection = NULL)
	{
		if ( ! $this->got_relations)
		{
			foreach ($this->fields as $nombre_campo => $obj_campo)
			{
				if($obj_campo->get_tipo() === Orm_field::TIPO_HAS_ONE)
				{
					$this->_recuperar_relation_field_has_one($nombre_campo, $obj_campo, $relations_collection);
				}
				elseif ($obj_campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
				{
					$this->_recuperar_relation_field_has_many($nombre_campo, $obj_campo, $relations_collection);
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera relación para el tipo de campo HAS_ONE
	 *
	 * @param  string     $nombre_campo         Nombre del campo
	 * @param  ORM_Field  $obj_campo            Campo
	 * @param  Collection $relations_collection Collection de relación
	 * @return void
	 */
	protected function _recuperar_relation_field_has_one($nombre_campo, $obj_campo, $relations_collection = NULL)
	{
		// recupera las propiedades de la relacion
		$arr_props_relation = $obj_campo->get_relation();
		$class_relacionado = $arr_props_relation['model'];

		// si el objeto a recuperar existe en el arreglo $relations_collection,
		// recupera el objeto del arreglo y no va a buscarlo a la BD
		if ($relations_collection
			AND $relations_collection->key_exists($nombre_campo)
			AND $relations_collection->item($nombre_campo)->key_exists($this->get_value($nombre_campo))
		)
		{
			$model_relacionado = $relations_collection->item($nombre_campo)->item($this->get_value($nombre_campo));
		}

		$model_relacionado = new $class_relacionado($this->get_value($nombre_campo));
		$arr_props_relation['model'] = $model_relacionado;
		$this->fields[$nombre_campo]->set_relation($arr_props_relation);
		$this->got_relations = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera relación para el tipo de campo HAS_MANY
	 *
	 * @param  string     $nombre_campo         Nombre del campo
	 * @param  ORM_Field  $obj_campo            Campo
	 * @param  Collection $relations_collection Collection de relación
	 * @return void
	 */
	protected function _recuperar_relation_field_has_many($nombre_campo, $obj_campo, $relations_collection = NULL)
	{
		// recupera las propiedades de la relacion
		$relation = $obj_campo->get_relation();
		$class_relacionado = $relation['model'];
		$model_relacionado = new $class_relacionado();

		$relation['model'] = $model_relacionado;
		$pivot_objects = $this->get_pivot_objects($relation, $model_relacionado);

		$relation['data']  = $pivot_objects;

		$this->set_value(
			$nombre_campo,
			collect($pivot_objects)->map(function($model) {
				return $model->get_id();
			})
			->all()
		);

		$this->fields[$nombre_campo]->set_relation($relation);
		$this->got_relations = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los objetos asociados a una relación MANY TO MANY
	 *
	 * @param  array     $relation Datos de la relación
	 * @param  ORM_Model $model    Modelo ORM de la relación
	 * @return Collection
	 */
	protected function get_pivot_objects($relation = [], $model)
	{
		// genera arreglo where con la llave del modelo
		$where_pivot = collect($relation['id_one_table'])
			->combine($this->campo_id)
			->map(function($id) {return $this->{$id};})
			->all();

		// recupera la llave del modelo en tabla de la relacion (n:m)
		$where_related = collect($this->db
			->select($this->_junta_campos_select($relation['id_many_table']), FALSE)
			->get_where($this->get_db_table($relation['join_table']), $where_pivot)
			->result_array())
			->flatten();

		return $where_related->count() > 0
			? $model->where_in($this->_junta_campos_select($model->get_campo_id()), $where_related->all())
				->get(FALSE)
			: collect();
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega a las relaciones del objeto actual,
	 * las relaciones del objeto pasado como parametro
	 *
	 * @param  mixed $obj_model Objeto del cual se sacan las relaciones
	 * @return void
	 */
	protected function _add_relation_fields($obj_model)
	{
		collect($obj_model->get_fields())
			->filter(function($campo) {
				return $campo->get_tipo() === Orm_field::TIPO_HAS_ONE;
			})->each(function($obj_campo, $campo) use ($obj_model) {
				$relation_model = $obj_campo->get_relation();

				if (array_key_exists('model', $relation_model))
				{
					if ( ! $this->relation_objects->key_exists($campo))
					{
						$this->relation_objects->add_item(new Collection(), $campo);
					}
					if ( ! $this->relation_objects->item($campo)->key_exists($obj_model->get_value($campo)))
					{
						$this->relation_objects->item($campo)->add_item($relation_model['model'], $obj_model->get_value($campo));
					}
				}
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve campos select de una lista para ser consulados como un solo
	 * campo de la base de datos
	 *
	 * @param  array $campos Arreglo con el listado de campos
	 * @return string        Listado de campos unidos por la BD
	 */
	private function _junta_campos_select($campos = [])
	{
		$campos = collect($campos);

		if ($campos->count() === 1)
		{
			return $campos->first();
		}

		// CONCAT_WS es especifico para MYSQL
		if ($this->db->dbdriver === 'mysqli'
			OR ($this->db->dbdriver === 'pdo' && $this->db->subdriver === 'mysql'))
		{
			$lista_campos = $campos->implode(',');

			return "CONCAT_WS('{$this->separador_campos}',{$lista_campos})";
		}

		return $campos->implode("+'{$this->separador_campos}'+");
	}

}
/* End of file model_has_relationships.php */
/* Location: ./application/libraries/model_has_relationships.php */
