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
	private $nombre = '';

	/**
	 * Nombre del tabla del campo
	 *
	 * @var  string
	 */
	private $tabla_bd = '';

	/**
	 * Nombre del campo en la tabla
	 *
	 * @var  string
	 */
	private $nombre_bd = '';

	/**
	 * Nombre del campo (etiqueta)
	 *
	 * @var  string
	 */
	private $label = '';

	/**
	 * Texto de ayuda del campo
	 *
	 * @var  string
	 */
	private $texto_ayuda = '';

	/**
	 * Valor por defecto del campo
	 *
	 * @var  string
	 */
	private $default = '';

	/**
	 * Indica si el campo se muestra en la listado
	 *
	 * @var  boolean
	 */
	private $mostrar_lista = TRUE;

	/**
	 * Tipo del campo
	 *
	 * @var  string
	 */
	private $tipo = 'char';

	/**
	 * Largo del campo
	 *
	 * @var  string
	 */
	private $largo = 10;

	/**
	 * Cantidad de decimales del campo
	 *
	 * @var  string
	 */
	private $decimales = 2;

	/**
	 * Cantidad de decimales del campo
	 *
	 * @var  string
	 */
	private $formato = null;

	/**
	 * Lista de los valores válidos para el campo
	 *
	 * @var  array
	 */
	private $choices = array();

	/**
	 * Arreglo con los datos de la relación
	 *
	 * @var  array
	 */
	private $relation = array();

	/**
	 * Indica si el campo es ID
	 *
	 * @var  boolean
	 */
	private $es_id = FALSE;

	/**
	 * Indica si el campo es obligatorio
	 *
	 * @var  boolean
	 */
	private $es_obligatorio = FALSE;

	/**
	 * Indica si el valor del campo debe ser único
	 *
	 * @var  boolean
	 */
	private $es_unico = FALSE;

	/**
	 * Indica si el campo es auto-incremental
	 *
	 * @var  boolean
	 */
	private $es_autoincrement = FALSE;


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
		$this->nombre    = $nombre;
		$this->nombre_bd = $nombre;
		$this->label     = $nombre;

		if (is_array($param))
		{
			foreach ($param as $llave => $valor)
			{
				if (isset($llave))
				{
					$this->$llave = $valor;
				}
			}
		}

		if ($this->tipo === 'int' AND $this->default === '')
		{
			$this->default = 0;
		}

		if ($this->tipo === 'has_one')
		{
			$this->es_obligatorio = TRUE;
		}

		if ($this->tipo === 'datetime')
		{
			$this->largo = 20;
		}

		if ($nombre === 'id')
		{
			$this->tipo  = 'id';
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
		if ($this->tipo === 'has_one')
		{
			return $this->relation['model']->get_model_label();
		}

		if ($this->tipo === 'has_many')
		{
			return $this->relation['model']->get_model_label_plural();
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

	/**
	 * Recupera indicador si el campo es ID
	 *
	 * @return boolean Indicador de campo ID
	 */
	public function get_es_id()
	{
		return $this->es_id;
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
	 * @return string                  Elemento de formulario
	 */
	public function form_field($valor = '', $filtra_activos = FALSE, $clase_adic = '')
	{
		$ci =& get_instance();

		$id_prefix  = 'id_';

		$valor_field = ($valor === '' AND $this->default !== '') ? $this->default : $valor;
		$valor_field = set_value($this->nombre, $valor_field);

		$arr_param = array(
			'name'      => $this->nombre,
			'id'        => $id_prefix . $this->nombre,
			'class'     => 'form-control '.$clase_adic,
			'value'     => $valor_field,
			'maxlength' => $this->largo,
			'size'      => $this->largo,
		);

		if ($this->tipo === 'id'
			OR ($this->es_id AND $valor_field !== '' AND $valor_field !== NULL)
			OR ($this->es_id AND $this->es_autoincrement))
		{
			$param_adic = ' id="'.$id_prefix.$this->nombre.'"';

			$form = '<p class="form-control-static">'.$valor_field.'</p>';
			$form .= form_hidden($this->nombre, $valor_field, $param_adic);

			return $form;
		}

		if ( ! empty($this->choices))
		{
			$param_adic = ' id="'.$id_prefix.$this->nombre.'" class="form-control '.$clase_adic.'"';

			return form_dropdown($this->nombre, $this->choices, $valor_field, $param_adic);
		}

		if ($this->tipo === 'char' AND $this->largo >= 100)
		{
			unset($arr_param['size']);
			$arr_param['cols'] = '50';
			$arr_param['rows'] = '5';

			return form_textarea($arr_param);
		}

		if ($this->tipo === 'char' OR $this->tipo === 'datetime')
		{
			return form_input($arr_param);
		}

		if ($this->tipo === 'int')
		{
			return form_number($arr_param);
		}

		if ($this->tipo === 'password')
		{
			return form_password($arr_param);
		}

		if ($this->tipo === 'real')
		{
			$arr_param['size'] = $this->largo + $this->decimales + 1;

			return form_input($arr_param);
		}

		if ($this->tipo === 'boolean')
		{
			$form = '<label class="radio-inline" for="'.$id_prefix.$this->nombre.'_1">';
			$form .= form_radio($this->nombre, 1, ($valor_field === '1') ? TRUE : FALSE, 'id='.$id_prefix.$this->nombre.'_1');
			$form .= $ci->lang->line('orm_radio_yes');
			$form .= '</label>';

			$form .= '<label class="radio-inline" for="'.$id_prefix.$this->nombre.'_0">';
			$form .= form_radio($this->nombre, 0, ($valor_field === '1') ? FALSE : TRUE, 'id='.$id_prefix.$this->nombre.'_0');
			$form .= $ci->lang->line('orm_radio_no');
			$form .= '</label>';

			return $form;
		}

		if ($this->tipo === 'has_one')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->nombre.'" class="form-control '.$clase_adic.'"';

			$dropdown_conditions = array_key_exists('conditions', $this->relation)
				? array('conditions' => $this->relation['conditions'])
				: array();

			$form = form_dropdown(
				$this->nombre,
				$modelo_rel->find('list', $dropdown_conditions, FALSE),
				$valor_field,
				$param_adic
			);

			return $form;
		}

		if ($this->tipo === 'has_many')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="'.$id_prefix.$this->nombre.'" size="7" class="form-control '.$clase_adic.'"';

			$dropdown_conditions = array_key_exists('conditions', $this->relation)
				? array('conditions' => $this->relation['conditions'])
				: array();

			// Para que el formulario muestre multiples opciones seleccionadas, debemos usar este hack
			//$form = form_multiselect($this->nombre.'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
			$opciones = $ci->input->post($this->nombre) ? $ci->input->post($this->nombre) : $valor_field;

			$form = form_multiselect(
				$this->nombre.'[]',
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

		if ($this->tipo === 'boolean')
		{
			return ($valor === 1) ? $ci->lang->line('orm_radio_yes') : $ci->lang->line('orm_radio_no');
		}

		if ($this->tipo === 'has_one')
		{
			return (string) $this->relation['model'];
		}

		if ($this->tipo === 'has_many')
		{
			$campo = '<ul class="formatted_has_many">';
			foreach ($this->relation['data'] as $obj_modelo)
			{
				$campo .= '<li>'.$obj_modelo.'</li>';
			}
			$campo .= '</ul>';

			return $campo;
		}

		if (count($this->choices) > 0)
		{
			return $this->choices[$valor];
		}

		if ($this->formato)
		{
			if ($this->formato === 'monto,2')
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
		if ($this->tipo === 'id')
		{
			return array(
				'type'           => 'INT',
				'constraint'     => $this->largo,
				'auto_increment' => $this->es_autoincrement,
				'null'           => $this->es_obligatorio,
			);
		}
		else if ($this->tipo === 'char')
		{
			return array(
				'type'       => 'VARCHAR',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo === 'text')
		{
			return array(
				'type'       => 'TEXT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo === 'integer')
		{
			return array(
				'type'       => 'INT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo === 'boolean')
		{
			return array(
				'type'       => 'BIT',
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo === 'datetime')
		{
			$bd_attrib = array(
				'type'       => 'DATETIME',
				'null'       => $this->es_obligatorio,
			);
		}
	}


}

/* End of file orm_model.php */
/* Location: ./application/libraries/orm_model.php */