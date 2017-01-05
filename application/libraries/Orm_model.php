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
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Orm_model implements IteratorAggregate {

	/**
	 * Nombre del modelo
	 *
	 * @var  string
	 */
	private $_model_nombre = '';

	/**
	 * Clase del modelo
	 *
	 * @var  string
	 */
	private $_model_class = '';

	/**
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	private $_model_tabla = '';

	/**
	 * Nombre (etiqueta) del modelo
	 *
	 * @var string
	 */
	private $_model_label = '';

	/**
	 * Nombre (etiqueta) del modelo (plural)
	 *
	 * @var string
	 */
	private $_model_label_plural = '';

	/**
	 * Campos para ordenar el modelo cuando se recupera de la BD
	 *
	 * @var string
	 */
	private $_model_order_by = '';

	/**
	 * Campos que conforman la llave (id) del modelo
	 *
	 * @var array
	 */
	private $_model_campo_id = array();

	/**
	 * Indicador si se recuperaron los datos de las relaciones
	 *
	 * @var boolean
	 */
	private $_model_got_relations = FALSE;

	/**
	 * Cantidad de registros por pagina
	 *
	 * @var integer
	 */
	private $_model_page_results = 12;

	/**
	 * Filtro para buscar resultados
	 *
	 * @var string
	 */
	private $_model_filtro = '';

	/**
	 * Caracter separador de los campos cuando la llave tiene más de un campo
	 *
	 * @var string
	 */
	protected $_separador_campos = '~';

	/**
	 * Arreglo de registros recuperados de la BD
	 *
	 * @var array
	 */
	private $_fields_values = array();

	/**
	 * Arreglo con los campos del modelo
	 *
	 * @var array
	 */
	private $_model_fields = array();

	/**
	 * Arreglo temporal de objetos recuperados de la BD
	 *
	 * @var array
	 */
	private $_fields_relation_objects = NULL;


	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @return  void
	 **/
	public function __construct()
	{
		$this->lang->load('orm');

		$this->_model_class  = get_class($this);
		$this->_model_nombre = strtolower($this->_model_class);
		$this->_model_tabla  = $this->_model_nombre;
		$this->_model_label  = $this->_model_nombre;
		$this->_model_label_plural = $this->_model_label . 's';
		$this->_fields_relation_objects = new Collection();
	}

	// --------------------------------------------------------------------

	/**
	 * Configuración del modelo
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   array $param Arreglo con los parametros a configurar
	 * @return  void
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

		$this->_model_campo_id = $this->_determina_campo_id();
		//$this->_recuperar_relation_fields();
	}

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
		if (array_key_exists($campo, $this->_fields_values))
		{
			return $this->_fields_values[$campo];
		}
		else
		{
			$ci =& get_instance();
			return $ci->{$campo};
		}
	}

	// --------------------------------------------------------------------
	// Iterator methods
	// --------------------------------------------------------------------

	/**
	 * Devuelve el iterador de array para recorrer los campos del modelo
	 * como si fuera un arreglo
	 *
	 * @return ArrayIterator Iterador de arreglo
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_fields_values);
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración del modelo
	 * @return nada
	 */
	private function _config_modelo($arr_config = array())
	{
		foreach ($arr_config as $campo => $propiedad)
		{
			$campo = (substr($campo, 0, 1) !== '_' ? '_' : '').$campo;

			if (isset($this->$campo))
			{
				$this->$campo = $propiedad;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades de los campos del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración de los campos del modelo
	 * @return nada
	 */
	protected function _config_campos($arr_config = array())
	{
		foreach ($arr_config as $campo => $propiedad)
		{
			$propiedad['tabla_bd'] = $this->_model_tabla;
			$obj_field = new Orm_field($campo, $propiedad);
			$this->_model_fields[$campo] = $obj_field;
			$this->_fields_values[$campo] = NULL;
		}
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	/**
	 * Recupera el nombre del modelo
	 *
	 * @return string Nombre del modelo
	 */
	public function get_model_nombre()
	{
		return $this->_model_nombre;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el nombre de la tabla del modelo en la base de datos
	 *
	 * @return string Nombre de la tabla
	 */
	public function get_model_tabla()
	{
		return $this->_model_tabla;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo
	 *
	 * @return string Etiqueta del modelo
	 */
	public function get_model_label()
	{
		return $this->_model_label;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo en plural
	 *
	 * @return string Etiqueta del modelo en plural
	 */
	public function get_model_label_plural()
	{
		return $this->_model_label_plural;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los nombres de los campos que son el ID del modelo
	 *
	 * @return array Arreglo con los campos ID del modelo
	 */
	public function get_model_campo_id()
	{
		return $this->_model_campo_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la cantidad de registros que componen una página
	 *
	 * @return integer Cantidad de registros en una página
	 */
	public function get_model_page_results()
	{
		return $this->_model_page_results;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los campos del modelo
	 *
	 * @return array Arreglo con los campos del modelo
	 */
	public function get_model_fields()
	{
		return $this->_model_fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @return string Filtro a usar
	 */
	public function get_model_filtro()
	{
		return ($this->_model_filtro === '_') ? '' : $this->_model_filtro;
	}

	// --------------------------------------------------------------------

	/**
	 * Fija el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @param  string $filtro Filtro a usar
	 * @return void
	 */
	public function set_model_filtro($filtro = '')
	{
		$this->_model_filtro = $filtro;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los campos que se usarán para listar el modelo
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_listado()
	{
		return $this->campos_listado;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los objetos que son las relaciones del campo indicado (1:n o n:m)
	 *
	 * @param  string $campo Nombre del campo a recuperar sus relaciones
	 * @return array         Arreglo con la relación del campo
	 */
	public function get_relation_object($campo = '')
	{
		if ( ! $campo)
		{
			return NULL;
		}

		$model_fields = $this->_model_fields;
		$relation = $model_fields[$campo]->get_relation();

		return $relation['model'];
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los valores de los campos del modelo
	 *
	 * @return array Arreglo con los valores de los campos del modelo
	 */
	public function get_fields_values()
	{
		return $this->_fields_values;
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

		foreach ($this->_model_fields as $nombre_campo => $obj_campo)
		{
			if ($obj_campo->get_es_id())
			{
				array_push($arr_key, $nombre_campo);
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

		foreach ($this->_model_campo_id as $campo)
		{
			array_push($arr_id, $this->{$campo});
		}

		$model_id = implode($this->_separador_campos, $arr_id);

		return ($model_id === '') ? NULL : $model_id;
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
		foreach ($this->_model_fields as $nombre_campo => $obj_campo)
		{
			$this->set_validation_rules_field($nombre_campo);
		}

		return $this->form_validation->run();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera la regla de validación para un campo, de acuerdo a la definición del campo
	 *
	 * @param  string $campo Nombre del campo
	 * @return void
	 */
	public function set_validation_rules_field($campo = '')
	{
		$field = $this->_model_fields[$campo];

		$reglas = 'trim';
		$reglas .= ($field->get_es_obligatorio() AND ! $field->get_es_autoincrement()) ? '|required' : '';
		$reglas .= ($field->get_tipo() === Orm_field::TIPO_INT)  ? '|integer' : '';
		$reglas .= ($field->get_tipo() === Orm_field::TIPO_REAL) ? '|numeric' : '';
		$reglas .= ($field->get_es_unico() AND ! $field->get_es_id())
			? '|edit_unique['.$this->_model_tabla.':'.$field->get_nombre_bd().':'.
				implode($this->_separador_campos, $this->get_model_campo_id()).':'.$this->get_model_id().']'
			: '';

		$campo_rules = $campo;
		if ($field->get_tipo() === Orm_field::TIPO_HAS_MANY)
		{
			$reglas = 'trim';
			$campo_rules = $campo.'[]';
		}

		$this->form_validation->set_rules($campo_rules, ucfirst($this->get_label_field($campo)), $reglas);
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
			$data = array(
				'form_label'    => form_label(
										ucfirst($this->get_label_field($campo))
										.$this->get_marca_obligatorio_field($campo),
										'id_'.$campo,
										array('class' => 'control-label col-sm-4')
									),
				'item_error'    => form_has_error_class($campo),
				'item-feedback' => is_null($field_error) ? '' : 'has-feedback',
				'item_form'     => $this->print_form_field($campo, FALSE, '', $field_error),
				'item_help'     => $show_help ? $this->get_texto_ayuda_field($campo) : '',
			);

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
		// busca condiciones en la relacion a las cuales se les deba buscar un valor de filtro
		$arr_relation = $this->_model_fields[$campo]->get_relation();

		if (array_key_exists('conditions', $arr_relation))
		{
			foreach ($arr_relation['conditions'] as $cond_key => $cond_value)
			{
				// si encontramos un valor que comience por @filed_value,
				// se reemplaza por el valor del campo en el objeto
				// ejemplo: @field_value:nombre_campo:valor_default
				if ( ! is_array($cond_value) AND strpos($cond_value, '@field_value') === 0)
				{
					list($cond_tipo, $cond_campo, $cond_default) = explode(':', $cond_value);

					$arr_relation['conditions'][$cond_key] = $this->{$cond_campo}
						? $this->{$cond_campo}
						: $cond_default;
				}
			}
			$this->_model_fields[$campo]->set_relation($arr_relation);
		}

		return $this->_model_fields[$campo]->form_field($this->$campo, $filtra_activos, $clase_adic, $field_error);
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
		$tipo = $this->_model_fields[$campo]->get_tipo();

		if (($tipo === Orm_field::TIPO_HAS_ONE OR $tipo === Orm_field::TIPO_HAS_MANY) AND ! $this->_model_got_relations)
		{
			$this->_recuperar_relation_fields();
		}

		return $this->_model_fields[$campo]->get_label();
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
		return $this->_model_fields[$campo]->get_texto_ayuda();
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
		return ( ! $formatted) ? $this->$campo : $this->_model_fields[$campo]->get_formatted_value($this->$campo);
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
		return $this->_model_fields[$campo]->get_es_obligatorio();
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
		return $this->_model_fields[$campo]->get_mostrar_lista();
	}

	// --------------------------------------------------------------------

	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 *
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas()
	{
		$this->load->library('pagination');
		$total_rows = $this->find('count', array('filtro' => $this->_model_filtro), FALSE);

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

			'per_page'    => $this->_model_page_results,
			'total_rows'  => $total_rows,
			'base_url'    => site_url(
				$this->router->class . '/' .
				($this->uri->segment(2) ? $this->uri->segment(2) : 'listado') . '/' .
				$this->get_model_nombre()
			),
			'first_link'  => $this->lang->line('orm_pag_first'),
			'last_link'   => $this->lang->line('orm_pag_last') . ' (' . (int)($total_rows / $this->_model_page_results + 1) . ')',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',

			'reuse_query_string'   => TRUE,
			'page_query_string'    => TRUE,
			'query_string_segment' => 'page',

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
					if (count($valor) === 0)
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

		if (array_key_exists('filtro', $param) AND $param['filtro'] !== '')
		{
			$this->_put_filtro($param['filtro']);
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

		if (array_key_exists('order_by', $param))
		{
			$this->db->order_by($param['order_by']);
		}

		if ($tipo === 'first')
		{
			$registro = $this->db->get($this->_model_tabla)->row_array();
			$this->fill_from_array($registro);

			if ($recupera_relation)
			{
				$this->_recuperar_relation_fields();
			}

			return $registro;
		}
		elseif ($tipo === 'all')
		{
			$results_collection = new Collection();

			if ($this->_model_order_by !== '' AND ! array_key_exists('order_by', $param))
			{
				$this->db->order_by($this->_model_order_by);
			}

			$registros = $this->db->get($this->_model_tabla)->result_array();

			foreach ($registros as $registro)
			{
				$obj_modelo = new $this->_model_class();
				$obj_modelo->fill_from_array($registro);

				if ($recupera_relation)
				{
					$obj_modelo->_recuperar_relation_fields($this->_fields_relation_objects);
					$this->_add_relation_fields($obj_modelo);
				}

				$results_collection->add_item($obj_modelo);
			}

			return $results_collection;
		}
		elseif ($tipo === 'count')
		{
			return $this->db
				->select('count(*) as cant')
				->get($this->_model_tabla)
				->row()
				->cant;
		}
		elseif ($tipo === 'list')
		{
			$arr_list = array();

			if ( ! $param OR array_key_exists('opc_ini', $param))
			{
				if ( ! $param OR $param['opc_ini'] === TRUE)
				{
					$arr_list[''] = 'Seleccione '.strtolower($this->_model_label).'...';
				}
			}

			if ($this->_model_order_by !== '' AND ! array_key_exists('order_by', $param))
			{
				$this->db->order_by($this->_model_order_by);
			}

			$registros = $this->db->get($this->_model_tabla)->result_array();

			foreach ($registros as $registro)
			{
				$obj_modelo = new $this->_model_class();
				$obj_modelo->fill_from_array($registro);
				$arr_list[$obj_modelo->get_model_id()] = (string) $obj_modelo;
			}

			return $arr_list;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera registros por el ID
	 *
	 * @param  string  $id_modelo         Identificador del registro a recuperar
	 * @param  boolean $recupera_relation Indica si se recuperará la información de relación
	 * @return void
	 */
	public function find_id($id_modelo = '', $recupera_relation = TRUE)
	{
		$arr_condiciones = array();

		if (count($this->_model_campo_id) === 1)
		{
			foreach ($this->_model_campo_id as $campo_id)
			{
				$tipo_id = $this->_model_fields[$campo_id]->get_tipo();

				if ($tipo_id === Orm_field::TIPO_ID OR $tipo_id === Orm_field::TIPO_INT)
				{
					$arr_condiciones[$campo_id] = (int) $id_modelo;
				}
				else
				{
					$arr_condiciones[$campo_id] = $id_modelo;
				}
			}
		}
		else
		{
			$arr_val_id = explode($this->_separador_campos, $id_modelo);

			foreach ($this->_model_campo_id as $indice => $campo_id)
			{
				$arr_condiciones[$campo_id] = is_null($id_modelo) ? '' : $arr_val_id[$indice];
			}
		}

		$this->find('first', array('conditions' => $arr_condiciones), $recupera_relation);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera listado paginado
	 *
	 * @param  integer $offset Offset para recuperar una pagina determinada
	 * @return void
	 */
	public function list_paginated($offset = 0)
	{
		return $this->find('all', array(
			'filtro' => $this->_model_filtro,
			'limit'  => $this->_model_page_results,
			'offset' => $offset
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los modelos dependientes (de las relaciones has_one y has_many)
	 *
	 * @param  Collection $relations_collection Coleccion de relacion donde se busca la relacion
	 * @return void
	 */
	private function _recuperar_relation_fields($relations_collection = NULL)
	{
		foreach ($this->_model_fields as $nombre_campo => $obj_campo)
		{
			if($obj_campo->get_tipo() === Orm_field::TIPO_HAS_ONE)
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $obj_campo->get_relation();
				$class_relacionado = $arr_props_relation['model'];

				// si el objeto a recuperar existe en el arreglo $relations_collection,
				// recupera el objeto del arreglo y no va a buscarlo a la BD
				if ($relations_collection AND
					$relations_collection->key_exists($nombre_campo) AND
					$relations_collection->item($nombre_campo)->key_exists($this->{$nombre_campo})
				)
				{
					$model_relacionado = $relations_collection->item($nombre_campo)->item($this->{$nombre_campo});
				}
				else
				{
					$model_relacionado = new $class_relacionado();

					// si el valor del campo es distinto de nulo, lo va  abuscar a la BD
					if ($this->{$nombre_campo} !== NULL)
					{
						$model_relacionado->find_id($this->{$nombre_campo}, FALSE);
					}
				}

				$arr_props_relation['model'] = $model_relacionado;
				$this->_model_fields[$nombre_campo]->set_relation($arr_props_relation);
				$this->_model_got_relations = TRUE;
			}
			elseif ($obj_campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				// recupera las propiedades de la relacion
				$arr_props_relation = $obj_campo->get_relation();

				// genera arreglo where con la llave del modelo
				$arr_where = array();
				$arr_id_one_table = $arr_props_relation['id_one_table'];

				foreach ($this->_model_campo_id as $campo_id)
				{
					$arr_where[array_shift($arr_id_one_table)] = $this->$campo_id;
				}

				// recupera la llave del modelo en tabla de la relacion (n:m)
				$registros = $this->db
					->select($this->_junta_campos_select($arr_props_relation['id_many_table']), FALSE)
					->get_where($arr_props_relation['join_table'], $arr_where)
					->result_array();

				$class_relacionado = $arr_props_relation['model'];
				$model_relacionado = new $class_relacionado();

				// genera arreglo de condiciones de busqueda
				$arr_where = array();

				foreach ($registros as $registro)
				{
					array_push($arr_where, array_pop($registro));
				}
				$arr_condiciones = array($this->_junta_campos_select($model_relacionado->get_model_campo_id()) => $arr_where);

				$model_relacionado->find('all', array('conditions' => $arr_condiciones), FALSE);

				$arr_props_relation['model'] = $model_relacionado;
				$arr_props_relation['data'] = $model_relacionado->find('all', array('conditions' => $arr_condiciones), FALSE);
				$this->_model_fields[$nombre_campo]->set_relation($arr_props_relation);
				$this->_fields_values[$nombre_campo] = $arr_where;
				$this->_model_got_relations = TRUE;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega a las relaciones del objeto actual,
	 * las relaciones del objeto pasado como parametro
	 *
	 * @param  mixed $obj_model Objeto del cual se sacan las relaciones
	 * @return void
	 */
	private function _add_relation_fields($obj_model)
	{
		foreach($obj_model->get_model_fields() as $campo => $obj_campo)
		{
			if ($obj_campo->get_tipo() === Orm_field::TIPO_HAS_ONE)
			{
				$relation_model = $obj_campo->get_relation();
				if (array_key_exists('model', $relation_model))
				{
					if ( ! $this->_fields_relation_objects->key_exists($campo))
					{
						$this->_fields_relation_objects->add_item(new Collection(), $campo);
					}
					if ( ! $this->_fields_relation_objects->item($campo)->key_exists($obj_model->{$campo}))
					{
						$this->_fields_relation_objects->item($campo)->add_item($relation_model['model'], $obj_model->{$campo});
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve campos select de una lista para ser consulados como un solo
	 * campo de la base de datos
	 *
	 * @param  array $arr_campos Arreglo con el listado de campos
	 * @return string             Listado de campos unidos por la BD
	 */
	private function _junta_campos_select($arr_campos = array())
	{
		if (count($arr_campos) === 1)
		{
			return array_pop($arr_campos);
		}
		else
		{
			if ($this->db->dbdriver === 'mysqli')
			{
				// CONCAT_WS es especifico para MYSQL
				$lista_campos = implode(',', $arr_campos);

				return 'CONCAT_WS(\'' . $this->_separador_campos . '\',' . $lista_campos . ')';
			}
			else
			{
				$lista_campos = implode(' + \'' . $this->_separador_campos . '\' + ', $arr_campos);

				return $lista_campos;
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Puebla el modelo con datos de la base de datos o de un arreglo entregado
	 *
	 * @param  mixed $id_modelo ID del registro a recuperar o arreglo con datos
	 * @return void
	 */
	public function fill($id_modelo = NULL)
	{
		if ($id_modelo)
		{
			return is_array($id_modelo) ? $this->fill_from_array($id_modelo) : $this->find_id($id_modelo);
		}
	}



	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores de un arreglo
	 *
	 * @param  array $arr_data Arreglo con los valores
	 * @return void
	 */
	public function fill_from_array($arr_data = array())
	{
		if ($arr_data AND count($arr_data) > 0)
		{
			foreach ($this->_model_fields as $nombre_campo => $obj_campo)
			{
				if (array_key_exists($nombre_campo, $arr_data))
				{
					$tipo_campo = $obj_campo->get_tipo();

					if ($tipo_campo === Orm_field::TIPO_ID
						OR $tipo_campo === Orm_field::TIPO_BOOLEAN
						OR $tipo_campo === Orm_field::TIPO_INT)
					{
						$this->_fields_values[$nombre_campo] = (int) $arr_data[$nombre_campo];
					}
					elseif ($tipo_campo === Orm_field::TIPO_DATETIME)
					{
						if ($arr_data[$nombre_campo] !== '')
						{
							$this->_fields_values[$nombre_campo] = date('Y-m-d H:i:s', strtotime($arr_data[$nombre_campo]));
						}
					}
					else
					{
						$this->_fields_values[$nombre_campo] = $arr_data[$nombre_campo];
					}
				}
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Puebla los campos del modelo con los valores del post
	 *
	 * @return void
	 */
	public function recuperar_post()
	{
		return $this->fill_from_array($this->input->post());
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

		foreach ($this->_model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() === Orm_field::TIPO_CHAR)
			{
				$arr_like[$nombre] = $filtro;
			}
		}

		if (count($arr_like) > 0)
		{
			$this->db->or_like($arr_like, $filtro, 'both');
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
		if ($filtro !== '_' && $filtro !== '')
		{
			$this->_put_filtro($filtro);
		}

		return $this->db->count_all_results($this->_model_tabla);
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

		foreach ($this->_model_fields as $nombre => $campo)
		{
			if ($campo->get_es_id() AND $campo->get_es_autoincrement())
			{
				$es_auto_id = TRUE;
			}

			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
			else
			{
				if ($campo->get_tipo() !== Orm_field::TIPO_HAS_MANY)
				{
					$data_update[$nombre] = $this->$nombre;
				}
			}
		}

		if ($es_auto_id)
		{
			foreach ($data_where as $llave => $valor)
			{
				$es_insert = ($valor === '' OR $valor === 0) ? TRUE : FALSE;
			}
		}
		else
		{
			$es_insert = ($this->db->get_where($this->_model_tabla, $data_where)->row() === NULL)
							OR ($this->db->get_where($this->_model_tabla, $data_where)->num_rows() === 0);
		}

		// NUEVO REGISTRO
		if ($es_insert)
		{
			if ( ! $es_auto_id)
			{
				$this->db->insert($this->_model_tabla, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->_model_tabla, $data_update);

				foreach ($this->_model_campo_id as $campo_id)
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
			$this->db->update($this->_model_tabla, $data_update);
		}

		// Revisa todos los campos en busqueda de relaciones has_many,
		// para actualizar la tabla relacionada
		foreach ($this->_model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				$relation = $campo->get_relation();

				$arr_where_delete = array();
				$data_where_tmp = $data_where;

				foreach ($relation['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}

				$this->db->delete($relation['join_table'], $arr_where_delete);

				foreach ($this->$nombre as $valor_campo)
				{
					$arr_values = array();

					$data_where_tmp = $data_where;

					foreach ($relation['id_one_table'] as $id_one_table_key)
					{
						$arr_values[$id_one_table_key] = array_shift($data_where_tmp);
					}

					$arr_many_valores = explode($this->_separador_campos, $valor_campo);

					foreach ($relation['id_many_table'] as $id_many)
					{
						$arr_values[$id_many] = array_shift($arr_many_valores);
					}

					$this->db->insert($relation['join_table'], $arr_values);
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

		foreach ($this->_model_fields as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}

		$this->db->delete($this->_model_tabla, $data_where);

		foreach ($this->_model_fields as $nombre => $campo)
		{
			if ($campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				$relation = $campo->get_relation();
				$arr_where_delete = array();
				$data_where_tmp = $data_where;

				foreach ($relation['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}

				$this->db->delete($relation['join_table'], $arr_where_delete);
			}
		}
	}


}
/* End of file orm_model.php */
/* Location: ./application/libraries/orm_model.php */