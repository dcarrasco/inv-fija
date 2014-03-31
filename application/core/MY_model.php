<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category	Controller
 * @author		dcr
 * @link
 */
class MY_Model {

	/**
	 * Nombre del modelo
	 *
	 * @var  string
	 */
	protected $nombre = '';

	/**
	 * Clase del modelo
	 *
	 * @var  string
	 */
	protected $clase = '';

	/**
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	protected $tabla_bd = '';

	/**
	 * Nombre (etiqueta) del modelo
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Nombre (etiqueta) del modelo (plural)
	 *
	 * @var string
	 */
	protected $label_plural = '';

	/**
	 * Campos para ordenar el modelo cuando se recupera de la BD
	 *
	 * @var string
	 */
	protected $orden = '';

	/**
	 * Campos que conforman la llave (id) del modelo
	 *
	 * @var array
	 */
	protected $key = array();

	/**
	 * Indicador si se recuperaron los datos de las relaciones
	 *
	 * @var boolean
	 */
	protected $model_got_relations = FALSE;

	/**
	 * Cantidad de registros por pagina
	 *
	 * @var integer
	 */
	protected $page_results = 10;

	/**
	 * Arreglo con los campos del modelo
	 *
	 * @var array
	 */
	protected $field_info = array();

	/**
	 * Arreglo con los valores por defecto de un campo
	 */
	private $field_default = array(
								'nombre'             => '',
								'tabla_bd'           => '',
								'campo_bd'           => '',
								'label'              => '',
								'texto_ayuda'        => '',
								'default'            => '',
								'mostrar_en_listado' => TRUE,
								'tipo'               => 'CHAR',
								'largo'              => 10,
								'decimales'          => 2,
								'choices'            => array(),
								'relation'           => array(),
								'es_id'              => FALSE,
								'es_obligatorio'     => FALSE,
								'es_unico'           => FALSE,
								'es_autoincrement'   => FALSE,
							);

	/**
	 * Caracter separador de los campos cuando la llave tiene más de un campo
	 *
	 * @var string
	 */
	protected $separador_campos = '~';

	/**
	 * Arreglo de valores del objeto
	 *
	 * @var array
	 */
	protected $valores = array();

	/**
	 * Arreglo de registros recuperados de la BD
	 *
	 * @var array
	 */
	protected $model_all = array();



	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 **/
	public function __construct($param = array())
	{
		$this->clase  = ($this->clase == '') ? get_class($this): $this->clase;
		$this->nombre = ($this->nombre == '') ? ((strpos($this->clase, '_') === FALSE ) ? strtolower($this->clase) : strtolower(substr($this->clase, 0, strpos($this->clase, '_')))) : $this->nombre;
		$this->tabla_bd  = ($this->tabla_bd == '') ? $this->nombre : $this->tabla_bd;
		$this->label  = ($this->label == '') ? ucfirst($this->nombre) : $this->label;
		$this->label_plural = ($this->label_plural == '') ? $this->label . 's' : $this->label_plural;

		// completa la información de los campos
		foreach ($this->field_info as $campo => $metadata)
		{

			$this->valores[$campo] = null;

			foreach ($this->field_default as $attr => $valor)
			{
				if (!array_key_exists($attr, $metadata))
				{
					$this->field_info[$campo][$attr] = $valor;
				}
			}

			if ($this->field_info[$campo]['nombre'] == '')
			{
				$this->field_info[$campo]['nombre'] = $campo;
			}

			if ($this->field_info[$campo]['campo_bd'] == '')
			{
				$this->field_info[$campo]['campo_bd'] = $campo;
			}

			if ($this->field_info[$campo]['label'] == '')
			{
				$this->field_info[$campo]['label'] = $campo;
			}

			if ($this->field_info[$campo]['tabla_bd'] == '')
			{
				$this->field_info[$campo]['tabla_bd'] = $this->tabla_bd;
			}

			if ($this->field_info[$campo]['tipo'] == 'INT' and $this->field_info[$campo]['default'] == '')
			{
				$this->field_info[$campo]['default'] = 0;
			}

			if ($this->field_info[$campo]['tipo'] == 'HAS_ONE')
			{
				$this->field_info[$campo]['es_obligatorio'] = TRUE;
			}

			if ($this->field_info[$campo]['tipo'] == 'DATETIME')
			{
				$this->field_info[$campo]['largo'] = 20;
			}

			if ($this->field_info[$campo]['nombre'] == 'id')
			{
				$this->field_info[$campo]['tipo']             = 'ID';
				$this->field_info[$campo]['largo']            = 10;
				$this->field_info[$campo]['es_id']            = TRUE;
				$this->field_info[$campo]['es_obligatorio']   = TRUE;
				$this->field_info[$campo]['es_unico']         = TRUE;
				$this->field_info[$campo]['es_autoincrement'] = TRUE;
			}

		}

		$this->key = $this->_determina_campo_id();
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
		$CI =& get_instance();
		return $CI->$key;
	}

	// --------------------------------------------------------------------



	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================
	public function get_nombre()         { return $this->nombre; }
	public function get_tabla_bd()       { return $this->tabla_bd; }
	public function get_label()          { return $this->label; }
	public function get_label_plural()   { return $this->label_plural; }
	public function get_key()            { return $this->key; }
	public function get_page_results()   { return $this->page_results; }
	public function get_field_info()     { return $this->field_info; }
	public function get_campos_listado() { return $this->campos_listado; }
	public function get_valores()        { return $this->valores; }
	public function get_model_all()      { return $this->model_all; }


	public function set_model_all($model_all = array())
	{
		$this->model_all = $model_all;
	}


	public function get_relation_object($campo = '')
	{
		$field_info = $this->field_info;
		$relation = $field_info[$campo]->get_relation();
		return $relation['data'];
	}



	// --------------------------------------------------------------------

	/**
	 * determina y fija el campo id del modelo
	 * @return arreglo con nombre de los campos marcados como id
	 */
	private function _determina_campo_id()
	{
		$arr_key = array();

		foreach ($this->field_info as $campo => $metadata)
		{
			if ($this->campo_es_id($campo))
			{
				array_push($arr_key, $campo);
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
		foreach ($this->key as $campo)
		{
			array_push($arr_id, $this->valores[$campo]);
		}
		return implode($this->separador_campos, $arr_id);
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
		foreach ($this->field_info as $campo => $metadata)
		{
			$this->set_validation_rules_field($campo);
		}
		$this->form_validation->set_error_delimiters('<p class="text-error"><strong>ERROR: ', '</strong></p>');
		$this->form_validation->set_message('required', 'Ingrese un valor para "%s"');
		$this->form_validation->set_message('greater_than', 'Seleccione un valor para "%s"');
		$this->form_validation->set_message('numeric', 'Ingrese un valor numérico para "%s"');
		$this->form_validation->set_message('integer', 'Ingrese un valor entero para "%s"');
		$this->form_validation->set_message('edit_unique', 'El valor del campo "%s" debe ser único');

		return $this->form_validation->run();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera la regla de validación para un campo, de acuerdo a la definición del campo
	 *
	 * @param string $campo Nombre del campo
	 */
	public function set_validation_rules_field($campo = '')
	{
		$info_campo = $this->field_info[$campo];

		$reglas = 'trim';
		$reglas .= ($info_campo['es_obligatorio'] and !$info_campo['es_autoincrement']) ? '|required' : '';
		$reglas .= ($info_campo['tipo'] == 'INT')  ? '|integer' : '';
		$reglas .= ($info_campo['tipo'] == 'REAL') ? '|numeric' : '';
		$reglas .= ($info_campo['es_unico'] AND !$info_campo['es_id']) ? '|edit_unique['. $this->tabla_bd . '.' . $info_campo['campo_bd'] . '.' . implode($this->separador_campos, $this->get_key()) . '.' . $this->get_model_id() . ']' : '';

		if ($info_campo['tipo'] == 'HAS_MANY')
		{
			$reglas = '';
		}

		$this->form_validation->set_rules($campo, ucfirst($info_campo['label']), $reglas);
	}

	// --------------------------------------------------------------------

	public function get_relation_fields()
	{
		$this->_recuperar_relation_fields();
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Devuelve el texto label de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Label del campo
	 */
	public function get_label_field($campo = '')
	{
		$tipo = $this->field_info[$campo]->get_tipo();
		if(($tipo == 'has_one' or $tipo == 'has_many') and !($this->model_got_relations))
		{
			$this->_recuperar_relation_fields();

		}
		return $this->field_info[$campo]->get_label();
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------


	// --------------------------------------------------------------------

	/**
	 * Devuelve la propiedad es_obligatorio de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return boolean       Indicador si el campo es obligatorio
	 */
	public function get_es_obligatorio_field($campo = '')
	{
		return $this->field_info[$campo]->get_es_obligatorio();
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
		return ($this->field_info[$campo]['es_obligatorio']) ? '<span class="text-danger"><strong>*</strong></span>' : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 *
	 * @param  string $filtro Filtro de los valores del modelo
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas($filtro = '_', $url = '')
	{
		$this->load->library('pagination');
		$total_rows = $this->find('count', array('filtro' => $filtro), FALSE);

		$cfg_pagination = array(
					'uri_segment' => 5,
					'num_links'   => 5,

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


					'per_page'    => $this->page_results,
					'total_rows'  => $total_rows,
					'base_url'    => site_url($url . '/' . $this->clase . '/' . $filtro . '/'),
					'first_link'  => 'Primero',
					'last_link'   => 'Ultimo (' . (int)($total_rows / $this->page_results + 1) . ')',
					'prev_link'   => '<span class="glyphicon glyphicon-chevron-left"></span>',
					'next_link'   => '<span class="glyphicon glyphicon-chevron-right"></span>',
				);
		$this->pagination->initialize($cfg_pagination);
		return $this->pagination->create_links();
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
						$this->db->where($campo.'=', 'NULL', FALSE);
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
				$this->db->limit($param['limit'], $param['offset']);
			}
			else
			{
				$this->db->limit($param['limit']);
			}
		}

		if ($tipo == 'first')
		{
			$rs = $this->db->get($this->tabla_bd)->row_array();
			$this->get_from_array($rs);

			if ($recupera_relation)
			{
				$this->_recuperar_relation_fields();
			}

			return $rs;
		}
		else if ($tipo == 'all')
		{
			if ($this->orden != '')
			{
				$this->db->order_by($this->orden);
			}
			$rs = $this->db->get($this->tabla_bd)->result_array();

			foreach($rs as $reg)
			{
				$o = new $this->clase();
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
			$rs = $this->db->get($this->tabla_bd)->row_array();

			return $rs['cant'];
		}
		else if ($tipo == 'list')
		{
			$arr_list = array();
			if ($this->orden != '')
			{
				$this->db->order_by($this->orden);
			}
			$rs = $this->db->get($this->tabla_bd)->result_array();

			foreach($rs as $reg)
			{
				$o = new $this->clase();
				$o->get_from_array($reg);
				$arr_list[$o->get_model_id()] = $o->__toString();
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

		if (count($this->key) == 1)
		{
			foreach($this->key as $campo_id)
			{
				$tipo_id = $this->field_info[$campo_id]['tipo'];
				if ($tipo_id == 'ID' OR $tipo_id == 'INTEGER')
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
			foreach($this->key as $i => $campo_id)
			{
				$arr_condiciones[$campo_id] = (is_null($id)) ? '' : $arr_val_id[$i];
			}
		}

		$this->find('first', array('conditions' => $arr_condiciones), $recupera_relation);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los mdelos dependientes (de las relaciones has_one y has_many)
	 *
	 * @return nada
	 */
	private function _recuperar_relation_fields()
	{
		foreach($this->field_info as $campo => $metadata)
		{
			if($this->campo_get_tipo($campo) == 'HAS_ONE')
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $metadata['relation'];

				$class_relacionado = $arr_props_relation['model'];
				$this->load->model($class_relacionado);

				$model_relacionado = new $class_relacionado();
				$model_relacionado->find_id($this->valores[$campo], FALSE);

				$arr_props_relation['data'] = $model_relacionado;
				$this->field_info[$campo]['relation'] = $arr_props_relation;
				$this->model_got_relations = TRUE;
			}
			else if ($this->campo_get_tipo($campo) == 'HAS_MANY')
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $metadata['relation'];

				// genera arreglo where con la llave del modelo
				$arr_where = array();
				$arr_id_one_table = $arr_props_relation['id_one_table'];

				foreach ($this->key as $campo_id)
				{
					$arr_where[array_shift($arr_id_one_table)] = $this->valores[$campo_id];
				}
				// recupera la llave del modelo en tabla de la relacion (n:m)
				$rs = $this->db->select($this->_junta_campos_select($arr_props_relation['id_many_table']), FALSE)->get_where($arr_props_relation['join_table'], $arr_where)->result_array();

				$class_relacionado = $arr_props_relation['model'];
				$this->load->model($class_relacionado);
				$model_relacionado = new $class_relacionado;

				// genera arreglo de condiciones de busqueda
				$arr_where = array();
				foreach($rs as $reg)
				{
					array_push($arr_where, array_pop($reg));
				}

				$arr_condiciones = array($this->_junta_campos_select($model_relacionado->get_key()) => $arr_where);

				$model_relacionado->find('all', array('conditions' => $arr_condiciones), FALSE);

				$arr_props_relation['data'] = $model_relacionado;
				$this->field_info[$campo]['relation'] = $arr_props_relation;
				//$this->valores[$campo] = $arr_where;
				$this->model_got_relations = TRUE;
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
	 * Puebla los campos del modelo con los valores de un arreglo
	 *
	 * @param  array  $rs Arreglo con los valores
	 * @return nada
	 */
	public function get_from_array($rs = array())
	{
		foreach($this->field_info as $campo => $metadata)
		{
			if (array_key_exists($campo, $rs))
			{
				if ($this->campo_get_tipo($campo) == 'DATETIME')
				{
					if ($rs[$campo] != '')
					{
						$this->$campo = date('Ymd H:i:s', strtotime($rs[$campo]));
					}
				}
				else
				{
					$this->valores[$campo] = $rs[$campo];
				}
			}
		}
	}

	// --------------------------------------------------------------------

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
		$i = 0;
		foreach($this->field_info as $campo => $metadata)
		{
			if ($this->campo_get_tipo($campo) == 'CHAR')
			{
				if ($i == 0)
				{
					$this->db->like($campo, $filtro, 'both');
				}
				else
				{
					$this->db->or_like($campo, $filtro, 'both');
				}
				$i++;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores del post
	 *
	 * @return nada
	 */
	public function recuperar_post()
	{
		foreach($this->field_info as $nombre => $metadata)
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
				$tipo_campo = $metadata['tipo'];
				if ($tipo_campo == 'ID' OR $tipo_campo == 'BOOLEAN' OR $tipo_campo == 'INTEGER')
				{
					$this->$nombre = (int) $this->input->post($nombre);
				}
				else
				{
					$this->$nombre = $this->input->post($nombre);
				}
			}
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

		foreach($this->field_info as $nombre => $campo)
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
				$es_insert = ($val == '' || $val == 0) ? TRUE : FALSE;
			}
		}
		else
		{
			$es_insert = ($this->db->get_where($this->tabla_bd, $data_where)->num_rows() == 0);
		}

		// NUEVO REGISTRO
		if ($es_insert)
		{
			if (!$es_auto_id)
			{
				$this->db->insert($this->tabla_bd, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->tabla_bd, $data_update);
				foreach($this->key as $campo_id)
				{
					$data_where[$campo_id] = $this->db->insert_id();
					$this->{$campo_id} = $this->db->insert_id();
				}
			}
		}
		// REGISTRO EXISTENTE
		else
		{
			$this->db->where($data_where);
			$this->db->update($this->tabla_bd, $data_update);
		}

		// Revisa todos los campos en busqueda de relaciones has_many,
		// para actualizar la tabla relacionada
		foreach($this->field_info as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();

				$arr_where_delete = array();
				$data_where_tmp = $data_where;
				foreach($rel['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}
				$this->db->delete($rel['join_table'], $arr_where_delete);


				foreach($this->$nombre as $valor_campo)
				{
					$arr_values = array();

					$data_where_tmp = $data_where;
					foreach($rel['id_one_table'] as $id_one_table_key)
					{
						$arr_values[$id_one_table_key] = array_shift($data_where_tmp);
					}

					$arr_many_valores = explode($this->separador_campos, $valor_campo);
					foreach($rel['id_many_table'] as $id_many)
					{
						$arr_values[$id_many] = array_shift($arr_many_valores);
					}

					$this->db->insert($rel['join_table'], $arr_values);
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
		foreach($this->field_info as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}
		$this->db->delete($this->tabla_bd, $data_where);

		foreach($this->field_info as $nombre => $campo)
		{
			if ($campo->get_tipo() == 'has_many')
			{
				$rel = $campo->get_relation();
				$arr_where_delete = array();
				$data_where_tmp = $data_where;
				foreach($rel['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}
				$this->db->delete($rel['join_table'], $arr_where_delete);
			}
		}
	}

// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************

	public function campo_es_id($campo = '')
	{
		return (boolean) $this->field_info[$campo]['es_id'];
	}


	public function campo_get_tipo($campo = '')
	{
		return strtoupper($this->field_info[$campo]['tipo']);
	}


	public function campo_get_mostrar_en_lista($campo = '')
	{
		return (boolean) $this->field_info[$campo]['mostrar_en_listado'];
	}

	public function campo_get_es_obligatorio($campo = '')
	{
		return (boolean) $this->field_info[$campo]['es_obligatorio'];
	}



	public function campo_get_label($campo = '')
	{
		if ($this->field_info[$campo]['tipo'] == 'HAS_ONE')
		{
			if (!array_key_exists('data', $this->field_info[$campo]['relation']))
			{
				$this->_recuperar_relation_fields();
			}
			return $this->field_info[$campo]['relation']['data']->get_label();
		}
		else if ($this->field_info[$campo]['tipo'] == 'HAS_MANY')
		{
			if (!array_key_exists('data', $this->field_info[$campo]['relation']))
			{
				$this->_recuperar_relation_fields();
			}
			return $this->field_info[$campo]['relation']['data']->get_label_plural();
		}
		else
		{
			return $this->field_info[$campo]['label'];
		}
	}


	/**
	 * Devuelve el valor de un campo del modelo
	 *
	 * @param  string  $campo     Nombre del campo
	 * @param  boolean $formatted Indica si se devuelve el valor formateado o no (ej. id de relaciones)
	 * @return string             Texto con el valor del campo
	 */
	public function campo_get_valor_field($campo = '', $formatted = TRUE)
	{
		if (!$formatted)
		{
			return ($this->valores[$campo]);
		}
		else
		{
			return ($this->campo_get_formatted_value($campo, $this->valores[$campo]));
		}
	}


	/**
	 * Devuelve el valor, formateado de acuerdo al tipo del campo
	 *
	 * @param  string $campo Campo a desplegar
	 * @return string        Valor formateado
	 */
	public function campo_get_formatted_value($campo = '')
	{
		if ($this->field_info[$campo]['tipo'] == 'BOOLEAN')
		{
			return ($this->valores[$campo] == 1) ? 'SI' : 'NO';
		}
		else if ($this->field_info[$campo]['tipo'] == 'HAS_ONE')
		{
			return $this->field_info[$campo]['relation']['data']->__toString();
		}
		else if ($this->field_info[$campo]['tipo'] == 'HAS_MANY')
		{
			$print_campo = '<ul class="formatted_has_many">';
			foreach ($this->field_info[$campo]['relation']['data']->get_model_all() as $obj)
			{
				$print_campo .= '<li>' . (string) $obj . '</li>';
			}
			$print_campo .= '</ul>';
			return $print_campo;
		}
		else if (count($this->field_info[$campo]['choices']) > 0)
		{
			return $this->field_info[$campo]['choices'][$this->valores[$campo]];
		}
		else
		{
			return $this->valores[$campo];
		}
	}


	/**
	 * Devuelve el formulario para el despliegue de un campo del modelo
	 *
	 * @param  string  $campo          Nombre del campo
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @return string        Elemento de formulario
	 */
	public function campo_print_form($campo = '', $filtra_activos = FALSE)
	{
		// busca condiciones en la relacion a las cuales se les deba buscar un valor de filtro
		$arr_relation = $this->field_info[$campo]['relation'];
		if (array_key_exists('conditions', $arr_relation))
		{
			foreach($arr_relation['conditions'] as $cond_key => $cond_value)
			{
				// si encontramos un valor que comience por @filed_value,
				// se reemplaza por el valor del campo en el objeto
				if (strpos($cond_value, '@field_value') === 0)
				{
					$arr_field_value = explode(':', $cond_value);
					$arr_relation['conditions'][$cond_key] = $this->{$arr_field_value[1]};
				}
			}
			$this->field_info[$campo]->set_relation($arr_relation);
		}

		return $this->campo_form($campo, $this->valores[$campo], $filtra_activos);
	}

	// --------------------------------------------------------------------

	/**
	 * Genera el elemento de formulario para el campo
	 *
	 * @param  string $campo Campo a desplegar en el elemento de formulario
	 * @param  string $valor Valor a desplegar en el elemento de formulario
	 * @param  boolean $filtra_activos Indica si se mostraran sólo los valores activos
	 * @return string        Elemento de formulario
	 */
	public function campo_form($campo = '', $valor = '', $filtra_activos = FALSE)
	{
		$form       = '';
		$id_prefix  = 'id_';

		$valor_field = ($valor === '' and $this->field_info[$campo]['default'] != '') ? $this->field_info[$campo]['default'] : $valor;
		$valor_field = set_value($this->field_info[$campo]['nombre'], $valor_field);

		if ($this->field_info[$campo]['tipo'] == 'ID' OR ($this->field_info[$campo]['es_id'] AND $valor_field != '') OR ($this->field_info[$campo]['es_id'] AND $this->field_info[$campo]['es_autoincrement']))
		{
			$param_adic = ' id="' . $id_prefix . $this->field_info[$campo]['nombre'] . '"';

			$form = '<span class="uneditable-input form-control">' . $valor_field . '</span>';
			$form .= form_hidden($this->field_info[$campo]['nombre'], $valor_field, $param_adic);
		}
		else if (!empty($this->field_info[$campo]['choices']))
		{
			$param_adic = ' id="' . $id_prefix . $this->field_info[$campo]['nombre'] . '" class="form-control"';
			$form = form_dropdown($this->field_info[$campo]['nombre'], $this->field_info[$campo]['choices'], $valor_field, $param_adic);
		}
		else if ($this->field_info[$campo]['tipo'] == 'CHAR')
		{
			if ($this->field_info[$campo]['largo'] >= 100)
			{
				$arr_param = array(
									'name'      => $this->field_info[$campo]['nombre'],
									'value'     => $valor_field,
									'maxlength' => $this->field_info[$campo]['largo'],
									'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
									'cols'      => '50',
									'rows'      => '5',
									'class'     => 'form-control',
								);
				$form = form_textarea($arr_param);
			}
			else
			{
				$arr_param = array(
									'name'      => $this->field_info[$campo]['nombre'],
									'value'     => $valor_field,
									'maxlength' => $this->field_info[$campo]['largo'],
									'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
									'class'     => 'form-control',
								);
				$form = form_input($arr_param);
			}
		}
		else if ($this->field_info[$campo]['tipo'] == 'PASSWORD')
		{
			$arr_param = array(
								'name'      => $this->field_info[$campo]['nombre'],
								'value'     => $valor_field,
								'maxlength' => $this->field_info[$campo]['largo'],
								'size'      => $this->field_info[$campo]['largo'],
								'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
								'class'     => 'form-control',
							);
			$form = form_password($arr_param);
		}
		else if ($this->field_info[$campo]['tipo'] == 'INT')
		{
			$arr_param = array(
								'name'      => $this->field_info[$campo]['nombre'],
								'value'     => $valor_field,
								'maxlength' => $this->field_info[$campo]['largo'],
								'size'      => $this->field_info[$campo]['largo'],
								'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
								'class'     => 'form-control',
							);
			$form = form_input($arr_param);
		}
		else if ($this->field_info[$campo]['tipo'] == 'REAL')
		{
			$arr_param = array(
								'name'      => $this->field_info[$campo]['nombre'],
								'value'     => $valor_field,
								'maxlength' => $this->field_info[$campo]['largo'],
								'size'      => $this->field_info[$campo]['largo'] + $this->field_info[$campo]['decimales'] + 1,
								'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
								'class'     => 'form-control',
							);
			$form = form_input($arr_param);
		}
		else if ($this->field_info[$campo]['tipo'] == 'DATETIME')
		{
			$arr_param = array(
								'name'      => $this->field_info[$campo]['nombre'],
								'value'     => $valor_field,
								'maxlength' => $this->field_info[$campo]['largo'],
								'size'      => $this->field_info[$campo]['largo'],
								'id'        => $id_prefix . $this->field_info[$campo]['nombre'],
								'class'     => 'form-control',
							);
			$form = form_input($arr_param);
		}
		else if ($this->field_info[$campo]['tipo'] == 'BOOLEAN')
		{
			$form = '<label class="radio-inline">';
			$form .= form_radio($this->field_info[$campo]['nombre'], 1, ($valor_field == 1) ? TRUE : FALSE) . 'Si';
			$form .= '</label>';
			$form .= '<label class="radio-inline">';
			$form .= form_radio($this->field_info[$campo]['nombre'], 0, ($valor_field == 1) ? FALSE : TRUE) . 'No';
			$form .= '</label>';
		}
		else if ($this->field_info[$campo]['tipo'] == 'HAS_ONE')
		{
			$nombre_rel_modelo = $this->field_info[$campo]['relation']['model'];
			$modelo_rel = new $nombre_rel_modelo();
			$param_adic = ' id="' . $id_prefix . $this->field_info[$campo]['nombre'] . '" class="form-control"';
			$dropdown_conditions = (array_key_exists('conditions', $this->field_info[$campo]['relation'])) ? array('conditions' => $this->field_info[$campo]['relation']['conditions']) : array();
			$form = form_dropdown($this->field_info[$campo]['nombre'], $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
		}
		else if ($this->field_info[$campo]['tipo'] == 'HAS_MANY')
		{
			$nombre_rel_modelo = $this->field_info[$campo]['relation']['model'];
			$modelo_rel = new $nombre_rel_modelo();
			$param_adic = ' id="' . $id_prefix . $this->field_info[$campo]['nombre'] . '" size="7" class="form-control"';
			$dropdown_conditions = (array_key_exists('conditions', $this->field_info[$campo]['relation'])) ? array('conditions' => $this->field_info[$campo]['relation']['conditions']) : array();
			// Para que el formulario muestre multiples opciones seleccionadas, debemos usar este hack
			//$form = form_multiselect($this->nombre.'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $valor_field, $param_adic);
			$opciones = ($this->input->post($this->field_info[$campo]['nombre']) != '') ? $this->input->post($this->field_info[$campo]['nombre']) : $valor_field;
			$form = form_multiselect($this->field_info[$campo]['nombre'].'[]', $modelo_rel->find('list', $dropdown_conditions, FALSE), $opciones, $param_adic);
		}

		return $form;
	}


	/**
	 * Devuelve el texto de ayuda de un campo del modelo
	 *
	 * @param  string $campo Nombre del campo
	 * @return string        Texto de ayuda del campo
	 */
	public function campo_get_texto_ayuda($campo = '')
	{
		return $this->field_info[$campo]['texto_ayuda'];
	}




}








// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************
// **********************************************************************************************************

class ORM_Field {


	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @access  public
	 * @param   array   parametros de construccion
	 * @return  void
	 * @author  dcr
	 **/


	public function get_tipo()
	{
		return $this->tipo;
	}



	public function get_campo_bd()
	{
		return $this->campo_bd;
	}


	public function get_mostrar_lista()
	{
		return $this->mostrar_lista;
	}


	public function get_es_unico()
	{
		return $this->es_unico;
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