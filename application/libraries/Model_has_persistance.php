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
trait Model_has_persistance {

	// --------------------------------------------------------------------

	/**
	 * Recupera registros por el ID
	 *
	 * @param  string  $id_modelo         Identificador del registro a recuperar
	 * @param  boolean $recupera_relation Indica si se recuperará la información de relación
	 * @return void
	 */
	public function find_id($id_modelo = '', $recupera_relation = TRUE)
	{
		return $this->find('first', ['conditions' => $this->array_id($id_modelo)], $recupera_relation);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera registros de la base de datos
	 *
	 * @param  string  $tipo              Indica el tipo de consulta
	 *                                    * all    todos los registros
	 *                                    * first  primer registro
	 *                                    * list   listado para combobox
	 *                                    * count  cantidad de registros
	 * @param  array   $param             Parametros para la consulta
	 *                                    * condition arreglo con condiciones para where
	 *                                    * filtro    string  con filtro de resultaods
	 *                                    * limit     cantidad de registros a devolver
	 *                                    * offset    pagina de registros a recuperar
	 * @param  boolean $recupera_relation Indica si se recuperan los modelos relacionados
	 * @return mixed                      Arreglo de resultados
	 */
	public function find($tipo = 'first', $param = [], $recupera_relation = TRUE)
	{
		$result = $this->get_db_result($tipo, $param);

		if ($tipo === 'first')
		{
			$this->fill_from_array($result);

			if ($recupera_relation)
			{
				$this->get_relation_fields();
			}

			return $this;
		}
		elseif ($tipo === 'all')
		{
			return collect($result)->map(function ($registro) use ($recupera_relation) {
				$obj_modelo = new $this->model_class($registro);

				if ($recupera_relation)
				{
					$obj_modelo->get_relation_fields($this->relation_objects);
					$this->_add_relation_fields($obj_modelo);
				}

				return $obj_modelo;
			});
		}
		elseif ($tipo === 'count')
		{
			return $result;
		}
		elseif ($tipo === 'list')
		{
			$opc_ini = collect(array_get($param, 'opc_ini', TRUE)
				? ['' => 'Seleccione '.strtolower($this->label).'...']
				: []
			);

			$opciones = collect($result)->map_with_keys(function ($registro) {
				$obj_modelo = new $this->model_class($registro);

				return [$obj_modelo->get_id() => (string) $obj_modelo];
			});

			return $opc_ini->merge($opciones)->all();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera uno o varios registros desde la BD
	 *
	 * @param  string $tipo  Tipo de query a ejecutar
	 * @param  array  $param Parámetros
	 * @return mixed         Registros de la BD
	 */
	protected function get_db_result($tipo = 'first', $param = [])
	{
		$this->db_filter($param);

		if ($tipo === 'first')
		{
			return $this->db->get($this->tabla)->row_array();
		}
		elseif ($tipo === 'all' OR $tipo === 'list')
		{
			return $this->db->get($this->tabla)->result_array();
		}
		elseif ($tipo === 'count')
		{
			return $this->db->from($this->tabla)->count_all_results();
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Genera las restricciones para consultar la BD
	 *
	 * @param  array $param Parámetros
	 * @return void
	 */
	protected function db_filter($param)
	{
		foreach (array_get($param, 'conditions', []) as $campo => $valor)
		{
			if (is_array($valor))
			{
				if (count($valor) === 0)
				{
					$this->db->where($campo.'=', 'NULL', FALSE);
				}
				else
				{
					$this->db->where_in($campo, $valor);
				}
			}
			else
			{
				$this->db->where($campo, $valor);
			}
		}

		$this->_put_filtro(array_get($param, 'filtro'));

		if (array_key_exists('limit', $param))
		{
			$this->db->limit(array_get($param, 'limit', NULL), array_get($param, 'offset', NULL));
		}

		$this->db->order_by(array_get($param, 'order_by', $this->order_by));
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un filtro para seleccionar elementos del modelo
	 *
	 * @param  string $filtro Filtro para seleccionar elementos
	 * @return nada
	 */
	protected function _put_filtro($filtro = '')
	{
		if ( ! empty($filtro))
		{
			$arr_like = collect($this->fields)
				->filter(function ($field) {
					return $field->get_tipo() === Orm_field::TIPO_CHAR;
				})->map(function($elem) use ($filtro) {
					return $filtro;
				})->all();

			if (count($arr_like) > 0)
			{
				$this->db->or_like($arr_like, $filtro, 'both');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Crea un arreglo con campos y valores del ID del modelo
	 *
	 * @param  string $id_modelo ID del modelo
	 * @return array
	 */
	protected function array_id($id_modelo)
	{
		$model_fields = $this->fields;

		return collect($this->campo_id)
			->combine(explode($this->separador_campos, $id_modelo))
			->map(function($valor, $llave) use ($model_fields) {
				$tipo = collect($model_fields)->get($llave)->get_tipo();

				// en caso que los id sean INT o ID, transforma los valores a (int)
				return in_array($tipo, [Orm_field::TIPO_ID, Orm_field::TIPO_INT])
					? (int) $valor : $valor;
			})
			->all();

	}
	// --------------------------------------------------------------------

	/**
	 * Graba el modelo en la base de datos (inserta o modifica, dependiendo si el modelo ya existe)
	 *
	 * @return nada
	 */
	public function grabar()
	{
		// NUEVO REGISTRO
		if ($this->is_new_record())
		{
			$this->save_new_record();

			if ($this->has_autoincrement_id())
			{
				collect($this->values_id_fields())->each(function($campo, $campo_id) {
					$this->values[$campo_id] = $this->db->insert_id();
				});
			}
		}
		// REGISTRO EXISTENTE
		else
		{
			if (! empty($this->values_not_id_fields()))
			{
				$this->db
					->where($this->values_id_fields())
					->update($this->tabla, $this->values_not_id_fields());
			}
		}

		$this->update_pivot_values();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo de valores de campos ID
	 *
	 * @return array
	 */
	protected function values_id_fields()
	{
		return collect($this->fields)
			->only($this->get_campo_id())
			->map(function($campo, $nombre) { return array_get($this->values, $nombre); })
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo de valores de campos no ID y no HAS_MANY
	 *
	 * @return array
	 */
	protected function values_not_id_fields()
	{
		return collect($this->fields)
			->except($this->get_campo_id())
			->filter(function($campo) { return $campo->get_tipo() !== Orm_field::TIPO_HAS_MANY; })
			->map(function($campo, $nombre) { return array_get($this->values, $nombre); })
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Determina si el modelo tiene campos ID autonumericos
	 *
	 * @return boolean
	 */
	protected function has_autoincrement_id()
	{
		return ! collect($this->fields)
			->only($this->get_campo_id())
			->filter(function($campo) { return $campo->get_es_autoincrement(); })
			->is_empty();
	}

	// --------------------------------------------------------------------

	/**
	 * Determina si los campos almacenados en el modelo son nuevos en ls BD
	 *
	 * @return boolean
	 */
	protected function is_new_record()
	{
		$values_id = $this->values_id_fields();

		return $this->has_autoincrement_id()
			? empty(collect($values_id)->sum())
			: ($this->db->get_where($this->tabla, $values_id)->row() === NULL
				OR $this->db->get_where($this->tabla, $values_id)->num_rows() === 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Graba (inserta) el modelo en la base de datos
	 *
	 * @return boolean
	 */
	protected function save_new_record()
	{
		$fields = $this->has_autoincrement_id()
			? $this->values_not_id_fields()
			: array_merge($this->values_id_fields(), $this->values_not_id_fields());

		return $this->db->insert($this->tabla, $fields);
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza registros de tabla pivote
	 *
	 * @return void
	 */
	protected function update_pivot_values()
	{
		$this->delete_pivot_values();

		collect($this->fields)
			->filter(function($campo) { return $campo->get_tipo() === Orm_field::TIPO_HAS_MANY; })
			->each(function($campo, $nombre_campo) {
				$relation  = $campo->get_relation();
				$id_fields = collect($relation['id_one_table'])->combine($this->values_id_fields())->all();

				collect($this->{$nombre_campo})
					->map(function($valor_campo) use ($relation, $id_fields) {
						return collect($relation['id_many_table'])
							->combine(explode($this->separador_campos, $valor_campo))
							->merge($id_fields)
							->all();
					})
					->each(function($insert_values) use ($relation) {
						$this->db->insert($relation['join_table'], $insert_values);
					});
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Borra registros de tabla pivote
	 *
	 * @return void
	 */
	protected function delete_pivot_values()
	{
		$id_fields  = $this->values_id_fields();

		collect($this->fields)
			->filter(function($campo) { return $campo->get_tipo() === Orm_field::TIPO_HAS_MANY; })
			->each(function($campo, $nombre_campo) use ($id_fields) {
				$relation = $campo->get_relation();
				$where_delete = collect($relation['id_one_table'])->combine($id_fields)->all();

				$this->db->delete($relation['join_table'], $where_delete);
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Elimina el modelo de la base de datos
	 *
	 * @return nada
	 */
	public function borrar()
	{
		$fields = collect($this->fields);

		$data_where = $fields->only($this->get_campo_id())
			->map(function($campo, $nombre) { return $this->{$nombre}; })
			->all();

		$this->db->delete($this->tabla, $data_where);

		$this->delete_pivot_values();
	}

}
/* End of file model_has_persistance.php */
/* Location: ./application/libraries/model_has_persistance.php */
