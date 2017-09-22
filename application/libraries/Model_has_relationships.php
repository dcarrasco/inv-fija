<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

	/**
	 * Recupera los campos relacionados
	 *
	 * @return none
	 */
	public function get_relation_fields()
	{
		$this->_recuperar_relation_fields();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los modelos dependientes (de las relaciones has_one y has_many)
	 *
	 * @param  Collection $relations_collection Coleccion de relacion donde se busca la relacion
	 * @return void
	 */
	protected function _recuperar_relation_fields($relations_collection = NULL)
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

	// --------------------------------------------------------------------

	protected function _recuperar_relation_field_has_one($nombre_campo, $obj_campo, $relations_collection = NULL)
	{
		// recupera las propiedades de la relacion
		$arr_props_relation = $obj_campo->get_relation();
		$class_relacionado = $arr_props_relation['model'];

		// si el objeto a recuperar existe en el arreglo $relations_collection,
		// recupera el objeto del arreglo y no va a buscarlo a la BD
		if ($relations_collection
			AND $relations_collection->key_exists($nombre_campo)
			AND $relations_collection->item($nombre_campo)->key_exists($this->{$nombre_campo})
		)
		{
			$model_relacionado = $relations_collection->item($nombre_campo)->item($this->{$nombre_campo});
		}
		else
		{
			$model_relacionado = new $class_relacionado();

			// si el valor del campo es distinto de nulo, lo va abuscar a la BD
			if ($this->{$nombre_campo} !== NULL)
			{
				$model_relacionado->find_id($this->{$nombre_campo}, FALSE);
			}
		}

		$arr_props_relation['model'] = $model_relacionado;
		$this->fields[$nombre_campo]->set_relation($arr_props_relation);
		$this->got_relations = TRUE;
	}

	// --------------------------------------------------------------------

	protected function _recuperar_relation_field_has_many($nombre_campo, $obj_campo, $relations_collection = NULL)
	{
		// recupera las propiedades de la relacion
		$arr_props_relation = $obj_campo->get_relation();

		$class_relacionado = $arr_props_relation['model'];
		$model_relacionado = new $class_relacionado();
		$arr_props_relation['model'] = $model_relacionado;

		// genera arreglo where con la llave del modelo
		$id_values = collect($this->values)->only($this->campo_id)->all();
		$where_pivot = collect($arr_props_relation['id_one_table'])
			->map_with_keys(function($id_key) use (&$id_values) {
				return [$id_key => array_shift($id_values)];
			})->all();

		// recupera la llave del modelo en tabla de la relacion (n:m)
		$registros_pivot = $this->db
			->select($this->_junta_campos_select($arr_props_relation['id_many_table']), FALSE)
			->get_where($arr_props_relation['join_table'], $where_pivot)
			->result_array();

		// genera arreglo de condiciones de busqueda
		$where_related   = collect($registros_pivot)->flatten()->all();
		$arr_condiciones = [$this->_junta_campos_select($model_relacionado->get_campo_id()) => $where_related];
		$arr_props_relation['data'] = $model_relacionado->find('all', ['conditions' => $arr_condiciones], FALSE);

		$this->values[$nombre_campo] = $where_related;
		$this->fields[$nombre_campo]->set_relation($arr_props_relation);
		$this->got_relations = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega a las relaciones del objeto actual,
	 * las relaciones del objeto pasado como parametro
	 *
	 * @param  mixed $obj_model Objeto del cual se sacan las relaciones
	 * @return void
	 */
	private function _add_relation_fields($obj_model)
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
					if ( ! $this->relation_objects->item($campo)->key_exists($obj_model->{$campo}))
					{
						$this->relation_objects->item($campo)->add_item($relation_model['model'], $obj_model->{$campo});
					}
				}
			});
	}

}
/* End of file model_has_relationships.php */
/* Location: ./application/libraries/model_has_relationships.php */
