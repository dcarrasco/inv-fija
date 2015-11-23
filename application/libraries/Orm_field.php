<?php
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
	 * Nombre del campo
	 *
	 * @var  string
	 */
	private $_nombre = '';

	/**
	 * Nombre del tabla del campo
	 *
	 * @var  string
	 */
	private $_tabla_bd = '';

	/**
	 * Nombre del campo en la tabla
	 *
	 * @var  string
	 */
	private $_nombre_bd = '';

	/**
	 * Nombre del campo (etiqueta)
	 *
	 * @var  string
	 */
	private $_label = '';

	/**
	 * Texto de ayuda del campo
	 *
	 * @var  string
	 */
	private $_texto_ayuda = '';

	/**
	 * Valor por defecto del campo
	 *
	 * @var  string
	 */
	private $_default = '';

	/**
	 * Indica si el campo se muestra en la listado
	 *
	 * @var  boolean
	 */
	private $_mostrar_lista = TRUE;

	/**
	 * Tipo del campo
	 *
	 * @var  string
	 */
	private $_tipo = 'char';

	/**
	 * Largo del campo
	 *
	 * @var  string
	 */
	private $_largo = 10;

	/**
	 * Cantidad de decimales del campo
	 *
	 * @var  string
	 */
	private $_decimales = 2;

	/**
	 * Formato del campo
	 *
	 * @var  string
	 */
	private $_formato = NULL;

	/**
	 * Lista de los valores válidos para el campo
	 *
	 * @var  array
	 */
	private $_choices = array();

	/**
	 * Arreglo con los datos de la relación
	 *
	 * @var  array
	 */
	private $_relation = array();

	/**
	 * Indica si el campo es ID
	 *
	 * @var  boolean
	 */
	private $_es_id = FALSE;

	/**
	 * Indica si el campo es obligatorio
	 *
	 * @var  boolean
	 */
	private $_es_obligatorio = FALSE;

	/**
	 * Indica si el valor del campo debe ser único
	 *
	 * @var  boolean
	 */
	private $_es_unico = FALSE;

	/**
	 * Indica si el campo es auto-incremental
	 *
	 * @var  boolean
	 */
	private $_es_autoincrement = FALSE;


	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @param  string $nombre Nombre del campo
	 * @param  array  $param  Parametros de construccion
	 * @return void
	 **/
	public function __construct($nombre = '', $param = array())
	{
		$this->_nombre    = $nombre;
		$this->_nombre_bd = $nombre;
		$this->_label     = $nombre;

		if (is_array($param))
		{
			foreach ($param as $llave => $valor)
			{
				$llave = (substr($llave, 0, 1) !== '_' ? '_' : '').$llave;

				if (isset($this->$llave))
				{
					$this->$llave = $valor;
				}
			}
		}

		if ($this->_tipo === 'int' AND $this->_default === '')
		{
			$this->_default = 0;
		}

		if ($this->_tipo === 'has_one')
		{
			$this->_es_obligatorio = TRUE;
		}

		if ($this->_tipo === 'datetime')
		{
			$this->_largo = 20;
		}

		if ($nombre === 'id')
		{
			$this->_tipo  = 'id';
			$this->_largo = 10;

			$this->_es_id            = TRUE;
			$this->_es_obligatorio   = TRUE;
			$this->_es_unico         = TRUE;
			$this->_es_autoincrement = TRUE;
		}
	}

	/**
	 * Recupera el tipo de datos del campo
	 *
	 * @return string Tipo de datos del campo
	 */
	public function get_tipo()
	{
		return $this->_tipo;
	}

	/**
	 * Recupera la etiqueta del campo
	 *
	 * @return string Etiqueta del campo
	 */
	public function get_label()
	{
		if ($this->_tipo === 'has_one')
		{
			return $this->_relation['model']->get_model_label();
		}

		if ($this->_tipo === 'has_many')
		{
			return $this->_relation['model']->get_model_label_plural();
		}

		return $this->_label;
	}

	/**
	 * Recupera el nombre campo en la base de datos
	 *
	 * @return string Nombre del campo en la base de datos
	 */
	public function get_nombre_bd()
	{
		return $this->_nombre_bd;
	}

	/**
	 * Recupera indicador de mostrar o no el campo en una lista
	 *
	 * @return boolean Indica si mostrar o no el campo en una lista
	 */
	public function get_mostrar_lista()
	{
		return $this->_mostrar_lista;
	}

	/**
	 * Recupera indicador si el campo es ID
	 *
	 * @return boolean Indicador de campo ID
	 */
	public function get_es_id()
	{
		return $this->_es_id;
	}

	/**
	 * Recupera indicador si el campo tiene valores unicos
	 *
	 * @return boolean Indicador de campo con valores unicos
	 */
	public function get_es_unico()
	{
		return $this->_es_unico;
	}

	/**
	 * Recupera indicador si el campo es obligatorio
	 *
	 * @return boolean Indicador de campo obligatorio
	 */
	public function get_es_obligatorio()
	{
		return $this->_es_obligatorio;
	}

	/**
	 * Recupera indicador si el campo es auto-incremental
	 *
	 * @return boolean Indicador de campo auto-incremental
	 */
	public function get_es_autoincrement()
	{
		return $this->_es_autoincrement;
	}

	/**
	 * Recupera texto de ayuda del campo
	 *
	 * @return string Texto de ayuda del campo
	 */
	public function get_texto_ayuda()
	{
		return $this->_texto_ayuda;
	}

	/**
	 * Recuperalas relaciones del campo
	 *
	 * @return array Relaciones del campo
	 */
	public function get_relation()
	{
		return $this->_relation;
	}

	/**
	 * Fija las relaciones del campo
	 *
	 * @param  array $arr_relation Arreglo de relaciones
	 * @return void
	 */
	public function set_relation($arr_relation)
	{
		$this->_relation = $arr_relation;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera el elemento de formulario para el campo
	 *
	 * @param  string  $valor          Valor a desplegar en el elemento de formulario
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @param  string  $clase_adic     Clases adicionales para la construccion del formulario
	 * @return string                  Elemento de formulario
	 */
	public function form_field($valor = '', $filtra_activos = FALSE, $clase_adic = '')
	{
		$ci =& get_instance();

		$id_prefix  = 'id_';

		$valor_field = ($valor === '' AND $this->_default !== '') ? $this->_default : $valor;
		$valor_field = set_value($this->_nombre, $valor_field);

		$arr_param = array(
			'name'      => $this->_nombre,
			'id'        => $id_prefix . $this->_nombre,
			'class'     => 'form-control '.$clase_adic,
			'value'     => $valor_field,
			'maxlength' => $this->_largo,
			'size'      => $this->_largo,
		);

		if ($this->_tipo === 'id'
			OR ($this->_es_id AND $valor_field !== '' AND $valor_field !== NULL)
			OR ($this->_es_id AND $this->_es_autoincrement))
		{
			$param_adic = ' id="'.$id_prefix.$this->_nombre.'"';

			$form = '<p class="form-control-static">'.$valor_field.'</p>';
			$form .= form_hidden($this->_nombre, $valor_field, $param_adic);

			return $form;
		}

		if ( ! empty($this->_choices))
		{
			$param_adic = ' id="'.$id_prefix.$this->_nombre.'" class="form-control '.$clase_adic.'"';

			return form_dropdown($this->_nombre, $this->_choices, $valor_field, $param_adic);
		}

		if ($this->_tipo === 'char' AND $this->_largo >= 100)
		{
			unset($arr_param['size']);
			$arr_param['cols'] = '50';
			$arr_param['rows'] = '5';

			return form_textarea($arr_param);
		}

		if ($this->_tipo === 'char' OR $this->_tipo === 'datetime')
		{
			return form_input($arr_param);
		}

		if ($this->_tipo === 'int')
		{
			return form_number($arr_param);
		}

		if ($this->_tipo === 'password')
		{
			return form_password($arr_param);
		}

		if ($this->_tipo === 'picture')
		{
			return form_picture($arr_param);
		}

		if ($this->_tipo === 'real')
		{
			$arr_param['size'] = $this->_largo + $this->_decimales + 1;

			return form_input($arr_param);
		}

		if ($this->_tipo === 'boolean')
		{
			$form = '<label class="radio-inline" for="'.$id_prefix.$this->_nombre.'_1">';
			$form .= form_radio($this->_nombre, 1, ($valor_field === '1') ? TRUE : FALSE, 'id='.$id_prefix.$this->_nombre.'_1');
			$form .= $ci->lang->line('orm_radio_yes');
			$form .= '</label>';

			$form .= '<label class="radio-inline" for="'.$id_prefix.$this->_nombre.'_0">';
			$form .= form_radio($this->_nombre, 0, ($valor_field === '1') ? FALSE : TRUE, 'id='.$id_prefix.$this->_nombre.'_0');
			$form .= $ci->lang->line('orm_radio_no');
			$form .= '</label>';

			return $form;
		}

		if ($this->_tipo === 'has_one')
		{
			$nombre_rel_modelo = $this->_relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->_nombre.'" class="form-control '.$clase_adic.'"';

			$dropdown_conditions = array_key_exists('conditions', $this->_relation)
				? array('conditions' => $this->_relation['conditions'])
				: array();

			$form = form_dropdown(
				$this->_nombre,
				$modelo_rel->find('list', $dropdown_conditions, FALSE),
				$valor_field,
				$param_adic
			);

			return $form;
		}

		if ($this->_tipo === 'has_many')
		{
			$nombre_rel_modelo = $this->_relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->_nombre.'" size="7" class="form-control '.$clase_adic.'"';

			$dropdown_conditions = array_key_exists('conditions', $this->_relation)
				? array('conditions' => $this->_relation['conditions'])
				: array();

			// Para que el formulario muestre multiples opciones seleccionadas, debemos usar este hack
			//$form = form_multiselect($this->_nombre.'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
			$opciones = $ci->input->post($this->_nombre) ? $ci->input->post($this->_nombre) : $valor_field;

			$form = form_multiselect(
				$this->_nombre.'[]',
				$modelo_rel->find('list', $dropdown_conditions, FALSE),
				$opciones,
				$param_adic
			);

			return $form;
		}
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

		if ($this->_tipo === 'boolean')
		{
			return ($valor === 1) ? $ci->lang->line('orm_radio_yes') : $ci->lang->line('orm_radio_no');
		}

		if ($this->_tipo === 'has_one')
		{
			return (string) $this->_relation['model'];
		}

		if ($this->_tipo === 'has_many')
		{
			$campo = '<ul class="formatted_has_many">';
			foreach ($this->_relation['data'] as $obj_modelo)
			{
				$campo .= '<li>'.$obj_modelo.'</li>';
			}
			$campo .= '</ul>';

			return $campo;
		}

		if (count($this->_choices) > 0)
		{
			return $this->_choices[$valor];
		}

		if ($this->_formato)
		{
			if ($this->_formato === 'monto,2')
			{
				return fmt_monto($valor, 'UN', '$', 2);
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
		if ($this->_tipo === 'id')
		{
			return array(
				'type'           => 'INT',
				'constraint'     => $this->_largo,
				'auto_increment' => $this->_es_autoincrement,
				'null'           => $this->_es_obligatorio,
			);
		}
		else if ($this->_tipo === 'char')
		{
			return array(
				'type'       => 'VARCHAR',
				'constraint' => $this->_largo,
				'default'    => $this->_default,
				'null'       => $this->_es_obligatorio,
			);
		}
		else if ($this->_tipo === 'text')
		{
			return array(
				'type'       => 'TEXT',
				'constraint' => $this->_largo,
				'default'    => $this->_default,
				'null'       => $this->_es_obligatorio,
			);
		}
		else if ($this->_tipo === 'integer')
		{
			return array(
				'type'       => 'INT',
				'constraint' => $this->_largo,
				'default'    => $this->_default,
				'null'       => $this->_es_obligatorio,
			);
		}
		else if ($this->_tipo === 'boolean')
		{
			return array(
				'type'       => 'BIT',
				'null'       => $this->_es_obligatorio,
			);
		}
		else if ($this->_tipo === 'datetime')
		{
			$bd_attrib = array(
				'type'       => 'DATETIME',
				'null'       => $this->_es_obligatorio,
			);
		}
	}


}
/* End of file orm_field.php */
/* Location: ./application/libraries/orm_field.php */