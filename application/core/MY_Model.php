<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category	Controller
 * @author		dcr
 * @link		
 */
class MY_Model extends CI_Model {

	protected $nombre          = '';
	protected $tabla_bd        = '';
	protected $label           = '';
	protected $order_by        = ''; 
	protected $lineas_pagina   = 15;
	protected $metadata        = array();
	protected $result_array    = array();

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	function __construct() 
	{
		parent::__construct();
		$this->load->dbforge();

		$this->nombre = get_class($this);

		if ($this->tabla_bd == '')
		{
			$this->tabla_bd = $this->nombre;
		}

		if ($this->label == '')
		{
			$this->label = $this->nombre;
		}
	}
	

	public function def_campos($param = array())
	{
		foreach ($param as $nom_campo => $def_campo) 
		{
			$oField = new ORM_Field(array_merge($def_campo, array('nombre' => $nom_campo)));
			$this->metadata[$nom_campo] = $oField;
			$this->$nom_campo = '';
		}
	}

	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	public function set_order_by($order_by = '')
	{
		$this->order_by = $order_by;
	}

	public function get_nombre()  
	{
		return $this->nombre;
	}

	public function get_tabla_bd()
	{
		return $this->get_tabla_bd;
	}

	public function get_verbose_name()  
	{
		return $this->verbose_name;
	}

	public function get_verbose_name_plural()  
	{
		return $this->verbose_name_plural;
	}

	public function get_max_results()  
	{
		return $this->max_results;
	}

	public function get_campos_metadata()  
	{
		return $this->campos_metadata;
	}

	public function get_result_array()  
	{
		return $this->result_array;
	}


	public function crear_arr_validacion()
	{
		$rules = array();
		foreach ($this->campos_metadata as $campo)
		{
			$c_rule  = array();
			$c_rule['field'] = $campo->get_nombre();
			$c_rule['label'] = ucfirst($campo->get_verbose_name());
			$c_rule['rules'] = $campo->get_form_validation();
			array_push($rules, $c_rule);
		}
		return $rules;
	}


	// ========================================================================
	// ========================================================================
	//
	// FUNCIONES DE BASE DE DATOS
	//
	// ========================================================================
	// ========================================================================

	public function find_all($pag = 0)
	{
		if ($this->order_by != '')
		{
			$this->db->order_by($this->order_by);
		}

		if ($pag == -1) 
		{
			return $this->db->get($this->tabla_bd)->result();
		}
		else 
		{
			return $this->db->get($this->tabla_bd, $this->lineas_pagina, $pag)->result();
		}
		
	}


	public function find($id = 0)
	{
		$arr_where = array();
		foreach ($this->metadata as $campo => $def_campo)
		{
			if ($def_campo->get_es_id())
			{
				$arr_where[$campo] = $id;
			}
		}

		$this->rs_to_object($this->db->get_where($this->tabla_bd, $arr_where)->row());
	}


	public function rs_to_object($rs)
	{
		foreach ($rs as $key => $value) 
		{
			$this->$key = $value;
		}
	}


	public function count_all()
	{
		return $this->db->count_all($this->tabla_bd);
	}


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
		foreach($this->campos_metadata as $nombre => $campo)
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


	public function recuperar($id = '') 
	{
		if ($id != '')
		{
			$rs = $this->db->get_where($this->tabla_bd, array('id' => $id))->row();
			foreach($rs as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}


	public function recuperar_post() 
	{
		foreach($this->campos_metadata as $nombre => $metadata)
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


	function recuperar_all_count($filtro = '_') 
	{
		if ($filtro != '_' && $filtro != '')
		{
			$this->db->select('count(*) as cant');
			$this->_put_filtro($filtro);
			$rs = $this->db->get($this->tabla_bd)->row();
			return $rs->cant;
		}
		else
		{
			return $this->db->count_all($this->tabla_bd);
		}
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

		foreach($this->campos_metadata as $nombre => $campo)
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
			$es_insert = ($this->db->get_where($this->tabla_bd, $data_where)->num_rows() == 0);
		}

		if ($es_insert)
		{
			if ($id_en_insert)
			{
				$this->db->insert($this->tabla_bd, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->tabla_bd, $data_update);
			}
		}
		else
		{
			$this->db->where($data_where);
			$this->db->update($this->tabla_bd, $data_update);
		}
	}

	function borrar()
	{
		$data_where = array();
		foreach($this->campos_metadata as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}


		$this->db->delete($this->tabla_bd, $data_where);
	}
	




	// ========================================================================
	// ========================================================================
	//
	// FUNCIONES DE PAGINACION DE DATOS
	//
	// ========================================================================
	// ========================================================================

	public function config_pagination($param = array())
	{
		$config = array(
			'total_rows'  => $this->count_all(),
			'per_page'    => $this->lineas_pagina,
			'num_links'   => 5,
			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo',
			'next_link'   => '<img src="'. base_url() . 'img/ic_right.png" />',
			'prev_link'   => '<img src="'. base_url() . 'img/ic_left.png" />',
			);
		$this->pagination->initialize(array_merge($config, $param));
	}

	public function links_paginas()
	{
		return $this->pagination->create_links();
	}

	// ========================================================================
	// ========================================================================
	//
	// FUNCIONES DE FORMULARIOS
	//
	// ========================================================================
	// ========================================================================

	public function exec_form_validation() 
	{
		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		foreach ($this->metadata as $campo => $def_campo)
		{
			$this->form_validation->set_rules($campo, $def_campo->get_label(), $def_campo->get_form_validation());
		}
		return $this->form_validation->run();
	}


	public function create_formfields()
	{
		$arr_formfield = array();
		foreach ($this->metadata as $campo => $def_campo) 
		{
			$arr_formfield[$campo] = array(
				'label' => $def_campo->get_label(),
				'form'  => $def_campo->get_form_field($this->$campo),
				'help'  => $def_campo->get_texto_ayuda(),
				'error' => form_error($this->$campo),
				);
		}
		return $arr_formfield;
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
			foreach ($this->campos_metadata as $campo)
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

class ORM_Field2 {
	protected $nombre        = '';
	protected $campo_bd      = '';
	protected $label         = '';
	protected $texto_ayuda   = '';
	protected $default       = '';

	protected $tipo           = 'char';
	protected $largo          = 20;
	protected $max_digits     = 10;
	protected $decimal_places = 2;
	protected $enum_array     = array();
	protected $rel            = array();
	
	protected $es_id            = false;
	protected $es_obligatorio   = false;
	protected $es_unico         = false;
	protected $es_autoincrement = false;
	
	
	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @access  public
	 * @param   array   parametros de construccion
	 * @return  void
	 * @author  dcr
	 **/
	function __construct($param = array()) {
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

		if ($this->campo_bd == '')
		{
			$this->campo_bd = $this->nombre;
		}

		if ($this->label == '') 
		{
			$this->label = $this->nombre;
		}

		if ($this->tipo == 'int' and $this->default == '') 
		{
			$this->default = 0;
		}

		if ($this->nombre == 'id')
		{
			$this->tipo  = 'id';
			$this->largo = 10;
			$this->es_id = true;
			$this->es_autoincrement = true;
			$this->es_obligatorio   = true;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Genera texto para desplegar un campo en un formulario html
	 *
	 * @access  public
	 * @return  string   texto para desplegar el formulario
	 * @author  dcr
	 **/
	function get_form_field($valor)
	{
		$form = '';
		$valor_field = ($valor == '' and $this->default != '') ? $this->default : $valor;

		if ($this->tipo == 'enum')
		{
			$param_adic = ' id="id_' . $this->nombre . '"';
			$form = form_dropdown($this->nombre, $this->enum_array, $valor_field, $param_adic);
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
		else if ($this->tipo == 'id')
		{
			$param_adic = ' id=id_"' . $this->nombre . '"';
			$form = $valor;
			$form .= form_hidden($this->nombre, $valor, $param_adic);
		}
		return $form;
	}

	public function get_form_error()
	{

	}

	public function get_tipo()
	{
		return $this->tipo;
	}

	public function get_nombre()
	{
		return $this->nombre;
	}

	public function get_label()
	{
		return $this->label;
	}

	public function get_es_id()
	{
		return $this->es_id;
	}

	public function get_es_obligatorio()
	{
		return $this->es_obligatorio;
	}

	public function get_es_autoincremet()
	{
		return $this->es_autoincrement;
	}

	public function get_texto_ayuda()
	{
		return $this->texto_ayuda;
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