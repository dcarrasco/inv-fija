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

	use Model_has_attributes;
	use Model_has_relationships;
	use Model_has_form;

	/**
	 * Nombre del modelo
	 *
	 * @var  string
	 */
	protected $model_nombre = '';

	/**
	 * Clase del modelo
	 *
	 * @var  string
	 */
	protected $model_class = '';

	/**
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	protected $tabla = '';

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
	protected $order_by = '';

	/**
	 * Cantidad de registros por pagina
	 *
	 * @var integer
	 */
	protected $page_results = 12;

	/**
	 * Nombre del registro del numero de pagina
	 *
	 * @var string
	 */
	protected $page_name = 'page';

	/**
	 * Filtro para buscar resultados
	 *
	 * @var string
	 */
	protected $filtro = '';

	/**
	 * Caracter separador de los campos cuando la llave tiene más de un campo
	 *
	 * @var string
	 */
	protected $separador_campos = '~';

	/**
	 * Arreglo con los campos del modelo
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Arreglo con la configuración del modelo
	 *
	 * @var array
	 */
	protected $model_config = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   mixed $id_modelo Identificador o arreglo de valores para inicializar el modelo
	 * @return  void
	 **/
	public function __construct($id_modelo = NULL)
	{
		$this->lang->load('orm');

		$this->model_class      = get_class($this);
		$this->model_nombre     = strtolower($this->model_class);
		$this->tabla            = $this->model_nombre;
		$this->label            = $this->model_nombre;
		$this->label_plural     = $this->label . 's';
		$this->relation_objects = new Collection();

		$this->config_model($this->model_config);

		if ($id_modelo)
		{
			$this->fill($id_modelo);
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
		return new ArrayIterator($this->values);
	}

	// --------------------------------------------------------------------

	/**
	 * Crea una instancia del modelo y la devuelve
	 *
	 * @return static
	 */
	public static function create()
	{
		return new static;
	}

	// --------------------------------------------------------------------

	/**
	 * Configuración del modelo
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   array $param Arreglo con los parametros a configurar
	 * @return  void
	 **/
	public function config_model($param = [])
	{
		$this->config_modelo(array_get($param, 'modelo', []));
		$this->config_campos(array_get($param, 'campos', []));

		$this->campo_id = $this->_determina_campo_id();
		//$this->_recuperar_relation_fields();
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración del modelo
	 * @return nada
	 */
	protected function config_modelo($arr_config = [])
	{
		collect($arr_config)->each(function($value, $index) {
			if (isset($this->{$index}))
			{
				$this->{$index} = $value;
			}
		});
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades de los campos del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración de los campos del modelo
	 * @return nada
	 */
	protected function config_campos($arr_config = [])
	{
		collect($arr_config)->each(function($config_value, $campo) {
			$config_value['tabla_bd'] = $this->tabla;

			$this->fields[$campo] = new Orm_field($campo, $config_value);
			$this->values[$campo] = NULL;
		});
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	/**
	 * Recupera el nombre del modelo
	 *
	 * @param  boolean $full_name Indica si devuelve el nombre completo (con namespace)
	 * @return string             Nombre del modelo
	 */
	public function get_model_nombre($full_name = TRUE)
	{
		if ($full_name)
		{
			return $this->model_nombre;
		}

		$arr_model_nombre = explode('\\', $this->model_nombre);

		return $arr_model_nombre[count($arr_model_nombre) - 1];
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el nombre de la tabla del modelo en la base de datos
	 *
	 * @return string Nombre de la tabla
	 */
	public function get_tabla()
	{
		return $this->tabla;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo
	 *
	 * @return string Etiqueta del modelo
	 */
	public function get_label()
	{
		return $this->label;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo en plural
	 *
	 * @return string Etiqueta del modelo en plural
	 */
	public function get_label_plural()
	{
		return $this->label_plural;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la cantidad de registros que componen una página
	 *
	 * @return integer Cantidad de registros en una página
	 */
	public function get_page_results()
	{
		return $this->page_results;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los campos del modelo
	 *
	 * @return array Arreglo con los campos del modelo
	 */
	public function get_fields($filtrar_listado = FALSE)
	{
		if ($filtrar_listado)
		{
			return collect($this->fields)->filter(function($campo) {
					return $campo->get_mostrar_lista();
				})->all();
		}

		return $this->fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @return string Filtro a usar
	 */
	public function get_filtro()
	{
		return ($this->filtro === '_') ? '' : $this->filtro;
	}

	// --------------------------------------------------------------------

	/**
	 * Fija el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @param  string $filtro Filtro a usar
	 * @return void
	 */
	public function set_filtro($filtro = '')
	{
		$this->filtro = $filtro;
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
	 * Devuelve indicador si el campo se mostrará o no en listaoo
	 *
	 * @param string $campo Nombre del campo
	 * @return boolean Indicador mostrar o no en la lista
	 */
	public function get_mostrar_lista($campo = '')
	{
		return $this->fields[$campo]->get_mostrar_lista();
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
		$total_rows = $this->find('count', ['filtro' => $this->filtro], FALSE);

		$cfg_pagination = [
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

			'per_page'    => $this->page_results,
			'total_rows'  => $total_rows,
			'base_url'    => site_url(
				$this->router->class . '/' .
				($this->uri->segment(2) ? $this->uri->segment(2) : 'listado') . '/' .
				$this->get_model_nombre(FALSE)
			),
			'first_link'  => lang('orm_pag_first'),
			'last_link'   => lang('orm_pag_last') . ' (' . (int)($total_rows / $this->page_results + 1) . ')',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',

			'reuse_query_string'   => TRUE,
			'page_query_string'    => TRUE,
			'query_string_segment' => $this->page_name,
			'use_page_numbers'     => TRUE,
		];

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
	public function find($tipo = 'first', $param = [], $recupera_relation = TRUE)
	{
		foreach (array_get($param, 'conditions', []) as $campo => $valor)
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

		if (array_key_exists('limit', $param))
		{
			$this->db->limit(array_get($param, 'limit', NULL), array_get($param, 'offset', NULL));
		}

		$this->_put_filtro(array_get($param, 'filtro'));
		$this->db->order_by(array_get($param, 'order_by', $this->order_by));

		if ($tipo === 'first')
		{
			$registro = $this->db->get($this->tabla)->row_array();
			$this->fill_from_array($registro);

			if ($recupera_relation)
			{
				$this->_recuperar_relation_fields();
			}

			return $registro;
		}
		elseif ($tipo === 'all')
		{
			return collect($this->db->get($this->tabla)->result_array())
				->map(function ($registro) use ($recupera_relation) {
					$obj_modelo = new $this->model_class($registro);
					if ($recupera_relation)
					{
						$obj_modelo->_recuperar_relation_fields($this->relation_objects);
						$this->_add_relation_fields($obj_modelo);
					}

					return $obj_modelo;
				});
		}
		elseif ($tipo === 'count')
		{
			return $this->db->from($this->tabla)->count_all_results();
		}
		elseif ($tipo === 'list')
		{
			$opc_ini = collect(array_get($param, 'opc_ini', TRUE)
				? ['' => 'Seleccione '.strtolower($this->label).'...']
				: []
			);

			$opciones = collect($this->db->get($this->tabla)->result_array())
				->map_with_keys(function ($registro) {
					$obj_modelo = new $this->model_class($registro);

					return [$obj_modelo->get_id() => (string) $obj_modelo];
				});

			return $opc_ini->merge($opciones)->all();
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
		// junta los arreglos de campos id con los valores
		$model_fields = $this->fields;

		// en caso que los id sean INT o ID, transforma los valores a (int)
		$arr_condiciones = collect($this->campo_id)
				->combine(explode($this->separador_campos, $id_modelo))
				->map(function($valor, $llave) use($model_fields) {
					return in_array(collect($model_fields)->get($llave)->get_tipo(),
						[Orm_field::TIPO_ID, Orm_field::TIPO_INT])
						? (int) $valor : $valor;
				})->all();

		$this->find('first', ['conditions' => $arr_condiciones], $recupera_relation);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera listado paginado
	 *
	 * @param  integer $page Numero de página a recuperar
	 * @return void
	 */
	public function paginate($page = NULL)
	{
		$page = is_null($page) ? request($this->page_name, 1) : $page;

		return $this->find('all', [
			'filtro' => $this->filtro,
			'limit'  => $this->page_results,
			'offset' => ($page - 1) * $this->page_results,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve campos select de una lista para ser consulados como un solo
	 * campo de la base de datos
	 *
	 * @param  array $campos Arreglo con el listado de campos
	 * @return string        Listado de campos unidos por la BD
	 */
	private function _junta_campos_select($campos = [])
	{
		$campos = collect($campos);

		if ($campos->count() === 1)
		{
			return $campos->first();
		}

		// CONCAT_WS es especifico para MYSQL
		if ($this->db->dbdriver === 'mysqli')
		{
			$lista_campos = $campos->implode(',');

			return "CONCAT_WS('{$this->separador_campos}',{$lista_campos})";
		}

		return $campos->implode("+'{$this->separador_campos}'+");
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
	public function fill_from_array($arr_data = [])
	{
		collect($this->fields)
			->only(collect($arr_data)->keys()->all())
			->each(function($campo, $nombre_campo) use ($arr_data) {
				if (in_array($campo->get_tipo(), [Orm_field::TIPO_ID, Orm_field::TIPO_BOOLEAN, Orm_field::TIPO_INT]))
				{
					$this->values[$nombre_campo] = (int) $arr_data[$nombre_campo];
				}
				elseif ($campo->get_tipo() === Orm_field::TIPO_DATETIME AND $arr_data[$nombre_campo] !== '')
				{
					$this->values[$nombre_campo] = date('Y-m-d H:i:s', strtotime($arr_data[$nombre_campo]));
				}
				else
				{
					$this->values[$nombre_campo] = $arr_data[$nombre_campo];
				}
			});

		return $this;
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
		if ( ! empty($filtro))
		{
			$arr_like = collect($this->fields)
				->filter(function ($field) {return $field->get_tipo() === Orm_field::TIPO_CHAR;})
				->map(function($elem) use ($filtro) {return $filtro;})
				->all();

			if (count($arr_like) > 0)
			{
				$this->db->or_like($arr_like, $filtro, 'both');
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
		if ($filtro !== '_' && $filtro !== '')
		{
			$this->_put_filtro($filtro);
		}

		return $this->db->count_all_results($this->tabla);
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
		$data_update  = [];
		$data_where   = [];
		$es_auto_id   = FALSE;
		$es_insert    = FALSE;

		foreach ($this->fields as $nombre => $campo)
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
				$es_insert = empty($valor) ? TRUE : FALSE;
			}
		}
		else
		{
			$es_insert = ($this->db->get_where($this->tabla, $data_where)->row() === NULL)
							OR ($this->db->get_where($this->tabla, $data_where)->num_rows() === 0);
		}

		// NUEVO REGISTRO
		if ($es_insert)
		{
			if ( ! $es_auto_id)
			{
				$this->db->insert($this->tabla, array_merge($data_where, $data_update));
			}
			else
			{
				$this->db->insert($this->tabla, $data_update);

				foreach ($this->campo_id as $campo_id)
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
			$this->db->update($this->tabla, $data_update);
		}

		// Revisa todos los campos en busqueda de relaciones has_many,
		// para actualizar la tabla relacionada
		foreach ($this->fields as $nombre => $campo)
		{
			if ($campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				$relation = $campo->get_relation();

				$arr_where_delete = [];
				$data_where_tmp = $data_where;

				foreach ($relation['id_one_table'] as $id_one_table_key)
				{
					$arr_where_delete[$id_one_table_key] = array_shift($data_where_tmp);
				}

				$this->db->delete($relation['join_table'], $arr_where_delete);

				foreach ($this->$nombre as $valor_campo)
				{
					$arr_values = [];
					$data_where_tmp = $data_where;

					foreach ($relation['id_one_table'] as $id_one_table_key)
					{
						$arr_values[$id_one_table_key] = array_shift($data_where_tmp);
					}

					$arr_many_valores = explode($this->separador_campos, $valor_campo);

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
		$data_where = [];

		foreach ($this->fields as $nombre => $campo)
		{
			if ($campo->get_es_id())
			{
				$data_where[$nombre] = $this->$nombre;
			}
		}

		$this->db->delete($this->tabla, $data_where);

		foreach ($this->fields as $nombre => $campo)
		{
			if ($campo->get_tipo() === Orm_field::TIPO_HAS_MANY)
			{
				$relation = $campo->get_relation();
				$arr_where_delete = [];
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
