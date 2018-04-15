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
 * Clase que modela un campo
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class Orm_field {

	/**
	 * Constantes de tipos de campos
	 */
	const TIPO_ID       = 'ID';
	const TIPO_INT      = 'INT';
	const TIPO_REAL     = 'REAL';
	const TIPO_CHAR     = 'CHAR';
	const TIPO_BOOLEAN  = 'BOOLEAN';
	const TIPO_DATETIME = 'DATETIME';
	const TIPO_HAS_ONE  = 'HAS_ONE';
	const TIPO_HAS_MANY = 'HAS_MANY';

	/**
	 * Nombre del campo
	 *
	 * @var  string
	 */
	protected $nombre = '';

	/**
	 * Nombre del tabla del campo
	 *
	 * @var  string
	 */
	protected $tabla_bd = '';

	/**
	 * Nombre del campo en la tabla
	 *
	 * @var  string
	 */
	protected $nombre_bd = '';

	/**
	 * Nombre del campo (etiqueta)
	 *
	 * @var  string
	 */
	protected $label = '';

	/**
	 * Texto de ayuda del campo
	 *
	 * @var  string
	 */
	protected $texto_ayuda = '';

	/**
	 * Valor por defecto del campo
	 *
	 * @var  string
	 */
	protected $default = '';

	/**
	 * Indica si el campo se muestra en la listado
	 *
	 * @var  boolean
	 */
	protected $mostrar_lista = TRUE;

	/**
	 * Indica si imprime un script en el evento de cambio del elemento del formulario
	 *
	 * @var  string
	 */
	protected $onchange = '';

	/**
	 * Tipo del campo
	 *
	 * @var  string
	 */
	protected $tipo = Orm_field::TIPO_CHAR;

	/**
	 * Largo del campo
	 *
	 * @var  string
	 */
	protected $largo = 10;

	/**
	 * Cantidad de decimales del campo
	 *
	 * @var  string
	 */
	protected $decimales = 2;

	/**
	 * Formato del campo
	 *
	 * @var  string
	 */
	protected $formato = '';

	/**
	 * Lista de los valores válidos para el campo
	 *
	 * @var  array
	 */
	protected $choices = [];

	/**
	 * Arreglo con los datos de la relación
	 *
	 * @var  array
	 */
	protected $relation = [];

	/**
	 * Indica si el campo es ID
	 *
	 * @var  boolean
	 */
	protected $es_id = FALSE;

	/**
	 * Indica si el campo es obligatorio
	 *
	 * @var  boolean
	 */
	protected $es_obligatorio = FALSE;

	/**
	 * Indica si el valor del campo debe ser único
	 *
	 * @var  boolean
	 */
	protected $es_unico = FALSE;

	/**
	 * Indica si el campo es auto-incremental
	 *
	 * @var  boolean
	 */
	protected $es_autoincrement = FALSE;


	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @param  string $nombre Nombre del campo
	 * @param  array  $param  Parametros de construccion
	 * @return void
	 **/
	public function __construct($nombre = '', $param = [])
	{
		$this->nombre    = $nombre;
		$this->nombre_bd = $nombre;
		$this->label     = $nombre;

		collect($param)->each(function($value, $index) {
			if (isset($this->{$index}))
			{
				$this->{$index} = $value;
			}
		});

		if ($this->tipo === Orm_field::TIPO_INT AND $this->default === '')
		{
			$this->default = 0;
		}

		if ($this->tipo === Orm_field::TIPO_HAS_ONE)
		{
			$this->es_obligatorio = TRUE;
		}

		if ($this->tipo === Orm_field::TIPO_DATETIME)
		{
			$this->largo = 20;
		}

		if ($nombre === 'id')
		{
			$this->tipo  = Orm_field::TIPO_ID;
			$this->largo = 10;

			$this->es_id            = TRUE;
			$this->es_obligatorio   = TRUE;
			$this->es_unico         = TRUE;
			$this->es_autoincrement = TRUE;
		}
	}

	/**
	 * Recupera el tipo de datos del campo
	 *
	 * @return string Tipo de datos del campo
	 */
	public function get_tipo()
	{
		return $this->tipo;
	}

	/**
	 * Recupera la etiqueta del campo
	 *
	 * @return string Etiqueta del campo
	 */
	public function get_label()
	{
		if ($this->tipo === Orm_field::TIPO_HAS_ONE)
		{
			$relation_model = new $this->relation['model'];
			return $relation_model->get_label();
		}
		if ($this->tipo === Orm_field::TIPO_HAS_MANY)
		{
			$relation_model = new $this->relation['model'];
			return $relation_model->get_label_plural();
		}

		return $this->label;
	}

	/**
	 * Recupera el nombre campo en la base de datos
	 *
	 * @return string Nombre del campo en la base de datos
	 */
	public function get_nombre_bd()
	{
		return $this->nombre_bd;
	}

	/**
	 * Recupera indicador de mostrar o no el campo en una lista
	 *
	 * @return boolean Indica si mostrar o no el campo en una lista
	 */
	public function get_mostrar_lista()
	{
		return $this->mostrar_lista;
	}

	public function get_default()
	{
		return $this->default;
	}

	/**
	 * Recupera indicador si el campo es ID
	 *
	 * @return boolean Indicador de campo ID
	 */
	public function get_es_id()
	{
		return $this->es_id;
	}

	public function get_choices()
	{
		return $this->choices;
	}

	/**
	 * Recupera indicador si el campo tiene valores unicos
	 *
	 * @return boolean Indicador de campo con valores unicos
	 */
	public function get_es_unico()
	{
		return $this->es_unico;
	}

	/**
	 * Recupera indicador si el campo es obligatorio
	 *
	 * @return boolean Indicador de campo obligatorio
	 */
	public function get_es_obligatorio()
	{
		return $this->es_obligatorio;
	}

	/**
	 * Recupera indicador si el campo es auto-incremental
	 *
	 * @return boolean Indicador de campo auto-incremental
	 */
	public function get_es_autoincrement()
	{
		return $this->es_autoincrement;
	}

	/**
	 * Recupera texto de ayuda del campo
	 *
	 * @return string Texto de ayuda del campo
	 */
	public function get_texto_ayuda()
	{
		return $this->texto_ayuda;
	}

	/**
	 * Recuperalas relaciones del campo
	 *
	 * @return array Relaciones del campo
	 */
	public function get_relation()
	{
		return $this->relation;
	}

	/**
	 * Fija las relaciones del campo
	 *
	 * @param  array $arr_relation Arreglo de relaciones
	 * @return void
	 */
	public function set_relation($arr_relation)
	{
		$this->relation = $arr_relation;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera el elemento de formulario para el campo
	 *
	 * @param  string  $valor          Valor a desplegar en el elemento de formulario
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @param  string  $clase_adic     Clases adicionales para la construccion del formulario
	 * @param  mixed   $field_error    Indica si el campo tiene error o no
	 * @return string                  Elemento de formulario
	 */
	public function form_field($valor = '', $filtra_activos = FALSE, $clase_adic = '', $field_error = NULL)
	{
		$ci =& get_instance();

		$id_prefix  = 'id_';

		$valor_field = ($valor === '' AND $this->default !== '') ? $this->default : $valor;
		$valor_field = request($this->nombre, $valor_field);

		$arr_param = [
			'name'        => $this->nombre,
			'id'          => $id_prefix . $this->nombre,
			'class'       => 'form-control '.$clase_adic,
			'value'       => $valor_field,
			'maxlength'   => $this->largo,
			'size'        => $this->largo,
			'placeholder' => $ci->lang->line('orm_placeholder').strtolower($this->label),
		];

		$status_feedback = '';
		if ( ! is_null($field_error))
		{
			$icon_feedback = $field_error ? 'remove' : 'ok';
			$icon_color    = $field_error ? '' : ' text-success';
			$status_feedback = '<span class="glyphicon glyphicon-'.$icon_feedback.' form-control-feedback'.$icon_color.'" aria-hidden="true"></span>';
		}

		if ($this->tipo === Orm_field::TIPO_ID
			OR ($this->es_id AND $valor_field !== '' AND $valor_field !== NULL)
			OR ($this->es_id AND $this->es_autoincrement))
		{
			$param_adic = ' id="'.$id_prefix.$this->nombre.'"';

			$valor_desplegar = ($this->tipo === Orm_field::TIPO_HAS_ONE) ? (string) $this->relation['model'] : $valor_field;

			$form = '<p class="form-control-static">'.$valor_desplegar.'</p>';
			$form .= form_hidden($this->nombre, $valor_field, $param_adic);

			return $form;
		}

		if ( ! empty($this->choices))
		{
			$param_adic = ' id="'.$id_prefix.$this->nombre.'" class="form-control '.$clase_adic.'"';
			$param_adic .= ($this->onchange !== '') ? ' onchange="'.$this->onchange.'"' : '';

			return form_dropdown($this->nombre, $this->choices, $valor_field, $param_adic);
		}

		if ($this->tipo === Orm_field::TIPO_CHAR AND $this->largo >= 100)
		{
			unset($arr_param['size']);
			$arr_param['cols'] = '50';
			$arr_param['rows'] = '5';

			return form_textarea($arr_param).$status_feedback;
		}

		if ($this->tipo === Orm_field::TIPO_CHAR OR $this->tipo === Orm_field::TIPO_DATETIME)
		{
			return form_input($arr_param).$status_feedback;
		}

		if ($this->tipo === Orm_field::TIPO_INT)
		{
			return form_number($arr_param).$status_feedback;
		}

		if ($this->tipo === 'password')
		{
			return form_password($arr_param).$status_feedback;
		}

		if ($this->tipo === 'picture')
		{
			return form_picture($arr_param).$status_feedback;
		}

		if ($this->tipo === Orm_field::TIPO_REAL)
		{
			$arr_param['size'] = $this->largo + $this->decimales + 1;

			return form_input($arr_param).$status_feedback;
		}

		if ($this->tipo === Orm_field::TIPO_BOOLEAN)
		{
			$valor = (boolean) $valor;

			$form = '<label class="radio-inline" for="'.$id_prefix.$this->nombre.'_1">';
			$form .= form_radio($this->nombre, 1, $valor_field, 'id='.$id_prefix.$this->nombre.'_1');
			$form .= $ci->lang->line('orm_radio_yes');
			$form .= '</label>';

			$form .= '<label class="radio-inline" for="'.$id_prefix.$this->nombre.'_0">';
			$form .= form_radio($this->nombre, 0, ! $valor_field, 'id='.$id_prefix.$this->nombre.'_0');
			$form .= $ci->lang->line('orm_radio_no');
			$form .= '</label>';

			return $form;
		}

		if ($this->tipo === Orm_field::TIPO_HAS_ONE)
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->nombre.'" class="form-control '.$clase_adic.'"';
			$param_adic .= ($this->onchange !== '') ? ' onchange="'.$this->onchange.'"' : '';

			$dropdown_conditions = array_key_exists('conditions', $this->relation)
				? ['conditions' => $this->relation['conditions'], 'opc_ini' => FALSE]
				: [];

			$form = form_dropdown(
				$this->nombre,
				$modelo_rel->get_dropdown_list(FALSE),
				$valor_field,
				$param_adic
			);

			return $form;
		}

		if ($this->tipo === Orm_field::TIPO_HAS_MANY)
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->nombre.'" size="7" class="form-control '.$clase_adic.'"';

			$dropdown_conditions = array_key_exists('conditions', $this->relation)
				? ['conditions' => $this->relation['conditions']]
				: [];

			$dropdown_conditions['opc_ini'] = FALSE;

			// Para que el formulario muestre multiples opciones seleccionadas, debemos usar este hack
			//$form = form_multiselect($this->nombre.'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
			$opciones = request($this->nombre) ? request($this->nombre) : $valor_field;

			$form = form_multiselect(
				$this->nombre.'[]',
				$modelo_rel->get_dropdown_list(FALSE),
				$opciones,
				$param_adic
			);

			return $form;
		}
	}

	// --------------------------------------------------------------------

	public function cast_value($valor)
	{
		if (in_array($this->get_tipo(), [Orm_field::TIPO_ID, Orm_field::TIPO_BOOLEAN, Orm_field::TIPO_INT]))
		{
			$valor = (int) $valor;
		}
		elseif ($this->get_tipo() === Orm_field::TIPO_DATETIME AND $valor !== '')
		{
			$valor = date('Y-m-d H:i:s', strtotime($valor));
		}

		return $valor;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el valor, formateado de acuerdo al tipo del campo
	 *
	 * @param  [type] $valor Valor a desplegar
	 * @return string        Valor formateado
	 */
	public function get_formatted_value($valor)
	{
		$ci =& get_instance();

		if ($this->tipo === Orm_field::TIPO_BOOLEAN)
		{
			return ($valor === 1) ? $ci->lang->line('orm_radio_yes') : $ci->lang->line('orm_radio_no');
		}

		if ($this->tipo === Orm_field::TIPO_HAS_ONE)
		{
			return (string) $this->relation['model'];
		}

		if ($this->tipo === Orm_field::TIPO_HAS_MANY)
		{
			return '<ul class="formatted_has_many">'
				. array_get($this->relation, 'data')
					->concat(function($obj_modelo) { return '<li>'.(string)$obj_modelo.'</li>'; })
				. '</ul>';
		}

		if (count($this->choices) > 0)
		{
			return array_get($this->choices, $valor, NULL);
		}

		if ($this->formato)
		{
			list($tipo_formato, $dec_formato) = explode(',', $this->formato);

			if ($tipo_formato === 'monto')
			{
				return fmt_monto($valor, 'UN', '$', (int) $dec_formato);
			}
			elseif ($tipo_formato === 'cantidad')
			{
				return fmt_cantidad($valor, (int) $dec_formato);
			}
			else
			{
				return 'XX'.$valor.'XX';
			}
		}

		return $valor;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los atributos para construir el campo en la base de datos
	 *
	 * @return array Configuración del campo para construirlo en la base de datos
	 */
	public function get_bd_attrib()
	{
		if ($this->tipo === Orm_field::TIPO_ID)
		{
			return [
				'type'           => 'INT',
				'constraint'     => $this->largo,
				'auto_increment' => $this->es_autoincrement,
				'null'           => $this->es_obligatorio,
			];
		}
		elseif ($this->tipo === Orm_field::TIPO_CHAR)
		{
			return [
				'type'       => 'VARCHAR',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			];
		}
		elseif ($this->tipo === 'text')
		{
			return [
				'type'       => 'TEXT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			];
		}
		elseif ($this->tipo === Orm_field::TIPO_INT)
		{
			return [
				'type'       => 'INT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			];
		}
		elseif ($this->tipo === Orm_field::TIPO_BOOLEAN)
		{
			return [
				'type'       => 'BIT',
				'null'       => $this->es_obligatorio,
			];
		}
		elseif ($this->tipo === Orm_field::TIPO_DATETIME)
		{
			$bd_attrib = [
				'type'       => 'DATETIME',
				'null'       => $this->es_obligatorio,
			];
		}
	}

}
/* End of file orm_field.php */
/* Location: ./application/libraries/orm_field.php */
