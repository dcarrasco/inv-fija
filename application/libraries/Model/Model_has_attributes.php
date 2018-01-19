<?php

namespace Model;

use App as AppContainer;

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
		if (array_key_exists($campo, $this->values))
		{
			return $this->values[$campo];
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

	/**
	 * Recupera los valores de los campos del modelo
	 *
	 * @return array Arreglo con los valores de los campos del modelo
	 */
	public function get_fields_values()
	{
		return $this->values;
	}

	// --------------------------------------------------------------------

	/**
	 * determina y fija el campo id del modelo
	 *
	 * @return arreglo con nombre de los campos marcados como id
	 */
	private function _determina_campo_id()
	{
		return collect($this->fields)
			->filter(function($campo) {return $campo->get_es_id(); })
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
	public function fill_from_array($data = [])
	{

		collect($this->fields)
			->only(collect($data)->keys()->all())
			->each(function($campo, $nombre_campo) use ($data) {
				$this->values[$nombre_campo] = $campo->cast_value(array_get($data, $nombre_campo));
			});

		return $this;
	}

}
/* End of file model_has_attributes.php */
/* Location: ./application/libraries/model_has_attributes.php */
