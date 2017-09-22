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
trait Model_has_form {

	// --------------------------------------------------------------------

	/**
	 * Ejecuta la validacion de los campos de formulario del modelo
	 *
	 * @return boolean Indica si el formulario fue validad OK o no
	 */
	public function valida_form()
	{
		$this->set_field_validation_rules(array_keys($this->fields));

		return $this->form_validation->run();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera la regla de validación para un campo, de acuerdo a la definición del campo
	 *
	 * @param  string/array $campo Nombre del campo / Arreglo con nombres de campos
	 * @return void
	 */
	public function set_field_validation_rules($campo = '')
	{
		if (is_array($campo))
		{
			return collect($campo)->map(function($field) {
				$this->set_field_validation_rules($field);
			});
		}

		$field = $this->fields[$campo];

		$reglas = 'trim';
		$reglas .= ($field->get_es_obligatorio() AND ! $field->get_es_autoincrement()) ? '|required' : '';
		$reglas .= ($field->get_tipo() === Orm_field::TIPO_INT)  ? '|integer' : '';
		$reglas .= ($field->get_tipo() === Orm_field::TIPO_REAL) ? '|numeric' : '';
		$reglas .= ($field->get_es_unico() AND ! $field->get_es_id())
			? '|edit_unique['.$this->tabla.':'.$field->get_nombre_bd().':'.
				implode($this->separador_campos, $this->get_campo_id()).':'.$this->get_id().']'
			: '';

		$campo_rules = $campo;
		if ($field->get_tipo() === Orm_field::TIPO_HAS_MANY)
		{
			$reglas = 'trim';
			$campo_rules = $campo.'[]';
		}

		$this->form_validation->set_rules($campo_rules, ucfirst($this->get_field_label($campo)), $reglas);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string con item de formulario para el campo indicado
	 *
	 * @param  string  $campo     Nombre del campo para devolver el elemento de formulario
	 * @param  boolean $show_help Indica si se mostrara glosa de ayuda
	 * @return string             Formulario
	 */
	public function form_item($campo = '', $show_help = TRUE)
	{
		$formulario_enviado = (boolean) ($this->input->method() === 'post');
		$field_error = NULL;

		if ($formulario_enviado)
		{
			$field_error = (form_has_error_class($campo) === 'has-error') ? TRUE : FALSE;
		}

		if ($campo !== '')
		{
			$data = [
				'form_label'    => form_label(
					ucfirst($this->get_field_label($campo)).$this->get_field_marca_obligatorio($campo),
					"id_{$campo}",
					['class' => 'control-label col-sm-4']
				),
				'item_error'    => form_has_error_class($campo),
				'item-feedback' => is_null($field_error) ? '' : 'has-feedback',
				'item_form'     => $this->print_form_field($campo, FALSE, '', $field_error),
				'item_help'     => $show_help ? $this->get_field_texto_ayuda($campo) : '',
			];

			return $this->parser->parse('orm/form_item', $data, TRUE);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el formulario para el despliegue de un campo del modelo
	 *
	 * @param  string  $campo          Nombre del campo
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @param  string  $clase_adic     Clases adicionales para construir el formulario
	 * @param  mixed   $field_error    Indica si el campo tiene error o no
	 * @return string                  Elemento de formulario
	 */
	public function print_form_field($campo = '', $filtra_activos = FALSE, $clase_adic = '', $field_error = NULL)
	{
		$arr_relation = $this->fields[$campo]->get_relation();

		// busca condiciones en la relacion a las cuales se les deba buscar un valor de filtro
		$arr_relation['conditions'] = collect($arr_relation)
			->map(function ($elem) { return collect($elem); })
			->get('conditions', collect())
			->map(function ($condition) {
				if (! is_array($condition) AND strpos($condition, '@field_value') === 0)
				{
					list($cond_tipo, $cond_campo, $cond_default) = explode(':', $condition);
					return $this->{$cond_campo} ? $this->{$cond_campo} : $cond_default;
				}

				return $condition;
			})->all();

		$this->fields[$campo]->set_relation($arr_relation);

		return $this->fields[$campo]->form_field($this->{$campo}, $filtra_activos, $clase_adic, $field_error);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el texto label de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Label del campo
	 */
	public function get_field_label($campo = '')
	{
		$tipo = $this->fields[$campo]->get_tipo();

		if (($tipo === Orm_field::TIPO_HAS_ONE OR $tipo === Orm_field::TIPO_HAS_MANY) AND ! $this->got_relations)
		{
			$this->_recuperar_relation_fields();
		}

		return $this->fields[$campo]->get_label();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el texto de ayuda de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Texto de ayuda del campo
	 */
	public function get_field_texto_ayuda($campo = '')
	{
		return $this->fields[$campo]->get_texto_ayuda();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la propiedad es_obligatorio de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return boolean       Indicador si el campo es obligatorio
	 */
	public function field_es_obligatorio($campo = '')
	{
		return $this->fields[$campo]->get_es_obligatorio();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve marca de campo obligatorio (*), si el campo así lo es
	 *
	 * @param  string $campo Nombre del campo
	 * @return string Texto html con la marca del campo
	 */
	public function get_field_marca_obligatorio($campo = '')
	{
		return $this->field_es_obligatorio($campo) ? ' <span class="text-danger">*</span>' : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores del post
	 *
	 * @return void
	 */
	public function recuperar_post()
	{
		$post_request = collect($this->fields)
			->map(function($field) {
				return NULL;
			})->merge(request())
			->all();

		return $this->fill_from_array($post_request);
	}

}
/* End of file model_has_form.php */
/* Location: ./application/libraries/model_has_form.php */
