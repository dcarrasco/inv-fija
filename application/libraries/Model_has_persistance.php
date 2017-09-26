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

	// =======================================================================
	// Funciones de interaccion modelo <--> base de datos
	// =======================================================================

	/**
	 * Graba el modelo en la base de datos (inserta o modifica, dependiendo si el modelo ya existe)
	 *
	 * @return nada
	 */
	public function grabar()
	{
		$fields = collect($this->fields);

		$data_where = $fields->only($this->get_campo_id())
			->map(function($campo, $nombre) { return $this->{$nombre}; })
			->all();

		$data_update = $fields->except($this->get_campo_id())
			->filter(function($campo) { return $campo->get_tipo() !== Orm_field::TIPO_HAS_MANY; })
			->map(function($campo, $nombre) { return $this->{$nombre}; })
			->all();

		$es_auto_id = ! $fields->only($this->get_campo_id())
			->filter(function($campo) { return $campo->get_es_autoincrement(); })
			->is_empty();

		$es_insert = $es_auto_id
			? empty(collect($data_where)->sum())
			: $this->db->get_where($this->tabla, $data_where)->row() === NULL OR $this->db->get_where($this->tabla, $data_where)->num_rows() === 0;

		// NUEVO REGISTRO
		if ($es_insert)
		{
			if ( ! $es_auto_id)
			{
				$this->db->insert($this->tabla, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->tabla, $data_update);

				$data_where = collect($data_where)
					->map(function($campo, $campo_id) {
						$this->values[$campo_id] = $this->db->insert_id();
						return $this->db->insert_id();
					})
					->all();
			}
		}
		// REGISTRO EXISTENTE
		else
		{
			if ( ! empty($data_update))
			{
				$this->db->where($data_where);
				$this->db->update($this->tabla, $data_update);
			}
		}

		$fields->filter(function($campo) { return $campo->get_tipo() === Orm_field::TIPO_HAS_MANY; })
			->each(function($campo, $nombre_campo) use ($data_where) {
				$relation = $campo->get_relation();

				$where_delete = collect($relation['id_one_table'])->combine($data_where)->all();
				$this->db->delete($relation['join_table'], $where_delete);

				collect($this->{$nombre_campo})
					->map(function($valor_campo) use ($relation, $where_delete) {
						return collect($relation['id_many_table'])
							->combine(explode($this->separador_campos, $valor_campo))
							->merge($where_delete)
							->all();
					})
					->each(function($insert_values) use ($relation) {
						$this->db->insert($relation['join_table'], $insert_values);
					});
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

		$fields->filter(function($campo) { return $campo->get_tipo() === Orm_field::TIPO_HAS_MANY; })
			->each(function($campo, $nombre_campo) use ($data_where) {
				$relation = $campo->get_relation();
				$where_delete = collect($relation['id_one_table'])->combine($data_where)->all();
				$this->db->delete($relation['join_table'], $where_delete);
			});
	}

}
/* End of file model_has_persistance.php */
/* Location: ./application/libraries/model_has_persistance.php */
