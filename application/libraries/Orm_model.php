<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category	Controller
 * @author		dcr
 * @link
 */
class ORM_Model {

	private $model_nombre        = '';
	private $model_class         = '';
	private $model_tabla         = '';
	private $model_label         = '';
	private $model_label_plural  = '';
	private $model_order_by      = '';
	private $model_campo_id      = '';
	private $model_got_relations = false;
	private $model_page_results  = 15;
	private $model_fields        = array();
	private $model_all           = array();

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	function __construct($param = array()) {

		//parent::__construct();

		$this->model_class  = get_class($this);
		$this->model_nombre = strtolower($this->model_class);
		$this->model_tabla  = $this->model_nombre;
		$this->model_label  = $this->model_nombre;
		$this->model_label_plural = $this->model_label . 's';

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

	public function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}

	/**
	 * Configura las propiedades del modelo
	 * @param  array $cfg arreglo con la configuración del modelo
	 * @return nada
	 */
	private function _config_modelo($cfg = array())
	{
		foreach($cfg as $key => $val)
		{
			if (isset($key))
			{
				$this->$key = $val;
			}
		}
	}


	/**
	 * Configura las propiedades de los campos del modelo
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
			$this->$campo = null;
		}
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	public function get_model_nombre()       { return $this->model_nombre; }

	public function get_model_tabla()        { return $this->model_tabla; }

	public function get_model_label()        { return $this->model_label; }

	public function get_model_label_plural() { return $this->model_label_plural; }

	public function get_model_campo_id()     { return $this->model_campo_id; }

	public function get_model_page_results() { return $this->model_page_results; }

	public function get_model_fields()       { return $this->model_fields; }

	public function get_campos_listado()     { return $this->campos_listado; }

	public function get_model_all()          { return $this->model_all; }


	/**
	 * determina y fija el campo id del modelo
	 * @return string nombre del campo id
	 */
	private function _determina_campo_id()
	{
		foreach ($this->model_fields as $key => $metadata)
		{
			if ($metadata->get_es_id())
			{
				return $key;
			}
		}
	}




	// =======================================================================
	// FUNCIONES DE FORMULARIOS
	// =======================================================================

	/**
	 * Ejecuta la validacion de los campos de formulario del modelo
	 * @return boolean Indica si el formulario fue validad OK o no
	 */
	public function valida_form()
	{
		foreach ($this->model_fields as $campo => $metadata)
		{
			$this->set_validation_rules_field($campo);
		}
		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para "%s"');
		$this->form_validation->set_message('greater_than', 'Seleccione un valor para "%s"');
		$this->form_validation->set_message('numeric', 'Ingrese un valor numérico para "%s"');
		$this->form_validation->set_message('integer', 'Ingrese un valor entero para "%s"');
		$this->form_validation->set_message('edit_unique', 'El valor del campo "%s" debe ser único');

		return $this->form_validation->run();
	}



	/**
	 * Genera la regla de validación para un campo, de acuerdo a la definición del campo
	 * @param string $campo Nombre del campo
	 */
	public function set_validation_rules_field($campo = '')
	{
		$field = $this->model_fields[$campo];

		$reglas = 'trim';
		$reglas .= ($field->get_es_obligatorio() and !$field->get_es_autoincrement()) ? '|required' : '';
		$reglas .= ($field->get_tipo() == 'int')  ? '|integer' : '';
		$reglas .= ($field->get_tipo() == 'real') ? '|numeric' : '';
		$reglas .= ($field->get_es_unico() AND !$field->get_es_id()) ? '|edit_unique['. $this->model_tabla . '.' . $field->get_nombre_bd() . '.' . $this->{$this->model_campo_id} . ']' : '';

		if ($field->get_tipo() == 'has_many')
		{
			$reglas = '';
		}

		$this->form_validation->set_rules($campo, ucfirst($field->get_label()), $reglas);
	}


	public function get_relation_fields()
	{
		$this->_recuperar_relation_fields();
	}


	/**
	 * Devuelve el formulario para el despliegue de un campo del modelo
	 * @param  string $campo Nombre del campo
	 * @return string        Elemento de formulario
	 */
	public function print_form_field($campo = '', $filtra_activos = FALSE)
	{
		return $this->model_fields[$campo]->form_field($this->$campo, $filtra_activos);
	}


	/**
	 * Devuelve el texto label de un campo del modelo
	 * @param  string $campo Nombre del campo
	 * @return string        Label del campo
	 */
	public function get_label_field($campo = '')
	{
		$tipo = $this->model_fields[$campo]->get_tipo();
		if(($tipo == 'has_one' or $tipo == 'has_many') and !($this->model_got_relations))
		{
			$this->_recuperar_relation_fields();

		}
		return $this->model_fields[$campo]->get_label();
	}


	/**
	 * Devuelve el texto de ayuda de un campo del modelo
	 * @param  string $campo Nombre del campo
	 * @return string        Texto de ayuda del campo
	 */
	public function get_texto_ayuda_field($campo = '')
	{
		return $this->model_fields[$campo]->get_texto_ayuda();
	}


	/**
	 * Devuelve el valor de un campo del modelo
	 * @param  string  $campo     Nombre del campo
	 * @param  boolean $formatted Indica si se devuelve el valor formateado o no (ej. id de relaciones)
	 * @return string             Texto con el valor del campo
	 */
	public function get_valor_field($campo = '', $formatted = TRUE)
	{
		if (!$formatted)
		{
			return $this->$campo;
		}
		else
		{
			return $this->model_fields[$campo]->get_formatted_value($this->$campo);
		}
	}


	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 * @param  string $filtro Filtro de los valores del modelo
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas($filtro = '_')
	{
		$this->load->library('pagination');
		$cfg_pagination = array(
					'uri_segment' => 5,
					'num_links'   => 5,
					'per_page'    => $this->model_page_results,
					'total_rows'  => $this->find('count', array('filtro' => $filtro), FALSE),
					'base_url'    => site_url('config2/listado/' . $this->get_model_nombre() . '/' . $filtro . '/'),
					'first_link'  => 'Primero',
					'last_link'   => 'Ultimo',
					'next_link'   => '<img src="'. base_url() . 'img/ic_right.png" />',
					'prev_link'   => '<img src="'. base_url() . 'img/ic_left.png" />',
				);
		$this->pagination->initialize($cfg_pagination);
		return $this->pagination->create_links();
	}



	// =======================================================================
	// FUNCIONES PUBLICAS DE CONSULTA EN BD
	// =======================================================================

	/**
	 * Recupera registros de la base de datos
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
						$this->db->where($campo, '');
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
		}

		if(array_key_exists('filtro', $param))
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
				$this->db->limit($param['limit'], $param['offset']);
			}
			else
			{
				$this->db->limit($param['limit']);
			}
		}

		if ($tipo == 'first')
		{
			$rs = $this->db->get($this->model_tabla)->row_array();
			$this->get_from_array($rs);

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
				$this->db->order_by($this->model_order_by);
			}
			$rs = $this->db->get($this->model_tabla)->result_array();

			foreach($rs as $reg)
			{
				$o = new $this->model_class();
				$o->get_from_array($reg);

				if ($recupera_relation)
				{
					$o->_recuperar_relation_fields();
				}

				array_push($this->model_all, $o);
			}

			return $rs;
		}
		else if ($tipo == 'count')
		{
			$this->db->select('count(*) as cant');
			$rs = $this->db->get($this->model_tabla)->row_array();
			return $rs['cant'];
		}
		else if ($tipo == 'list')
		{
			$arr_list = array();
			if ($this->model_order_by != '')
			{
				$this->db->order_by($this->model_order_by);
			}
			$rs = $this->db->get($this->model_tabla)->result_array();

			foreach($rs as $reg)
			{
				$o = new $this->model_class();
				$o->get_from_array($reg);
				$arr_list[$o->{$o->get_model_campo_id()}] = $o->__toString();
			}
			return $arr_list;
		}

	}

	public function find_id($id = 0)
	{
		$this->find('first', array('conditions' => array($this->model_campo_id => $id)));
	}



	/**
	 * Recupera los mdelos dependientes (de las relaciones has_one y has_many)
	 * @return nada
	 */
	private function _recuperar_relation_fields()
	{
		foreach($this->model_fields as $campo => $metadata)
		{
			if($metadata->get_tipo() == 'has_one')
			{
				$arr_rel = $metadata->get_relation();

				$class = $arr_rel['model'];
				$obj = new $class();
				$obj->find_id($this->$campo, FALSE);
				$arr_rel['data'] = $obj;

				$this->model_fields[$campo]->set_relation($arr_rel);

				$this->model_got_relations = TRUE;
			}
			if($metadata->get_tipo() == 'has_many')
			{
				$arr_rel = $metadata->get_relation();

				$arr_rs = array();
				$rs = $this->db->select($arr_rel['id_many_table'])->get_where($arr_rel['join_table'], array($arr_rel['id_one_table'] => $this->{$this->model_campo_id}))->result_array();
				foreach($rs as $val)
				{
					array_push($arr_rs, $val[$arr_rel['id_many_table']]);
				}

				$class = $arr_rel['model'];
				$obj = new $class();
				$obj->find('all', array('conditions' => array($obj->get_model_campo_id() => $arr_rs)), FALSE);

				$arr_rel['data'] = $obj;
				$this->model_fields[$campo]->set_relation($arr_rel);
				$this->$campo = $arr_rs;

				$this->model_got_relations = TRUE;
			}
		}
	}


	/**
	 * Puebla los campos del modelo con los valores de un arreglo
	 * @param  array  $rs Arreglo con los valores
	 * @return nada
	 */
	public function get_from_array($rs = array())
	{
		foreach($this->model_fields as $nombre => $metadata)
		{
			if (array_key_exists($nombre, $rs))
			{
				$this->$nombre = $rs[$nombre];
			}
		}
	}




	// =======================================================================
	// FUNCIONES PRIVADAS DE CONSULTA EN BD
	// =======================================================================

	/**
	 * Agrega un filtro para seleccionar elementos del modelo
	 * @param  string $filtro Filtro para seleccionar elementos
	 * @return nada
	 */
	private function _put_filtro($filtro = '')
	{
		$i = 0;
		foreach($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'char')
			{
				if ($i == 0)
				{
					$this->db->like($nombre, $filtro, 'both');
				}
				else
				{
					$this->db->or_like($nombre, $filtro, 'both');
				}
				$i++;
			}
		}
	}


	/**
	 * Puebla los campos del modelo con los valores del post
	 * @return nada
	 */
	public function recuperar_post()
	{
		foreach($this->model_fields as $nombre => $metadata)
		{
			// si el valor del post es un arreglo, transforma los valores a llaves del arreglo
			if (is_array($this->input->post($nombre)))
			{
				$arr = array();
				foreach($this->input->post($nombre) as $key => $val)
				{
					$arr[$val] = $val;
				}
				$this->$nombre = $arr;
			}
			else
			{
				$this->$nombre = $this->input->post($nombre);
			}
		}
	}


	/**
	 * Devuelve la cantidad de elementos del modelo, dado un filtro
	 * @param  string $filtro Filtro de los elementos del modelo
	 * @return int            Cantidad de elementos
	 */
	private function _get_all_count($filtro = '_')
	{
		if ($filtro != '_' && $filtro != '')
		{
			$this->db->select('count(*) as cant');
			$this->_put_filtro($filtro);
			$rs = $this->db->get($this->model_tabla)->row();
			return $rs->cant;
		}
		else
		{
			return $this->db->count_all($this->model_tabla);
		}
	}



	// =======================================================================
	// Funciones de interaccion modelo <--> base de datos
	// =======================================================================

	/**
	 * Graba el modelo en la base de datos (inserta o modifica, dependiendo si el modelo ya existe)
	 * @return nada
	 */
	public function grabar()
	{
		$data_update  = array();
		$data_where   = array();
		$es_auto_id   = FALSE;
		$es_insert    = FALSE;

		foreach($this->model_fields as $nombre => $campo)
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
			foreach($data_where as $key => $val)
			{
				$es_insert = ($val == '') ? TRUE : FALSE;
			}
		}
		else
		{
			$es_insert = ($this->db->get_where($this->model_tabla, $data_where)->num_rows() == 0);
		}

		if ($es_insert)
		{
			if (!$es_auto_id)
			{
				$this->db->insert($this->model_tabla, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->model_tabla, $data_update);
				$data_where[$this->model_campo_id] = $this->db->insert_id();
			}
		}
		else
		{
			$this->db->where($data_where);
			$this->db->update($this->model_tabla, $data_update);
		}

		foreach($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();
				foreach($data_where as $key => $val_key)
				{
					$this->db->delete($rel['join_table'], array($rel['id_one_table'] => $val_key));
					foreach($this->$nombre as $valor_campo)
					{
						$this->db->insert($rel['join_table'], array($rel['id_one_table'] => $val_key, $rel['id_many_table'] => $valor_campo));
					}
				}
			}
		}
	}


	/**
	 * Elimina el modelo de la base de datos
	 * @return nada
	 */
	public function borrar()
	{
		$data_where = array();
		foreach($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}
		$this->db->delete($this->model_tabla, $data_where);

		foreach($this->model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();
				foreach($data_where as $key => $val_key)
				{
					$this->db->delete($rel['join_table'], array($rel['id_one_table'] => $val_key));
				}
			}
		}
	}



	// ----------------------------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------------------------


	function valida_tabla_creada()
	{
		if (!$this->db->table_exists($this->tabla_bd))
		{
			foreach ($this->model_metadata as $campo)
			{
				if ($campo->get_nombre() == 'id')
				{
					$this->dbforge->add_field($campo->get_nombre());
				}
				else
				{
					$this->dbforge->add_field(array($campo->get_nombre() => $campo->get_bd_attrib()));
				}
			}

			$this->dbforge->drop_table($this->tabla_bd);
			$this->dbforge->create_table($this->tabla_bd);
		}
	}


	public function ___result_to_array($arr)
	{
		foreach($arr as $e)
		{
			$nom_clase = get_class($this);
			$o = new $nom_clase();
			foreach($e as $key => $value)
			{
				$o->$key = $value;
			}
			$this->result_array[] = $o;
		}
	}

	public function ___recuperar_choices($filtro = '_')
	{
		$this->recuperar_all($filtro, -1);
		foreach ($this->result_array as $key => $value)
		{
			$arr_choices[$value->fields[$value->id_field]->value] = $value->to_string();
		}
		return $arr_choices;
	}


}


// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************

class ORM_Field {
	private $nombre           = '';
	private $tabla_bd         = '';
	private $nombre_bd        = '';
	private $label            = '';
	private $texto_ayuda      = '';
	private $default          = '';

	private $tipo             = 'char';
	private $largo            = 10;
	private $decimales        = 2;
	private $choices          = array();
	private $relation         = array();

	private $es_id            = FALSE;
	private $es_obligatorio   = FALSE;
	private $es_unico         = FALSE;
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

		$this->nombre    = $nombre;
		$this->nombre_bd = $nombre;
		$this->label     = $nombre;

		if (is_array($param))
		{
			foreach($param as $key => $val)
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




	public function get_tipo()            { return $this->tipo; }

	public function get_label()
	{
		if ($this->tipo == 'has_one')
		{
			return $this->relation['data']->get_model_label();
		}
		else if ($this->tipo == 'has_many')
		{
			return $this->relation['data']->get_model_label_plural();
		}
		else
		{
			return $this->label;
		}
	}

	public function get_nombre_bd()        { return $this->nombre_bd; }

	public function get_es_id()            { return $this->es_id; }
	public function get_es_unico()         { return $this->es_unico; }
	public function get_es_obligatorio()   { return $this->es_obligatorio; }
	public function get_es_autoincrement() { return $this->es_autoincrement; }
	public function get_texto_ayuda()      { return $this->texto_ayuda; }

	public function get_relation()         { return $this->relation; }
	public function set_relation($arr)     { $this->relation = $arr; }

	// --------------------------------------------------------------------

	/**
	 * Genera el elemento de formulario para el campo
	 * @param  string $valor Valor a desplegar en el elemento de formulario
	 * @return string        Elemento de formulario
	 */
	public function form_field($valor = '', $filtra_activos = FALSE)
	{
		$form       = '';
		$id_prefix  = 'id_';
		$form_class = 'form_edit' . ((form_error($this->nombre) == '') ? '' : ' form_edit_error');

		$valor_field = ($valor === '' and $this->default != '') ? $this->default : $valor;
		$valor_field = set_value($this->nombre, $valor_field);

		if ($this->tipo == 'id' OR ($this->es_id AND $valor_field != ''))
		{
			$param_adic = ' id="' . $id_prefix . $this->nombre . '"';

			$form = $valor_field;
			$form .= form_hidden($this->nombre, $valor_field, $param_adic);
		}
		else if (!empty($this->choices))
		{
			$param_adic = ' id="' . $id_prefix . $this->nombre . '" class="' . $form_class . '"';
			$form = form_dropdown($this->nombre, $this->choices, $valor_field, $param_adic);
		}
		else if ($this->tipo == 'char')
		{
			if ($this->largo >= 100)
			{
				$arr_param = array(
									'name'      => $this->nombre,
									'value'     => $valor_field,
									'maxlength' => $this->largo,
									'id'        => $id_prefix . $this->nombre,
									'cols'      => '50',
									'rows'      => '5',
									'class'     => $form_class,
								);
				$form = form_textarea($arr_param);
			}
			else
			{
				$arr_param = array(
									'name'      => $this->nombre,
									'value'     => $valor_field,
									'maxlength' => $this->largo,
									'size'      => $this->largo,
									'id'        => $id_prefix . $this->nombre,
									'class'     => $form_class,
								);
				$form = form_input($arr_param);
			}
		}
		else if ($this->tipo == 'password')
		{
			$arr_param = array(
								'name'      => $this->nombre,
								'value'     => $valor_field,
								'maxlength' => $this->largo,
								'size'      => $this->largo,
								'id'        => $id_prefix . $this->nombre,
								'class'     => $form_class,
							);
			$form = form_password($arr_param);
		}
		else if ($this->tipo == 'int')
		{
			$arr_param = array(
								'name'      => $this->nombre,
								'value'     => $valor_field,
								'maxlength' => $this->largo,
								'size'      => $this->largo,
								'id'        => $id_prefix . $this->nombre,
								'class'     => $form_class,
							);
			$form = form_input($arr_param);
		}
		else if ($this->tipo == 'real')
		{
			$arr_param = array(
								'name'      => $this->nombre,
								'value'     => $valor_field,
								'maxlength' => $this->largo + $this->decimales + 1,
								'size'      => $this->largo + $this->decimales + 1,
								'id'        => $id_prefix . $this->nombre,
								'class'     => $form_class,
							);
			$form = form_input($arr_param);
		}
		else if ($this->tipo == 'datetime')
		{
			$arr_param = array(
								'name'      => $this->nombre,
								'value'     => $valor_field,
								'maxlength' => $this->largo,
								'size'      => $this->largo,
								'id'        => $id_prefix . $this->nombre,
								'class'     => $form_class,
							);
			$form = form_input($arr_param);
		}
		else if ($this->tipo == 'boolean')
		{
			$form  = form_radio($this->nombre, 1, ($valor_field == 1) ? true : false) . 'Si &nbsp; &nbsp; &nbsp; &nbsp;';
			$form .= form_radio($this->nombre, 0, ($valor_field == 1) ? false : true) . 'No';
		}
		else if ($this->tipo == 'has_one')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();
			$param_adic = ' id="' . $id_prefix . $this->nombre . '" class="' . $form_class . '"';
			$dropdown_conditions = (array_key_exists('conditions', $this->relation)) ? array('conditions' => $this->relation['conditions']) : array();
			$form = form_dropdown($this->nombre, $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
		}
		else if ($this->tipo == 'has_many')
		{
			$nombre_rel_modelo = $this->relation['model'];
			$modelo_rel = new $nombre_rel_modelo();
			$param_adic = ' id="' . $id_prefix . $this->nombre . '" size="7" class="' . $form_class . '"';

			$form = form_multiselect($this->nombre.'[]', $modelo_rel->find('list', array(), FALSE), $valor_field, $param_adic);
		}

		return $form;
	}


	/**
	 * Devuelve el valor, formateado de acuerdo al tipo del campo
	 * @param  [type] $valor Valor a desplegar
	 * @return string        Valor formateado
	 */
	public function get_formatted_value($valor)
	{
		if ($this->tipo == 'boolean')
		{
			return ($valor == 1) ? 'SI' : 'NO';
		}
		else if ($this->tipo == 'has_one')
		{
			return $this->relation['data']->__toString();
		}
		else if ($this->tipo == 'has_many')
		{
			$campo = '';
			foreach ($this->relation['data']->get_model_all() as $obj)
			{
				$campo .= $obj->__toString() . '<br>';
			}
			return $campo;
		}
		else if (count($this->choices) > 0)
		{
			return $this->choices[$valor];
		}
		else
		{
			return $valor;
		}
	}


	/**
	 * Devuelve los atributos para construir el campo en la base de datos
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