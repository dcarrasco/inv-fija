<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category	Controller
 * @author		dcr
 * @link
 */
class ORM_Model extends CI_Model {

	private $model_nombre       = '';
	private $model_class        = '';
	private $model_tabla        = '';
	private $model_label        = '';
	private $model_label_plural = '';
	private $model_order_by     = '';
	private $model_campo_id     = '';
	private $model_page_results = 10;
	private $model_fields       = array();
	private $model_all          = array();

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	function __construct($param = array()) {

		parent::__construct();

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

		$this->set_model_campo_id($this->_determina_campo_id());
		$this->_recuperar_relation_fields();


	}


	private function _config_modelo($cfg)
	{
		foreach($cfg as $key => $val)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $val;
			}
		}
	}

	private function _config_campos($cfg)
	{
		foreach ($cfg as $campo => $prop)
		{
			$oField = new ORM_Field($campo, $prop);
			$this->model_fields[$campo] = $oField;
			$this->$campo = null;
		}
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	public function get_model_nombre()              { return $this->model_nombre; }
	public function set_model_nombre($nombre = '')  { $this->model_nombre = $nombre; }

	public function get_model_class()               { return $this->model_class; }
	public function set_model_classs($class = '')   { $this->model_class = $class; }

	public function get_model_tabla()               { return $this->model_tabla; }
	public function set_model_tabla($tabla = '')    { $this->model_tabla = $tabla; }

	public function get_model_label()               { return $this->model_label; }
	public function set_model_label($label = '')    { $this->model_label = $label; }

	public function get_model_label_plural()             { return $this->model_label_plural; }
	public function set_model_label_plural($nombre = '') { $this->model_label_plural = $nombre; }

	public function get_model_order_by()               { return $this->model_order_by; }
	public function set_model_order_by($order_by = '') { $this->model_order_by = $order_by; }

	public function get_model_campo_id()            { return $this->model_campo_id; }
	public function set_model_campo_id($campo = '') { $this->model_campo_id = $campo; }

	public function get_model_page_results()        { return $this->model_page_results; }

	public function get_model_fields()              { return $this->model_fields; }

	public function get_campos_listado()            { return $this->campos_listado; }

	public function get_model_all()                 { return $this->model_all; }


	private function _determina_campo_id()
	{
		foreach ($this->get_model_fields() as $key => $metadata)
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

	public function valida_form()
	{
		foreach ($this->model_metadata as $campo => $metadata)
		{
			$this->form_validation->set_rules($campo, ucfirst($metadata->get_label()), $metadata->get_form_validation());
		}
		$this->form_validation->set_error_delimiters('<br/><div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');

		return $this->form_validation->run();
	}

	public function form_field($campo = '')
	{
		return $this->model_metadata[$campo]->form_field($this->$campo);
	}

	public function label_field($campo = '')
	{
		return $this->model_metadata[$campo]->get_label();
	}

	public function texto_ayuda_field($campo = '')
	{
		return $this->model_metadata[$campo]->get_texto_ayuda();
	}

	public function get_combo($glosa_seleccion = true)
	{
		$combo = array();

		if ($glosa_seleccion)
		{
			$combo[] = 'Seleccione un(a) ' . $this->get_model_label() . '...';
		}

		//determina el campo id
		$campo_id = '';
		foreach($this->get_model_metadata() as $key => $metadata)
		{
			if ($metadata->get_es_id())
			{
				$campo_id = $key;
			}
		}

		// llena los valores del combo
		$this->get_all('_', -1);
		foreach ($this->model_rs_list as $o)
		{
			$combo[$o->$campo_id] = $o->to_string();
		}

		return $combo;
	}

	public function print_campo($campo = '')
	{
		if ($this->model_fields[$campo]->get_tipo() == 'has_one')
		{
			$arr_rel = $this->model_fields[$campo]->get_relation();
			return $arr_rel['data']->__toString();
		}
		else
		{
			return $this->$campo;
		}
	}

	public function print_label_campo($campo = '')
	{
		if ($this->model_fields[$campo]->get_tipo() == 'has_one')
		{
			$arr_rel = $this->model_fields[$campo]->get_relation();
			return $arr_rel['data']->get_model_label();
		}
		else
		{
			return $this->model_fields[$campo]->get_label();
		}
	}

	// =======================================================================
	// FUNCIONES DE CONSULTA EN BD
	// =======================================================================

	public function get_all($filtro = '_', $pag = 0)
	{
		if ($filtro != '_' && $filtro != '')
		{
			$this->_put_filtro($filtro);
		}
		if ($pag != -1)
		{
			$this->db->limit($this->get_model_page_results(), $pag);
		}

		$this->db->order_by($this->get_model_order_by());
		$rs = $this->db->get($this->get_model_tabla())->result_array();
		foreach($rs as $reg)
		{
			$o = new $this->model_class;
			$o->recuperar_array($reg);
			$o->_recuperar_relation_fields();
			array_push($this->model_all, $o);
		}
	}

	public function get_id($id = '')
	{
		$rs = $this->db->get_where($this->get_model_tabla(), array($this->get_model_tabla() . '.' . $this->get_model_campo_id() => $id))->row_array();
		if (count($rs) > 0)
		{
			foreach ($rs as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}

	private function _recuperar_relation_fields()
	{
		foreach($this->model_fields as $campo => $metadata)
		{
			if($metadata->get_tipo() == 'has_one')
			{
				$arr_rel = $metadata->get_relation();
				$class = $arr_rel['model'];
				$obj = new $class;
				$obj->get_id($this->$campo);

				$arr_rel = $this->model_fields[$campo]->get_relation();
				$arr_rel['data'] = $obj;
				$this->model_fields[$campo]->set_relation($arr_rel);
			}
		}
	}



	/*
	private function _get_select_fields()
	{
		$arr_fields = array();
		foreach($this->model_fields as $key => $metadata)
		{
			if ($metadata->get_tipo() == 'has_one')
			{
				$relation = $metadata->get_relation();
				$this->db->join($relation['rel_table'], $this->get_model_tabla() . '.' . $relation['local_id'] . '=' . $relation['rel_table'] . '.' . $relation['external_id'], 'left');
				array_push($arr_fields, $relation['rel_table'] . '.' . $key);
				array_push($arr_fields, $this->get_model_tabla() . '.' . $relation['local_id']);
			}
			else if ($metadata->get_tipo() == 'has_many')
			{

			}
			else
			{
				array_push($arr_fields, $this->get_model_tabla() . '.' . $key);
			}
		}
		return implode(',', $arr_fields);
	}
	 */

	public function recuperar_choices($filtro = '_')
	{
		$this->recuperar_all($filtro, -1);
		foreach ($this->result_array as $key => $value)
		{
			$arr_choices[$value->fields[$value->id_field]->value] = $value->to_string();
		}
		return $arr_choices;
	}


	private function _put_filtro($filtro)
	{
		$i = 0;
		foreach($this->model_fields as $nombre => $campo)
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

	public function result_to_array($arr)
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





	public function recuperar_post()
	{
		foreach($this->model_metadata as $nombre => $metadata)
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
			else if ($this->input->post($nombre) != "")
			{
				$this->$nombre = $this->input->post($nombre);
			}
		}
	}

	public function recuperar_array($rs = array())
	{
		foreach($this->model_fields as $nombre => $metadata)
		{
			if (array_key_exists($nombre, $rs))
			{
				$this->$nombre = $rs[$nombre];
			}
		}
	}

	public function get_all_count($filtro = '_')
	{
		if ($filtro != '_' && $filtro != '')
		{
			$this->db->select('count(*) as cant');
			$this->_put_filtro($filtro);
			$rs = $this->db->get($this->get_model_tabla())->row();
			return $rs->cant;
		}
		else
		{
			return $this->db->count_all($this->get_model_tabla());
		}
	}


	public function crea_links_paginas($filtro = '_')
	{
		$this->load->library('pagination');
		$cfg_pagination = array(
					'uri_segment' => 5,
					'num_links'   => 5,
					'per_page'    => $this->get_model_page_results(),
					'total_rows'  => $this->get_all_count($filtro),
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
	// Funciones de interaccion modelo <--> base de datos
	// =======================================================================

	public function grabar()
	{
		$data_update  = array();
		$data_where   = array();
		$id_en_insert = true;
		$es_insert    = false;

		foreach($this->model_metadata as $nombre => $campo)
		{
			if ($campo->get_es_id() and $campo->get_es_autoincremet())
			{
				$id_en_insert = false;
			}

			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
			else
			{
				$data_update[$nombre] = $this->$nombre;
			}
		}

		if ($id_en_insert)
		{
			foreach($data_where as $key => $val)
			{
				$es_insert = ($val == '') ? true : false;
			}
		}
		else
		{
			$es_insert = ($this->db->get_where($this->get_model_tabla(), $data_where)->num_rows() == 0);
		}

		if ($es_insert)
		{
			if ($id_en_insert)
			{
				$this->db->insert($this->get_model_tabla(), array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->get_model_tabla(), $data_update);
			}
		}
		else
		{
			$this->db->where($data_where);
			$this->db->update($this->get_model_tabla(), $data_update);
		}
	}

	public function borrar()
	{
		$data_where = array();
		foreach($this->model_metadata as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}
		$this->db->delete($this->get_model_tabla(), $data_where);
	}

	// --------------------------------------------------------------------

	/**
	 * Representacion string del modelo
	 *
	 * @access  public
	 * @return  string  representacion string del modelo
	 * @author  dcr
	 **/
	function to_string()
	{
		$s = '';
		foreach($this->fields as $f)
		{
			$s .= $f->value . '-';
		}
		return $s;
	}

	// --------------------------------------------------------------------

	/**
	 * Formulario utilizado para la vista de detalle
	 *
	 * @access  public
	 * @return  array  arreglo con formulario de detalle
	 * @author  dcr
	 **/
	function detail_form()
	{
		$form_array = array();
		foreach ($this->fields as $field)
		{
			$form_array[$field->nombre] = array(
				'nombre'     => $field->nombre,
				'form_field' => $field->form_field()
				);
		}
		return $form_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Chequea si la tabla del modelo esta creada en la base de datos, si no, la crea
	 *
	 * @access  public
	 * @return  voif
	 * @author  dcr
	 **/
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




}


// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************

class ORM_Field {
	private $nombre        = '';
	private $nombre_bd     = '';
	private $label         = '';
	private $texto_ayuda   = '';
	private $default       = '';

	private $tipo           = 'char';
	private $largo          = 20;
	private $max_digits     = 10;
	private $decimal_places = 2;
	private $choices        = array();
	private $relation       = array();

	private $es_id            = false;
	private $es_obligatorio   = false;
	private $es_unico         = false;
	private $es_autoincrement = false;


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
				if (property_exists($this, $key))
				{
					$this->$key = $val;
				}
			}
		}

		if ($this->tipo == 'int' and $this->default == '')
		{
			$this->default = 0;
		}

		if ($nombre == 'id')
		{
			$this->tipo  = 'id';
			$this->largo = 10;

			$this->es_id            = true;
			$this->es_obligatorio   = true;
			$this->es_unico         = true;
			$this->es_autoincrement = true;
		}


	}

	public function get_tipo()            { return $this->tipo; }
	public function get_label()           { return $this->label; }
	public function get_es_id()           { return $this->es_id; }
	public function get_es_obligatorio()  { return $this->es_obligatorio; }
	public function get_es_autoincremet() { return $this->es_autoincrement; }
	public function get_texto_ayuda()     { return $this->texto_ayuda; }

	public function get_relation()        { return $this->relation; }
	public function set_relation($arr)    { $this->relation = $arr; }

	// --------------------------------------------------------------------

	/**
	 * Genera texto para desplegar un campo en un formulario html
	 *
	 * @access  public
	 * @return  string   texto para desplegar el formulario
	 * @author  dcr
	 **/
	function form_field($valor = '')
	{
		$form = '';
		$valor_field = ($valor == '' and $this->default != '') ? $this->default : $valor;

		if (!empty($this->choices) and $this->tipo != 'has_many')
		{
			$param_adic = ' id="id_' . $this->nombre . '"';
			$form = form_dropdown($this->nombre, $this->choices, $valor_field, $param_adic);
		}
		else if (!empty($this->choices) and $this->tipo == 'has_many')
		{
			$param_adic = ' id="id_' . $this->nombre . '" size="5"';
			$form = form_multiselect($this->nombre . '[]', $this->choices, $valor_field, $param_adic);
			/*
			$form = '<div class="scroll_checkboxes">';
			$lin = 0;
			foreach($this->choices as $val => $choice)
			{
				$form .= ($lin++ > 0) ? '<br>' : '';
				$form .= form_checkbox($this->nombre . '[]', $val, array_key_exists($val, $this->value));
				$form .= ' ' . $choice;
			}
			$form .= "</div>";
			*/
		}
		else if ($this->tipo == 'char')
		{
			$param_adic = ' maxlength="' . $this->largo . '"';
			$param_adic .= ' size="' . $this->largo . '"';
			$param_adic .= ' id=id_"' . $this->nombre . '"';
			//$param_adic .= ($this->is_editable) ? '' : ' readonly="readonly"';
			$form = form_input($this->nombre, $valor_field, $param_adic);
		}
		else if ($this->tipo == 'password')
		{
			$param_adic = ' maxlength="' . $this->max_length . '"';
			$param_adic .= ' id="id_' . $this->nombre . '"';
			$param_adic .= ($this->is_editable) ? '' : ' readonly="readonly"';
			$form = form_password($this->nombre, $valor_field, $param_adic);
		}
		else if ($this->tipo == 'datetime')
		{
			$param_adic = ' maxlength="' . $this->max_length . '"';
			$param_adic .= ' id="id_' . $this->nombre . '"';
			$param_adic .= ($this->is_editable) ? '' : ' readonly="readonly"';
			$form = form_input($this->nombre, $valor_field, $param_adic);
		}
		else if ($this->tipo == 'integer')
		{
			$param_adic = ' maxlength="' . $this->largo . '"';
			$param_adic .= ' id="id_' . $this->nombre . '"';
			//$param_adic .= ($this->is_editable) ? '' : ' readonly="readonly"';
			$form = form_input($this->nombre, $valor_field, $param_adic);
		}
		else if ($this->tipo == 'boolean')
		{
			$form  = form_radio($this->nombre, 1, ($valor_field==1) ? true : false) . 'Si &nbsp; &nbsp; &nbsp; &nbsp;';
			$form .= form_radio($this->nombre, 0, ($valor_field==1) ? false : true) . 'No';
		}
		else if ($this->tipo == 'has_one')
		{
			$nombre_rel_modelo = $this->relation['rel_modelo'];
			$modelo_rel = new $nombre_rel_modelo;
			$param_adic = ' id="id_' . $this->nombre . '"';
			$form = form_dropdown($this->nombre, $modelo_rel->get_combo(), $valor_field, $param_adic);
		}
		else if ($this->tipo == 'has_many')
		{
			$modelo_join = $this->relation['modelo'];
			$CI =& get_instance();
			$CI->load->model($modelo_join);
			$param_adic = ' id="id_' . $this->nombre . '" size ="5"';
			$form = form_multiselect($this->nombre, $CI->$modelo_join->get_combo(false), $valor_field, $param_adic);
		}
		else if ($this->tipo == 'id')
		{
			$param_adic = ' id=id_"' . $this->nombre . '"';
			$form = $valor;
			$form .= form_hidden($this->nombre, $valor, $param_adic);
		}
		return $form;
	}

	public function get_form_validation()
	{
		$regla = 'trim';
		$regla .= ($this->es_obligatorio == true and $this->es_autoincrement == false) ? '|required' : '';
		return $regla;
	}

	function get_bd_attrib()
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

/* End of file Someclass.php */