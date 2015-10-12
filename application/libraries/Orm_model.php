<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category	Controller
 * @author		dcr
 * @link
 */
class Orm_model implements IteratorAggregate {

	/**
	 * Nombre del modelo
	 *
	 * @var  string
	 */
	private $model_nombre = '';

	/**
	 * Clase del modelo
	 *
	 * @var  string
	 */
	private $model_class = '';

	/**
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	private $model_tabla = '';

	/**
	 * Nombre (etiqueta) del modelo
	 *
	 * @var string
	 */
	private $model_label = '';

	/**
	 * Nombre (etiqueta) del modelo (plural)
	 *
	 * @var string
	 */
	private $model_label_plural = '';

	/**
	 * Campos para ordenar el modelo cuando se recupera de la BD
	 *
	 * @var string
	 */
	private $model_order_by = '';

	/**
	 * Campos que conforman la llave (id) del modelo
	 *
	 * @var array
	 */
	private $model_campo_id = array();

	/**
	 * Indicador si se recuperaron los datos de las relaciones
	 *
	 * @var boolean
	 */
	private $model_got_relations = FALSE;

	/**
	 * Cantidad de registros por pagina
	 *
	 * @var integer
	 */
	private $model_page_results = 12;

	/**
	 * Filtro para buscar resultados
	 *
	 * @var string
	 */
	private $model_filtro = '';

	/**
	 * Arreglo con los campos del modelo
	 *
	 * @var array
	 */
	private $model_fields = array();

	/**
	 * Arreglo de registros recuperados de la BD
	 *
	 * @var array
	 */
	private $model_all = array();

	/**
	 * Arreglo de registros recuperados de la BD
	 *
	 * @var array
	 */
	private $fields_values = array();

	/**
	 * Arreglo temporal de objetos recuperados de la BD
	 *
	 * @var array
	 */
	private $fields_relation_objects = array();

	/**
	 * Caracter separador de los campos cuando la llave tiene más de un campo
	 *
	 * @var string
	 */
	private $separador_campos = '~';

	/**
	 * Instacia CI para llamar objetos globales
	 *
	 * @var object
	 */
	protected $CI;


	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('orm');

		$this->model_class  = get_class($this);
		$this->model_nombre = strtolower($this->model_class);
		$this->model_tabla  = $this->model_nombre;
		$this->model_label  = $this->model_nombre;
		$this->model_label_plural = $this->model_label . 's';
	}

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	public function config_model($param = array())
	{
		if (array_key_exists('modelo', $param))
		{
			$this->_config_modelo($param['modelo']);
		}

		if (array_key_exists('campos', $param))
		{
			$this->_config_campos($param['campos']);
		}

		$this->model_campo_id = $this->_determina_campo_id();
		//$this->_recuperar_relation_fields();
	}

	// --------------------------------------------------------------------

	/**
	 * __get
	 *
	 * Permite acceder a las clases de CI de forma directa
	 *
	 * @param	string
	 * @access private
	 */
	public function __get($key)
	{
		return (array_key_exists($key, $this->fields_values)) ? $this->fields_values[$key] : NULL;
	}

	// --------------------------------------------------------------------
	// Iterator methods
	// --------------------------------------------------------------------

	public function getIterator()
	{
		return new ArrayIterator($this->fields_values);
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades del modelo
	 *
	 * @param  array $cfg arreglo con la configuración del modelo
	 * @return nada
	 */
	private function _config_modelo($cfg = array())
	{
		foreach ($cfg as $key => $val)
		{
			if (isset($key))
			{
				$this->$key = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades de los campos del modelo
	 *
	 * @param  array $cfg arreglo con la configuración de los campos del modelo
	 * @return nada
	 */
	private function _config_campos($cfg = array())
	{
		foreach ($cfg as $campo => $prop)
		{
			$prop['tabla_bd'] = $this->model_tabla;
			$oField = new ORM_Field($campo, $prop);
			$this->model_fields[$campo] = $oField;
			$this->fields_values[$campo] = NULL;
		}
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	public function get_model_nombre()
	{
		return $this->model_nombre;
	}


	public function get_model_tabla()
	{
		return $this->model_tabla;
	}


	public function get_model_label()
	{
		return $this->model_label;
	}


	public function get_model_label_plural()
	{
		return $this->model_label_plural;
	}


	public function get_model_campo_id()
	{
		return $this->model_campo_id;
	}


	public function get_model_page_results()
	{
		return $this->model_page_results;
	}


	public function get_model_fields()
	{
		return $this->model_fields;
	}


	public function get_model_filtro()
	{
		return ($this->model_filtro == '_') ? '' : $this->model_filtro;
	}


	public function set_model_filtro($filtro = '')
	{
		$this->model_filtro = $filtro;
	}


	public function get_campos_listado()
	{
		return $this->campos_listado;
	}


	public function get_model_all()
	{
		return $this->model_all;
	}


	public function set_model_all($model_all = array())
	{
		$this->model_all = $model_all;
	}


	public function get_relation_object($campo = '')
	{
		$model_fields = $this->model_fields;
		$relation = $model_fields[$campo]->get_relation();

		return $relation['data'];
	}

	public function get_fields_values()
	{
		return $this->fields_values;
	}



	// --------------------------------------------------------------------

	/**
	 * determina y fija el campo id del modelo
	 *
	 * @return arreglo con nombre de los campos marcados como id
	 */
	private function _determina_campo_id()
	{
		$arr_key = array();

		foreach ($this->model_fields as $key => $metadata)
		{
			if ($metadata->get_es_id())
			{
				array_push($arr_key, $key);
			}
		}

		return $arr_key;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el valor del id del modelo
	 *
	 * @return string
	 */
	public function get_model_id()
	{
		$arr_id = array();

		foreach ($this->model_campo_id as $campo)
		{
			array_push($arr_id, $this->{$campo});
		}

		$model_id = implode($this->separador_campos, $arr_id);

		return ($model_id === '') ? null : $model_id;
	}

	// --------------------------------------------------------------------

	// =======================================================================
	// FUNCIONES DE FORMULARIOS
	// =======================================================================

	/**
	 * Ejecuta la validacion de los campos de formulario del modelo
	 *
	 * @return boolean Indica si el formulario fue validad OK o no
	 */
	public function valida_form()
	{
		foreach ($this->model_fields as $campo => $metadata)
		{
			$this->set_validation_rules_field($campo);
		}
		$this->CI->app_common->form_validation_config();

		return $this->CI->form_validation->run();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera la regla de validación para un campo, de acuerdo a la definición del campo
	 *
	 * @param string $campo Nombre del campo
	 */
	public function set_validation_rules_field($campo = '')
	{
		$field = $this->model_fields[$campo];

		$reglas = 'trim';
		$reglas .= ($field->get_es_obligatorio() and !$field->get_es_autoincrement()) ? '|required' : '';
		$reglas .= ($field->get_tipo() == 'int')  ? '|integer' : '';
		$reglas .= ($field->get_tipo() == 'real') ? '|numeric' : '';
		$reglas .= ($field->get_es_unico() AND ! $field->get_es_id())
			? '|edit_unique['. $this->model_tabla . '.' . $field->get_nombre_bd() . '.' .
				implode($this->separador_campos, $this->get_model_campo_id()) . '.' . $this->get_model_id() . ']'
			: '';

		$campo_rules = $campo;
		if ($field->get_tipo() == 'has_many')
		{
			$reglas = 'trim';
			$campo_rules = $campo . '[]';
		}

		$this->CI->form_validation->set_rules($campo_rules, ucfirst($this->get_label_field($campo)), $reglas);
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
	 * Devuelve string con item de formulario para el campo indicado
	 *
	 * @param  string $campo Nombre del campo para devolver el elemento de formulario
	 * @return string        Formulario
	 */
	public function form_item($campo = '')
	{
		$formulario_enviado = (boolean) (count($this->CI->input->post()) != 0);
		$item_error = $formulario_enviado ? '' : '';

		if ($campo != '')
		{
			$data = array(
				'item_error'    => form_has_error($campo) ? 'has-error' : $item_error,
				'item_label'    => ucfirst($this->get_label_field($campo)),
				'item_required' => $this->get_es_obligatorio_field($campo),
				'item_id'       => 'id_'.$campo,
				'item_form'     => $this->print_form_field($campo),
				'item_help'     => $this->get_texto_ayuda_field($campo),
			);

			return ($this->CI->load->view('orm/form_item', $data, TRUE));
		}
	}



	// --------------------------------------------------------------------

	/**
	 * Devuelve el formulario para el despliegue de un campo del modelo
	 *
	 * @param  string  $campo          Nombre del campo
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @return string        Elemento de formulario
	 */
	public function print_form_field($campo = '', $filtra_activos = FALSE, $param_adic = '')
	{
		// busca condiciones en la relacion a las cuales se les deba buscar un valor de filtro
		$arr_relation = $this->model_fields[$campo]->get_relation();

		if (array_key_exists('conditions', $arr_relation))
		{
			foreach ($arr_relation['conditions'] as $cond_key => $cond_value)
			{
				// si encontramos un valor que comience por @filed_value,
				// se reemplaza por el valor del campo en el objeto
				if (strpos($cond_value, '@field_value') === 0)
				{
					$arr_field_value = explode(':', $cond_value);
					$arr_relation['conditions'][$cond_key] = $this->{$arr_field_value[1]};
				}
			}
			$this->model_fields[$campo]->set_relation($arr_relation);
		}

		return $this->model_fields[$campo]->form_field($this->$campo, $filtra_activos, $param_adic);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el texto label de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Label del campo
	 */
	public function get_label_field($campo = '')
	{
		$tipo = $this->model_fields[$campo]->get_tipo();

		if (($tipo == 'has_one' OR $tipo == 'has_many') AND ! $this->model_got_relations)
		{
			$this->_recuperar_relation_fields();
		}

		return $this->model_fields[$campo]->get_label();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el texto de ayuda de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Texto de ayuda del campo
	 */
	public function get_texto_ayuda_field($campo = '')
	{
		return $this->model_fields[$campo]->get_texto_ayuda();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el valor de un campo del modelo
	 *
	 * @param  string  $campo     Nombre del campo
	 * @param  boolean $formatted Indica si se devuelve el valor formateado o no (ej. id de relaciones)
	 * @return string             Texto con el valor del campo
	 */
	public function get_valor_field($campo = '', $formatted = TRUE)
	{
		return ( ! $formatted) ? $this->$campo : $this->model_fields[$campo]->get_formatted_value($this->$campo);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la propiedad es_obligatorio de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return boolean       Indicador si el campo es obligatorio
	 */
	public function get_es_obligatorio_field($campo = '')
	{
		return $this->model_fields[$campo]->get_es_obligatorio();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve marca de campo obligatorio (*), si el campo así lo es
	 *
	 * @param  string $campo Nombre del campo
	 * @return string Texto html con la marca del campo
	 */
	public function get_marca_obligatorio_field($campo = '')
	{
		return $this->get_es_obligatorio_field($campo) ? ' <span class="text-danger">*</span>' : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve indicador si el campo se mostrará o no en listaoo
	 *
	 * @param string $campo Nombre del campo
	 * @return boolean Indicador mostrar o no en la lista
	 */
	public function get_mostrar_lista($campo = '')
	{
		return $this->model_fields[$campo]->get_mostrar_lista();
	}

	// --------------------------------------------------------------------

	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 *
	 * @param  string $filtro Filtro de los valores del modelo
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas()
	{
		$this->CI->load->library('pagination');
		$total_rows = $this->find('count', array('filtro' => $this->model_filtro), FALSE);

		$cfg_pagination = array(
			'uri_segment'     => 5,
			'num_links'       => 5,
			'full_tag_open'   => '<ul class="pagination">',
			'flil_tag_close'  => '</ul>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',

			'per_page'    => $this->model_page_results,
			'total_rows'  => $total_rows,
			'base_url'    => site_url(
				$this->CI->router->class . '/' .
				($this->CI->uri->segment(2) ? $this->CI->uri->segment(2) : 'listado') . '/' .
				$this->get_model_nombre() . '/' .
				$this->model_filtro . '/'
			),
			'first_link'  => $this->CI->lang->line('orm_pag_first'),
			'last_link'   => $this->CI->lang->line('orm_pag_last') . ' (' . (int)($total_rows / $this->model_page_results + 1) . ')',
			'prev_link'   => '<span class="glyphicon glyphicon-chevron-left"></span>',
			'next_link'   => '<span class="glyphicon glyphicon-chevron-right"></span>',
		);

		$this->CI->pagination->initialize($cfg_pagination);

		return $this->CI->pagination->create_links();
	}

	// --------------------------------------------------------------------

	// =======================================================================
	// FUNCIONES PUBLICAS DE CONSULTA EN BD
	// =======================================================================

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
	 * @return array                      Arreglo de resultados
	 */
	public function find($tipo = 'first', $param = array(), $recupera_relation = TRUE)
	{
		if (array_key_exists('conditions', $param))
		{
			foreach ($param['conditions'] as $campo => $valor)
			{
				if (is_array($valor))
				{
					if (count($valor) == 0)
					{
						$this->CI->db->where($campo.'=', 'NULL', FALSE);
					}
					else
					{
						$this->CI->db->where_in($campo, $valor);
					}
				}
				else
				{
					$this->CI->db->where($campo, $valor);
				}
			}
		}

		if (array_key_exists('filtro', $param))
		{
			if ($param['filtro'] != '_' AND $param['filtro'] != '')
			{
				$this->_put_filtro($param['filtro']);
			}
		}

		if (array_key_exists('limit', $param))
		{
			if (array_key_exists('offset', $param))
			{
				$this->CI->db->limit($param['limit'], $param['offset']);
			}
			else
			{
				$this->CI->db->limit($param['limit']);
			}
		}

		if ($tipo == 'first')
		{
			$rs = $this->CI->db->get($this->model_tabla)->row_array();
			$this->fill_from_array($rs);

			if ($recupera_relation)
			{
				$this->_recuperar_relation_fields();
			}

			return $rs;
		}
		else if ($tipo == 'all')
		{
			if ($this->model_order_by != '')
			{
				$this->CI->db->order_by($this->model_order_by);
			}

			$rs = $this->CI->db->get($this->model_tabla)->result_array();

			foreach ($rs as $reg)
			{
				$o = new $this->model_class();
				$o->fill_from_array($reg);

				if ($recupera_relation)
				{
					$o->_recuperar_relation_fields($this->fields_relation_objects);
					$this->_add_relation_field_data($o->_get_relation_field_data_to_array());
				}
				array_push($this->model_all, $o);
			}

			return $rs;
		}
		else if ($tipo == 'count')
		{
			return $this->CI->db
				->select('count(*) as cant')
				->get($this->model_tabla)
				->row()
				->cant;
		}
		else if ($tipo == 'list')
		{
			$arr_list = array();

			if ($this->model_order_by != '')
			{
				$this->CI->db->order_by($this->model_order_by);
			}

			$rs = $this->CI->db->get($this->model_tabla)->result_array();

			foreach ($rs as $reg)
			{
				$o = new $this->model_class();
				$o->fill_from_array($reg);
				$arr_list[$o->get_model_id()] = (string) $o;
			}

			return $arr_list;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera registros por el ID
	 *
	 * @param string $id Identificador del registro a recuperar
	 * @param boolean $recupera_relation Indica si se recuperará la información de relación
	 * @return
	 */
	public function find_id($id = '', $recupera_relation = TRUE)
	{
		$arr_condiciones = array();

		if (count($this->model_campo_id) == 1)
		{
			foreach ($this->model_campo_id as $campo_id)
			{
				$tipo_id = $this->model_fields[$campo_id]->get_tipo();

				if ($tipo_id == 'id' OR $tipo_id == 'integer')
				{
					$arr_condiciones[$campo_id] = (int) $id;
				}
				else
				{
					$arr_condiciones[$campo_id] = $id;
				}
			}
		}
		else
		{
			$arr_val_id = explode($this->separador_campos, $id);

			foreach ($this->model_campo_id as $i => $campo_id)
			{
				$arr_condiciones[$campo_id] = is_null($id) ? '' : $arr_val_id[$i];
			}
		}

		$this->find('first', array('conditions' => $arr_condiciones), $recupera_relation);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera listado paginado
	 *
	 * @return nada
	 */
	public function list_paginated($offset = 0)
	{
		$this->find('all', array(
			'filtro' => $this->model_filtro,
			'limit'  => $this->model_page_results,
			'offset' => $offset
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los modelos dependientes (de las relaciones has_one y has_many)
	 *
	 * @param  array  $arr_obj Arreglo de objetos recuperados anteriormente (evita hacer queries repetidas)
	 * @return nada
	 */
	private function _recuperar_relation_fields($arr_obj = array())
	{
		foreach ($this->model_fields as $nombre_campo => $obj_campo)
		{
			if($obj_campo->get_tipo() == 'has_one')
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $obj_campo->get_relation();
				$class_relacionado = $arr_props_relation['model'];

				// si el objeto a recuperar existe en el arreglo $arr_obj,
				// recupera el objeto del arreglo y no va a buscarlo a la BD
				if (array_key_exists($nombre_campo, $arr_obj) AND array_key_exists($this->$nombre_campo, $arr_obj[$nombre_campo]))
				{
					$model_relacionado = $arr_obj[$nombre_campo][$this->$nombre_campo];
				}
				else
				{
					$model_relacionado = new $class_relacionado();
					$model_relacionado->find_id($this->$nombre_campo, FALSE);
				}

				$arr_props_relation['data'] = $model_relacionado;
				$this->model_fields[$nombre_campo]->set_relation($arr_props_relation);
				$this->model_got_relations = TRUE;
			}
			else if ($obj_campo->get_tipo() == 'has_many')
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $obj_campo->get_relation();

				// genera arreglo where con la llave del modelo
				$arr_where = array();
				$arr_id_one_table = $arr_props_relation['id_one_table'];

				foreach ($this->model_campo_id as $campo_id)
				{
					$arr_where[array_shift($arr_id_one_table)] = $this->$campo_id;
				}

				// recupera la llave del modelo en tabla de la relacion (n:m)
				$rs = $this->CI->db
					->select($this->_junta_campos_select($arr_props_relation['id_many_table']), FALSE)
					->get_where($arr_props_relation['join_table'], $arr_where)
					->result_array();

				$class_relacionado = $arr_props_relation['model'];
				$model_relacionado = new $class_relacionado();

				// genera arreglo de condiciones de busqueda
				$arr_where = array();

				foreach ($rs as $reg)
				{
					array_push($arr_where, array_pop($reg));
				}
				$arr_condiciones = array($this->_junta_campos_select($model_relacionado->get_model_campo_id()) => $arr_where);

				$model_relacionado->find('all', array('conditions' => $arr_condiciones), FALSE);

				$arr_props_relation['data'] = $model_relacionado;
				$this->model_fields[$nombre_campo]->set_relation($arr_props_relation);
				$this->fields_values[$nombre_campo] = $arr_where;
				$this->model_got_relations = TRUE;
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los datos de las relaciones y los devuelve como arreglo
	 *
	 * @return array  Arreglo con los objetos recuperados
	 */
	private function _get_relation_field_data_to_array()
	{
		$arr = array();
		foreach ($this->model_fields as $nombre_campo => $obj_campo)
		{
			if($obj_campo->get_tipo() == 'has_one')
			{
				$arr[$nombre_campo][$this->$nombre_campo]=$this->get_relation_object($nombre_campo);
			}
		}

		return $arr;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los datos de las relaciones y los devuelve como arreglo
	 *
	 * @return nada
	 */
	private function _add_relation_field_data($arr = array())
	{
		foreach ($arr as $campo => $arr_valores)
		{
			if (! array_key_exists($campo, $this->fields_relation_objects))
			{
				$this->fields_relation_objects[$campo] = array();
			}

			foreach ($arr_valores as $key => $obj)
			{
				if (! array_key_exists($key, $this->fields_relation_objects[$campo]))
				{
					$this->fields_relation_objects[$campo][$key] = $obj;
				}
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve campos select de una lista para ser consulados como un solo
	 * campo de la base de datos
	 *
	 * @param  array  $arr_campos Arreglo con el listado de campos
	 * @return string             Listado de campos unidos por la BD
	 */
	private function _junta_campos_select($arr_campos = array())
	{
		if (count($arr_campos) == 1)
		{
			return array_pop($arr_campos);
		}
		else
		{
			if (ENVIRONMENT == 'development-mac')
			{
				// CONCAT_WS es especifico para MYSQL
				$lista_campos = implode(',', $arr_campos);

				return 'CONCAT_WS(\'' . $this->separador_campos . '\',' . $lista_campos . ')';
			}
			else
			{
				$lista_campos = implode(' + \'' . $this->separador_campos . '\' + ', $arr_campos);

				return $lista_campos;
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Puebla el modelo con datos de la base de datos o de un arreglo entregado
	 *
	 * @param  mixed $id ID del registro a recuperar o arreglo con datos
	 * @return none
	 */
	public function fill($id = null)
	{
		if ($id)
		{
			return is_array($id) ? $this->fill_from_array($id) : $this->find_id($id);
		}
	}



	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores de un arreglo
	 *
	 * @param  array  $rs Arreglo con los valores
	 * @return nada
	 */
	public function fill_from_array($rs = array())
	{
		if ($rs OR count($rs) > 0)
		{
			foreach ($this->model_fields as $nombre => $metadata)
			{
				if (array_key_exists($nombre, $rs))
				{
					$tipo_campo = $metadata->get_tipo();

					if ($tipo_campo == 'id' OR $tipo_campo == 'boolean' OR $tipo_campo == 'integer')
					{
						$this->fields_values[$nombre] = (int) $rs[$nombre];
					}

					if ($metadata->get_tipo() == 'datetime')
					{
						if ($rs[$nombre] != '')
						{
							$this->fields_values[$nombre] = date('Ymd H:i:s', strtotime($rs[$nombre]));
						}
					}
					else
					{
						$this->fields_values[$nombre] = $rs[$nombre];
					}
				}
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores del post
	 *
	 * @param  nada
	 * @return nada
	 */
	public function recuperar_post()
	{
		return $this->fill_from_array($this->CI->input->post());
	}


	// =======================================================================
	// FUNCIONES PRIVADAS DE CONSULTA EN BD
	// =======================================================================

	/**
	 * Agrega un filtro para seleccionar elementos del modelo
	 *
	 * @param  string $filtro Filtro para seleccionar elementos
	 * @return nada
	 */
	private function _put_filtro($filtro = '')
	{
		$arr_like = array();

		foreach ($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'char')
			{
				$arr_like[$nombre] = $filtro;
			}
		}

		if (count($arr_like) > 0)
		{
			$this->CI->db->or_like($arr_like, $filtro, 'both');
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve la cantidad de elementos del modelo, dado un filtro
	 *
	 * @param  string $filtro Filtro de los elementos del modelo
	 * @return int            Cantidad de elementos
	 */
	private function _get_all_count($filtro = '_')
	{
		if ($filtro != '_' && $filtro != '')
		{
			$this->_put_filtro($filtro);
		}

		return $this->CI->db->count_all_results($this->model_tabla);
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
		$data_update  = array();
		$data_where   = array();
		$es_auto_id   = FALSE;
		$es_insert    = FALSE;

		foreach ($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_es_id() and $campo->get_es_autoincrement())
			{
				$es_auto_id = TRUE;
			}

			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
			else
			{
				if ($campo->get_tipo() != 'has_many')
				{
					$data_update[$nombre] = $this->$nombre;
				}
			}
		}

		if ($es_auto_id)
		{
			foreach ($data_where as $key => $val)
			{
				$es_insert = ($val == '' || $val == 0) ? TRUE : FALSE;
			}
		}
		else
		{
			$es_insert = ($this->CI->db->get_where($this->model_tabla, $data_where)->num_rows() == 0);
		}

		// NUEVO REGISTRO
		if ($es_insert)
		{
			if (!$es_auto_id)
			{
				$this->CI->db->insert($this->model_tabla, array_merge($data_where, $data_update));
			}
			else
			{
				$this->CI->db->insert($this->model_tabla, $data_update);

				foreach ($this->model_campo_id as $campo_id)
				{
					$data_where[$campo_id] = $this->CI->db->insert_id();
					$this->{$campo_id} = $this->CI->db->insert_id();
				}
			}
		}
		// REGISTRO EXISTENTE
		else
		{
			$this->CI->db->where($data_where);
			$this->CI->db->update($this->model_tabla, $data_update);
		}

		// Revisa todos los campos en busqueda de relaciones has_many,
		// para actualizar la tabla relacionada
		foreach ($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();

				$arr_where_delete = array();
				$data_where_tmp = $data_where;

				foreach ($rel['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}

				$this->CI->db->delete($rel['join_table'], $arr_where_delete);

				foreach ($this->$nombre as $valor_campo)
				{
					$arr_values = array();

					$data_where_tmp = $data_where;

					foreach ($rel['id_one_table'] as $id_one_table_key)
					{
						$arr_values[$id_one_table_key] = array_shift($data_where_tmp);
					}

					$arr_many_valores = explode($this->separador_campos, $valor_campo);

					foreach ($rel['id_many_table'] as $id_many)
					{
						$arr_values[$id_many] = array_shift($arr_many_valores);
					}

					$this->CI->db->insert($rel['join_table'], $arr_values);
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Elimina el modelo de la base de datos
	 *
	 * @return nada
	 */
	public function borrar()
	{
		$data_where = array();

		foreach ($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}

		$this->CI->db->delete($this->model_tabla, $data_where);

		foreach ($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();
				$arr_where_delete = array();
				$data_where_tmp = $data_where;

				foreach ($rel['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}

				$this->CI->db->delete($rel['join_table'], $arr_where_delete);
			}
		}
	}
}





// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************

/**
 * Clase que modela un campo
 *
 *
 * @category	Controller
 * @author		dcr
 * @link
 */
class ORM_Field {

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
	 * @access  public
	 * @param   array   parametros de construccion
	 * @return  void
	 * @author  dcr
	 **/
	function __construct($nombre = '', $param = array()) {

		$this->CI =& get_instance();

		$this->nombre    = $nombre;
		$this->nombre_bd = $nombre;
		$this->label     = $nombre;

		if (is_array($param))
		{
			foreach ($param as $key => $val)
			{
				if (isset($key))
				{
					$this->$key = $val;
				}
			}
		}

		if ($this->tipo == 'int' and $this->default == '')
		{
			$this->default = 0;
		}

		if ($this->tipo == 'has_one')
		{
			$this->es_obligatorio = TRUE;
		}

		if ($this->tipo == 'datetime')
		{
			$this->largo = 20;
		}

		if ($nombre == 'id')
		{
			$this->tipo  = 'id';
			$this->largo = 10;

			$this->es_id            = TRUE;
			$this->es_obligatorio   = TRUE;
			$this->es_unico         = TRUE;
			$this->es_autoincrement = TRUE;
		}
	}


	public function get_tipo()
	{
		return $this->tipo;
	}


	public function get_label()
	{
		if ($this->tipo == 'has_one')
		{
			return $this->relation['data']->get_model_label();
		}

		if ($this->tipo == 'has_many')
		{
			return $this->relation['data']->get_model_label_plural();
		}

		return $this->label;
	}


	public function get_nombre_bd()
	{
		return $this->nombre_bd;
	}


	public function get_mostrar_lista()
	{
		return $this->mostrar_lista;
	}


	public function get_es_id()
	{
		return $this->es_id;
	}


	public function get_es_unico()
	{
		return $this->es_unico;
	}


	public function get_es_obligatorio()
	{
		return $this->es_obligatorio;
	}


	public function get_es_autoincrement()
	{
		return $this->es_autoincrement;
	}


	public function get_texto_ayuda()
	{
		return $this->texto_ayuda;
	}


	public function get_relation()
	{
		return $this->relation;
	}


	public function set_relation($arr)
	{
		$this->relation = $arr;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera el elemento de formulario para el campo
	 *
	 * @param  string $valor Valor a desplegar en el elemento de formulario
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @return string        Elemento de formulario
	 */
	public function form_field($valor = '', $filtra_activos = FALSE, $param_adic = '')
	{
		$id_prefix  = 'id_';

		$valor_field = ($valor === '' AND $this->default != '') ? $this->default : $valor;
		$valor_field = set_value($this->nombre, $valor_field);

		$arr_param = array(
			'name'      => $this->nombre,
			'id'        => $id_prefix . $this->nombre,
			'class'     => 'form-control ' . $param_adic,
			'value'     => $valor_field,
			'maxlength' => $this->largo,
			'size'      => $this->largo,
		);

		if ($this->tipo == 'id'
			OR ($this->es_id AND $valor_field != '')
			OR ($this->es_id AND $this->es_autoincrement))
		{
			$param_adic = ' id="' . $id_prefix . $this->nombre . '"';

			$form = '<p class="form-control-static">' . $valor_field . '</p>';
			$form .= form_hidden($this->nombre, $valor_field, $param_adic);

			return $form;
		}

		if ( ! empty($this->choices))
		{
			$param_adic = ' id="' . $id_prefix . $this->nombre . '" class="form-control"';

			return form_dropdown($this->nombre, $this->choices, $valor_field, $param_adic);
		}

		if ($this->tipo == 'char' AND $this->largo >= 100)
		{
			unset($arr_param['size']);
			$arr_param['cols'] = '50';
			$arr_param['rows'] = '5';

			return form_textarea($arr_param);
		}

		if ($this->tipo == 'char' OR $this->tipo == 'int' OR $this->tipo == 'datetime')
		{
			return form_input($arr_param);
		}

		if ($this->tipo == 'password')
		{
			return form_password($arr_param);
		}

		if ($this->tipo == 'real')
		{
			$arr_param['size'] = $this->largo + $this->decimales + 1;

			return form_input($arr_param);
		}

		if ($this->tipo == 'boolean')
		{
			$form = '<label class="radio-inline" for="' . $id_prefix . $this->nombre . '_1">';
			$form .= form_radio($this->nombre, 1, ($valor_field == 1) ? TRUE : FALSE, "id=" . $id_prefix . $this->nombre . "_1");
			$form .= $this->CI->lang->line('orm_radio_yes');
			$form .= '</label>';

			$form .= '<label class="radio-inline" for="' . $id_prefix . $this->nombre . '_0">';
			$form .= form_radio($this->nombre, 0, ($valor_field == 1) ? FALSE : TRUE, "id=" . $id_prefix . $this->nombre . "_0");
			$form .= $this->CI->lang->line('orm_radio_no');
			$form .= '</label>';

			return $form;
		}

		if ($this->tipo == 'has_one')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="' . $id_prefix . $this->nombre . '" class="form-control '. $param_adic . '"';

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

		if ($this->tipo == 'has_many')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();

			$param_adic = ' id="' . $id_prefix . $this->nombre . '" size="7" class="form-control ' . $param_adic . '"';

			$dropdown_conditions = array_key_exists('conditions', $this->relation)
				? array('conditions' => $this->relation['conditions'])
				: array();

			// Para que el formulario muestre multiples opciones seleccionadas, debemos usar este hack
			//$form = form_multiselect($this->nombre.'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
			$CI =& get_instance();
			$opciones = ($CI->input->post($this->nombre) != '') ? $CI->input->post($this->nombre) : $valor_field;

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
		if ($this->tipo == 'boolean')
		{
			return ($valor == 1) ? $this->CI->lang->line('orm_radio_yes') : $this->CI->lang->line('orm_radio_no');
		}

		if ($this->tipo == 'has_one')
		{
			return (string) $this->relation['data'];
		}

		if ($this->tipo == 'has_many')
		{
			$campo = '<ul class="formatted_has_many">';
			foreach ($this->relation['data']->get_model_all() as $obj)
			{
				$campo .= '<li>' . $obj . '</li>';
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
			if ($this->formato == 'monto,2')
			{
				return fmt_monto($valor,'UN','$',2);
			}
			else
			{
				return 'XX' . $valor . 'XX';
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
		if ($this->tipo == 'id')
		{
			return array(
				'type'           => 'INT',
				'constraint'     => $this->largo,
				'auto_increment' => $this->es_autoincrement,
				'null'           => $this->es_obligatorio,
			);
		}
		else if ($this->tipo == 'char')
		{
			return array(
				'type'       => 'VARCHAR',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo == 'text')
		{
			return array(
				'type'       => 'TEXT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo == 'integer')
		{
			return array(
				'type'       => 'INT',
				'constraint' => $this->largo,
				'default'    => $this->default,
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo == 'boolean')
		{
			return array(
				'type'       => 'BIT',
				'null'       => $this->es_obligatorio,
			);
		}
		else if ($this->tipo == 'datetime')
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