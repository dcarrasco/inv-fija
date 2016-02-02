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
 * Clase Modelo Detalle de inventario
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Detalle_inventario extends ORM_Model {

	/**
	 * Define la cantidad de registros por página para los ajustes
	 *
	 * @var integer
	 */
	private $per_page_ajustes = 50;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_detalle Identificador del detalle de inventario
	 * @return void
	 */
	public function __construct($id_detalle = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_detalle_inventario'),
				'model_label'        => 'Detalle inventario',
				'model_label_plural' => 'Detalles inventario',
				'model_order_by'     => 'id',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'id_inventario' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'inventario',
					),
				),
				'hoja' => array(
					'label'          => 'Hoja',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'N&uacute;mero de la hoja usada en el inventario',
					'es_obligatorio' => TRUE,
				),
				'ubicacion' => array(
					'label'          => 'Ubicaci&oacute;n del material',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Indica la posici&oacute;n del material en el almac&eacute;n.',
					'es_obligatorio' => TRUE,
				),
				'hu' => array(
					'label'          => 'HU del material',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Indica la HU del material en el almac&eacute;n.',
					'es_obligatorio' => FALSE,
				),
				'catalogo' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'catalogo',
					),
					'texto_ayuda'    => 'Cat&aacute;logo del material.',
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del material',
					'tipo'           => 'char',
					'largo'          => 45,
					'texto_ayuda'    => 'M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'lote' => array(
					'label'          => 'Lote del material',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Lote del material.',
					'es_obligatorio' => TRUE,
				),
				'centro' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'centro'
					),
				),
				'almacen' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'almacen'
					),
				),
				'um' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'unidad_medida'
					),
				),
				'stock_sap' => array(
					'label'          => 'Stock SAP del material',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Stock sist&eacute;mico (SAP) del material.',
					'es_obligatorio' => TRUE,
				),
				'stock_fisico' => array(
					'label'          => 'Stock f&iacute;sico del material',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Stock f&iacute;sico (inventariado) del material.',
					'es_obligatorio' => TRUE,
				),
				'digitador' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'usuario',
					),
					'texto_ayuda'    => 'Digitador de la hoja.',
				),
				'auditor' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model'      => 'auditor',
						'conditions' => array('activo' => 1),
					),
					'texto_ayuda'    => 'Auditor de la hoja.',
				),
				'reg_nuevo' => array(
					'label'          => 'Registro nuevo',
					'tipo'           => 'char',
					'largo'          => 1,
					'texto_ayuda'    => 'Indica si el registro es nuevo.',
					'es_obligatorio' => TRUE,
				),
				'fecha_modificacion' => array(
					'label'          => 'Fecha de modificacion',
					'tipo'           => 'datetime',
					'texto_ayuda'    => 'Fecha de modificaci&oacute;n del registro.',
					'es_obligatorio' => TRUE,
				),
				'observacion' => array(
					'label'          => 'Observaci&oacute;n de registro',
					'tipo'           => 'char',
					'largo'          => 200,
					'texto_ayuda'    => 'M&aacute;ximo 200 caracteres.',
				),
				'stock_ajuste' => array(
					'label'          => 'Stock de ajuste del material',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 100 caracteres.',
				),
				'glosa_ajuste' => array(
					'label'          => 'Observaci&oacute;n del ajuste',
					'tipo'           => 'char',
					'largo'          => 100,
					'texto_ayuda'    => 'M&aacute;ximo 100 caracteres.',
				),
				'fecha_ajuste' => array(
					'label'          => 'Fecha del ajuste',
					'tipo'           => 'datetime',
					'texto_ayuda'    => 'Fecha de modificacion del ajuste.',
				),
			),
		);
		$this->config_model($arr_config);


		if ($id_detalle)
		{
			$this->fill($id_detalle);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Detalle de inventario (hoja)
	 */
	public function __toString()
	{
		return (string) $this->hoja;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve registros de inventario de una hoja específica
	 *
	 * @param  integer $id_inventario ID inventario a rescatar
	 * @param  integer $hoja          Numero de la hoja a rescatar
	 * @return Collection             Registros de detalle inventario
	 */
	public function get_hoja($id_inventario = 0, $hoja = 0)
	{
		return $this->find('all', array(
			'conditions' => array('id_inventario' => $id_inventario, 'hoja' => $hoja),
			'order_by'   => 'ubicacion, catalogo, id'
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve ID del auditor de una hoja específica
	 *
	 * @param  integer $id_inventario ID inventario a rescatar
	 * @param  integer $hoja          Numero de la hoja a rescatar
	 * @return integer                ID auditor
	 */
	public function get_auditor_hoja($id_inventario = 0, $hoja = 0)
	{
		return $this->db
			->select_max('auditor')
			->where('id_inventario', $id_inventario)
			->where('hoja', $hoja)
			->get($this->get_model_tabla())
			->row()
			->auditor;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera nombre del digitador
	 *
	 * @return integer ID digitador
	 */
	public function get_nombre_digitador()
	{
		if ($this->digitador !== 0)
		{
			return $this->digitador;
		}
		else
		{
			return '[nombre digitador]';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera las lineas de detalle del inventario para ajustar
	 * @param  integer $id_inventario         ID del inventario a consultar
	 * @param  integer $ocultar_regularizadas indicador si se ocultan los registros ya regularizados
	 * @param  integer $pagina                Pagina a mostrar
	 * @return Collection                     Arreglo con el detalle de los registros
	 */
	public function get_ajustes($id_inventario = 0, $ocultar_regularizadas = 0, $pagina = 0)
	{
		$arr_detalles = $this->db
			->order_by('catalogo, lote, centro, almacen, ubicacion')
			->where('id_inventario', $id_inventario)
			->where(($ocultar_regularizadas === 1)
				? 'stock_fisico - stock_sap + stock_ajuste <> 0'
				: 'stock_fisico - stock_sap <> 0',
				NULL, FALSE)
			->limit($this->per_page_ajustes, $pagina)
			->get($this->get_model_tabla())
			->result_array();

		$detalles_collection = new Collection();
		foreach($arr_detalles as $linea_detalle)
		{
			$detalles_collection->add_item(new Detalle_inventario($linea_detalle));
		}

		return $detalles_collection;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera paginación de las lineas de detalle del inventario para ajustar
	 * @param  integer $id_inventario         ID del inventario a consultar
	 * @param  integer $ocultar_regularizadas indicador si se ocultan los registros ya regularizados
	 * @param  integer $pagina                Pagina a mostrar
	 * @return array                          Arreglo con el detalle de los registros
	 */
	public function get_pagination_ajustes($id_inventario = 0, $ocultar_regularizadas = 0, $pagina = 0)
	{
		//determina la cantidad de registros
		$total_rows = $this->db
			->where('id_inventario', $id_inventario)
			->where(($ocultar_regularizadas === 1)
				? 'stock_fisico - stock_sap + stock_ajuste <> 0'
				: 'stock_fisico - stock_sap <> 0',
				NULL, FALSE)
			->count_all_results($this->get_model_tabla());

		$this->load->library('pagination');

		$cfg_pagination = array(
			'uri_segment' => 4,
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

			'per_page'    => $this->per_page_ajustes,
			'total_rows'  => $total_rows,
			'base_url'    => site_url($this->router->class . '/ajustes/' . $ocultar_regularizadas . '/'),
			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo (' . (int)($total_rows / $this->per_page_ajustes) . ')',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',
		);

		$this->pagination->initialize($cfg_pagination);

		return $this->pagination->create_links();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo validación de un conjunto de lineas de detalle
	 *
	 * @param  Collection $data_collection Colección de objetos con el conjunto de datos a validar
	 * @return array                       Arreglo con reglas de validación
	 */
	public function get_validation_ajustes(Collection $data_collection)
	{
		$arr_validation = array();

		if ($data_collection->length())
		{
			foreach($data_collection as $linea_detalle)
			{
				array_push($arr_validation, array(
					'field' => 'stock_ajuste_'.$linea_detalle->id,
					'label' => 'cantidad',
					'rules' => 'trim|integer'
				));
				array_push($arr_validation, array(
					'field' => 'observacion_'.$linea_detalle->id,
					'label' => 'observacion',
					'rules' => 'trim'
				));
			}
		}

		return $arr_validation;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo validación de un conjunto de lineas de detalle
	 *
	 * @param  Collection $data_collection Colección de objetos con el conjunto de datos a validar
	 * @return array                       Arreglo con reglas de validación
	 */
	public function get_validation_digitacion(Collection $data_collection)
	{
		$arr_validation = array();

		if ($data_collection->length() > 0)
		{
			foreach($data_collection as $linea_detalle)
			{
				array_push($arr_validation, array(
					'field' => 'stock_fisico_'.$linea_detalle->id,
					'label' => 'Cantidad',
					'rules' => 'trim|required|integer|greater_than[-1]'
				));
				array_push($arr_validation, array(
					'field' => 'hu'.$linea_detalle->id,
					'label' => 'HU',
					'rules' => 'trim'
				));
				array_push($arr_validation, array(
					'field' => 'observacion'.$linea_detalle->id,
					'label' => 'Observacion',
					'rules' => 'trim'
				));
			}
		}

		return $arr_validation;
	}

	// --------------------------------------------------------------------

	/**
	 * Setea validación para formulario editar/agregar linea de detalle
	 *
	 * @return void
	 */
	public function get_validation_editar()
	{
		$this->set_validation_rules_field('ubicacion');
		$this->set_validation_rules_field('hu');
		$this->set_validation_rules_field('catalogo');
		$this->set_validation_rules_field('lote');
		$this->set_validation_rules_field('centro');
		$this->set_validation_rules_field('almacen');
		$this->set_validation_rules_field('um');
		$this->set_validation_rules_field('stock_fisico');
	}

	// --------------------------------------------------------------------

	/**
	 * Setea validación para formulario editar/agregar linea de detalle
	 *
	 * @return void
	 */
	public function get_validation_editar_mobile()
	{
		$this->set_validation_rules_field('stock_fisico');
		$this->set_validation_rules_field('hu');
		$this->set_validation_rules_field('observacion');
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera información del post en para editar registro de inventario
	 *
	 * @param  integer $id_detalle ID del registro detalle inventario
	 * @param  integer $hoja       Numero de la hoja del registro de detalle de inventario
	 *
	 * @return void
	 */
	public function get_editar_post_data($id_detalle = NULL, $hoja = 0)
	{
		$ci =& get_instance();

		// recupera el inventario activo
		$inventario = new Inventario;
		$id_inventario = $inventario->get_id_inventario_activo();

		// recupera el nombre del material
		$material = new Catalogo;
		$material->find_id($ci->input->post('catalogo'));

		if ($id_detalle)
		{
			$this->find_id($id_detalle);
		}
		$id_auditor = $this->get_auditor_hoja($id_inventario, $hoja);

		$this->fill(array(
			'id'                 => (int) $id_detalle,
			'id_inventario'      => (int) $id_inventario,
			'hoja'               => (int) $hoja,
			'ubicacion'          => strtoupper($ci->input->post('ubicacion')),
			'hu'                 => strtoupper($ci->input->post('hu')),
			'catalogo'           => strtoupper($ci->input->post('catalogo')),
			'descripcion'        => $material->descripcion,
			'lote'               => strtoupper($ci->input->post('lote')),
			'centro'             => strtoupper($ci->input->post('centro')),
			'almacen'            => strtoupper($ci->input->post('almacen')),
			'um'                 => strtoupper($ci->input->post('um')),
			'stock_sap'          => 0,
			'stock_fisico'       => (int) $ci->input->post('stock_fisico'),
			'digitador'          => (int) $ci->acl_model->get_id_usr(),
			'auditor'            => (int) $id_auditor,
			'reg_nuevo'          => 'S',
			'fecha_modificacion' => date('Y-m-d H:i:s'),
			'observacion'        => $ci->input->post('observacion'),
			'stock_ajuste'       => 0,
			'glosa_ajuste'       => '',
			'fecha_ajuste'       => NULL,
		));

	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza cantidades y observaciones de un conjunto de lineas de detalle
	 * de acuerdo a información del POST
	 *
	 * @param  Collection $data_collection Colección de objetos con el conjunto de datos
	 * @return integer Cantidad de registros modificados
	 */
	public function update_ajustes(Collection $data_collection)
	{
		$cant_modif = 0;

		foreach($data_collection as $linea_detalle)
		{
			if ( (int) set_value('stock_ajuste_'.$linea_detalle->id) !== (int) $linea_detalle->stock_ajuste OR
				trim(set_value('observacion_'.$linea_detalle->id)) !== trim($linea_detalle->glosa_ajuste) )
			{
				$linea_detalle->stock_ajuste = (int) set_value('stock_ajuste_'.$linea_detalle->id);
				$linea_detalle->glosa_ajuste = trim(set_value('observacion_'.$linea_detalle->id));
				$linea_detalle->fecha_ajuste = date('Y-m-d H:i:s');
				$linea_detalle->grabar();
				$cant_modif += 1;
			}
		}

		return $cant_modif;
	}


	// --------------------------------------------------------------------

	/**
	 * Actualiza un conjunto de lineas de detalle
	 * de acuerdo a información del POST
	 *
	 * @param  integer $id_inventario ID del inventario a actualizar
	 * @param  integer $hoja          Numero de la hoja con los datos a actualizar
	 * @param  integer $id_usr        ID del usuario que está modificando los datos
	 * @return integer                Cantidad de registros modificados
	 */
	public function update_digitacion($id_inventario = 0, $hoja = 0, $id_usr = 0)
	{
		$cant_modif = 0;

		foreach($this->get_hoja($id_inventario, $hoja) as $linea_detalle)
		{
			$linea_detalle->fill(array(
				'digitador'          => $id_usr,
				'auditor'            => set_value('auditor'),
				'stock_fisico'       => set_value('stock_fisico_'.$linea_detalle->id),
				'hu'                 => set_value('hu_'.$linea_detalle->id),
				'observacion'        => set_value('observacion_'.$linea_detalle->id),
				'fecha_modificacion' => date('Y-m-d H:i:s'),
			));

			$linea_detalle->grabar();
			$cant_modif += 1;
		}

		return $cant_modif;
	}
}
/* End of file detalle_inventario.php */
/* Location: ./application/models/detalle_inventario.php */