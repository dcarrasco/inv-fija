<?php

namespace Model;

use \App as AppContainer;

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
trait Model_has_attributes {

	/**
	 * Campos que conforman la llave (id) del modelo
	 *
	 * @var array
	 */
	protected $campo_id = [];

	/**
	 * Arreglo de registros recuperados de la BD
	 *
	 * @var array
	 */
	protected $values = [];

	protected $list_fields = [];

	protected $fechas = [];

	// --------------------------------------------------------------------

	/**
	 * __get
	 *
	 * Permite acceder a los campos del modelo de forma directa
	 *
	 * @param  string $campo Nombre del campo
	 * @return mixed         Valor del campo
	 */
	public function __get($campo)
	{
		$method = 'get_'.$campo;

		if (method_exists($this, $method))
		{
			return $this->$method($campo);
		}

		if (array_key_exists($campo, $this->values))
		{
			if ($this->fields[$campo]->get_tipo() === Orm_field::TIPO_HAS_ONE)
			{
				return $this->get_relation($campo);
			}

			if ($this->fields[$campo]->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				return ul($this->get_relation($campo)->all());
			}

			if (count($this->fields[$campo]->get_choices()) > 0)
			{
				return array_get($this->fields[$campo]->get_choices(), $this->get_value($campo));
			}

			return $this->get_value($campo);
		}

		if (is_null(app($campo)) && ! is_null(get_instance()->{$campo}))
		{
			AppContainer::add_component($campo, get_instance()->{$campo});
		}

		return app($campo);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los nombres de los campos que son el ID del modelo
	 *
	 * @return array Arreglo con los campos ID del modelo
	 */
	public function get_campo_id()
	{
		return $this->campo_id;
	}

	// --------------------------------------------------------------------

	public function get_value($campo)
	{
		return array_get($this->values, $campo);
	}

	// --------------------------------------------------------------------

	public function set_value($campo, $valor)
	{
		$this->values[$campo] = $valor;

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_list_fields()
	{
		return empty($this->list_fields) ? collect($this->fields)->keys()->all() : $this->list_fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los valores de los campos del modelo
	 *
	 * @return array Arreglo con los valores de los campos del modelo
	 */
	public function get_values()
	{
		return $this->values;
	}

	// --------------------------------------------------------------------

	/**
	 * determina y fija el campo id del modelo
	 *
	 * @return arreglo con nombre de los campos marcados como id
	 */
	public function determina_campo_id()
	{
		return collect($this->fields)
			->where('get_es_id', TRUE)
			->keys()
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el valor del id del modelo
	 *
	 * @return string
	 */
	public function get_id()
	{
		return collect($this->values)
			->only($this->campo_id)
			->implode($this->separador_campos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el valor de un campo del modelo
	 *
	 * @param  string  $campo     Nombre del campo
	 * @param  boolean $formatted Indica si se devuelve el valor formateado o no (ej. id de relaciones)
	 * @return string             Texto con el valor del campo
	 */
	public function get_field_value($campo = '', $formatted = TRUE)
	{
		return ! $formatted ? $this->{$campo} : $this->fields[$campo]->get_formatted_value($this->{$campo});
	}

	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores de un arreglo
	 *
	 * @param  array $arr_data Arreglo con los valores
	 * @return void
	 */
	public function fill($data = [])
	{
		$data = collect($data);

		collect($this->fields)
			->only($data->keys()->all())
			->each(function($campo, $nombre_campo) use ($data) {
				$this->set_value($nombre_campo, $campo->cast_value($data->get($nombre_campo)));
			});

		return $this;
	}

}
/* End of file model_has_attributes.php */
/* Location: ./application/libraries/model_has_attributes.php */
