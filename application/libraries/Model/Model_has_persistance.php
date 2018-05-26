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
			->combine(explode($this->separador_campos, (string) $id_modelo))
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
					$this->set_value($campo_id, $this->db->insert_id());
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
					->update($this->get_db_table(), $this->values_not_id_fields());
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
			: ($this->db->get_where($this->get_db_table(), $values_id)->row() === NULL
				OR $this->db->get_where($this->get_db_table(), $values_id)->num_rows() === 0);
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

		return $this->db->insert($this->get_db_table(), $fields);
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

				collect($this->get_value($nombre_campo))
					->map(function($valor_campo) use ($relation, $id_fields) {
						return collect($relation['id_many_table'])
							->combine(explode($this->separador_campos, $valor_campo))
							->merge($id_fields)
							->all();
					})
					->each(function($insert_values) use ($relation) {
						$this->db->insert($this->get_db_table($relation['join_table']), $insert_values);
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

				$this->db->delete($this->get_db_table($relation['join_table']), $where_delete);
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

		$this->db->delete($this->get_db_table(), $data_where);

		$this->delete_pivot_values();
	}

}
/* End of file model_has_persistance.php */
/* Location: ./application/libraries/model_has_persistance.php */
