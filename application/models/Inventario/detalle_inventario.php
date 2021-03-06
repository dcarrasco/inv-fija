<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

namespace Inventario;

use Acl\Acl;
use Collection;
use Model\Orm_model;
use Model\Orm_field;

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

	protected $db_table = 'config::bd_detalle_inventario';
	protected $label = 'Detalle inventario';
	protected $label_plural = 'Detalles inventario';
	protected $order_by = 'id';
	protected $page_results = 20;

	protected $fields = [
		'id' => ['tipo' => Orm_field::TIPO_ID],
		'id_inventario' => [
			'tipo'     => Orm_field::TIPO_HAS_ONE,
			'relation' => ['model' => inventario::class],
		],
		'hoja' => [
			'label'          => 'Hoja',
			'tipo'           => Orm_field::TIPO_INT,
			'largo'          => 10,
			'texto_ayuda'    => 'N&uacute;mero de la hoja usada en el inventario',
			'es_obligatorio' => TRUE,
		],
		'ubicacion' => [
			'label'          => 'Ubicaci&oacute;n del material',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Indica la posici&oacute;n del material en el almac&eacute;n.',
			'es_obligatorio' => TRUE,
		],
		'hu' => [
			'label'          => 'HU del material',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'Indica la HU del material en el almac&eacute;n.',
			'es_obligatorio' => FALSE,
		],
		'catalogo' => [
			'tipo'        => Orm_field::TIPO_HAS_ONE,
			'relation'    => ['model' => catalogo::class],
			'texto_ayuda' => 'Cat&aacute;logo del material.',
		],
		'descripcion' => [
			'label'          => 'Descripci&oacute;n del material',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 45,
			'texto_ayuda'    => 'M&aacute;ximo 45 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'lote' => [
			'label'          => 'Lote del material',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 10,
			'texto_ayuda'    => 'Lote del material.',
			'es_obligatorio' => TRUE,
		],
		'centro' => [
			'tipo'     =>  Orm_field::TIPO_HAS_ONE,
			'relation' => ['model' => centro::class],
		],
		'almacen' => [
			'tipo'     =>  Orm_field::TIPO_HAS_ONE,
			'relation' => ['model' => almacen::class],
		],
		'um' => [
			'tipo'     =>  Orm_field::TIPO_HAS_ONE,
			'relation' => ['model' => unidad_medida::class],
		],
		'stock_sap' => [
			'label'          => 'Stock SAP del material',
			'tipo'           => Orm_field::TIPO_INT,
			'largo'          => 10,
			'texto_ayuda'    => 'Stock sist&eacute;mico (SAP) del material.',
			'es_obligatorio' => TRUE,
		],
		'stock_fisico' => [
			'label'          => 'Stock f&iacute;sico del material',
			'tipo'           => Orm_field::TIPO_INT,
			'largo'          => 10,
			'texto_ayuda'    => 'Stock f&iacute;sico (inventariado) del material.',
			'es_obligatorio' => TRUE,
		],
		'digitador' => [
			'tipo'        => Orm_field::TIPO_HAS_ONE,
			'relation'    => ['model' => \Acl\usuario::class],
			'texto_ayuda' => 'Digitador de la hoja.',
		],
		'auditor' => [
			'tipo'        => Orm_field::TIPO_HAS_ONE,
			'relation'    => [
				'model'      => auditor::class,
				'conditions' => ['activo' => 1],
			],
			'texto_ayuda' => 'Auditor de la hoja.',
		],
		'reg_nuevo' => [
			'label'          => 'Registro nuevo',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 1,
			'texto_ayuda'    => 'Indica si el registro es nuevo.',
			'es_obligatorio' => TRUE,
		],
		'fecha_modificacion' => [
			'label'          => 'Fecha de modificacion',
			'tipo'           => Orm_field::TIPO_DATETIME,
			'texto_ayuda'    => 'Fecha de modificaci&oacute;n del registro.',
			'es_obligatorio' => TRUE,
		],
		'observacion' => [
			'label'       => 'Observaci&oacute;n de registro',
			'tipo'        => Orm_field::TIPO_CHAR,
			'largo'       => 200,
			'texto_ayuda' => 'M&aacute;ximo 200 caracteres.',
		],
		'stock_ajuste' => [
			'label'       => 'Stock de ajuste del material',
			'tipo'        => Orm_field::TIPO_INT,
			'largo'       => 10,
			'texto_ayuda' => 'M&aacute;ximo 100 caracteres.',
		],
		'glosa_ajuste' => [
			'label'       => 'Observaci&oacute;n del ajuste',
			'tipo'        => Orm_field::TIPO_CHAR,
			'largo'       => 100,
			'texto_ayuda' => 'M&aacute;ximo 100 caracteres.',
		],
		'fecha_ajuste' => [
			'label'       => 'Fecha del ajuste',
			'tipo'        => Orm_field::TIPO_DATETIME,
			'texto_ayuda' => 'Fecha de modificacion del ajuste.',
		],
	];

	/**
	 * Define la cantidad de registros por página para los ajustes
	 *
	 * @var integer
	 */
	protected $page_ajustes = 30;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Detalle de inventario (hoja)
	 */
	public function __toString()
	{
		return (string) $this->get_value('hoja');
	}

	// --------------------------------------------------------------------

	public function get_stock_sap()
	{
		return fmt_cantidad($this->get_value('stock_sap'));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve registros de inventario de una hoja específica
	 *
	 * @param  integer $id_inventario ID inventario a rescatar
	 * @param  integer $hoja          Numero de la hoja a rescatar
	 * @return Collection             Registros de detalle inventario
	 */
	public function get_hoja_inventario($id_inventario = 0, $hoja = 0)
	{
		return $this
			->where('id_inventario', $id_inventario)
			->where('hoja', $hoja)
			->order_by('ubicacion, catalogo, id')
			->get();
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
		return $this->get_hoja_inventario($id_inventario, $hoja)
			->map(function($detalle) {
				return $detalle->get_value('auditor');
			})
			->max();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera nombre del digitador
	 *
	 * @return integer ID digitador
	 */
	public function get_nombre_digitador()
	{
		return ($this->digitador !== 0) ? $this->digitador : '[nombre digitador]';
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera las lineas de detalle del inventario para ajustar
	 *
	 * @param  integer $id_inventario         ID del inventario a consultar
	 * @param  integer $ocultar_regularizadas indicador si se ocultan los registros ya regularizados
	 * @return Collection                     Arreglo con el detalle de los registros
	 */
	public function get_ajustes($id_inventario = 0, $ocultar_regularizadas = 0)
	{
		$stock_ajuste = $ocultar_regularizadas === 1 ? '+ stock_ajuste ' : '';

		return $this->where('id_inventario', $id_inventario)
			->where("stock_fisico - stock_sap {$stock_ajuste}<> 0",	NULL, FALSE)
			->order_by('catalogo, lote, centro, almacen, ubicacion')
			->paginate();
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
		$stock_ajuste = $ocultar_regularizadas === 1 ? '+ stock_ajuste ' : '';
		$total_rows   = $this->where('id_inventario', $id_inventario)
			->where("stock_fisico - stock_sap {$stock_ajuste}<> 0",	NULL, FALSE)
			->count();

		$this->load->library('pagination');
		$this->pagination->initialize([
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

			'per_page'    => $this->page_ajustes,
			'total_rows'  => $total_rows,
			'base_url'    => site_url("{$this->router->class}/ajustes"),
			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo ('.(int)($total_rows / $this->page_ajustes).')',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',

			'reuse_query_string'   => TRUE,
			'page_query_string'    => TRUE,
			'query_string_segment' => 'page',
			'use_page_numbers'     => TRUE,
		]);

		return $this->pagination->create_links();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo validación de un conjunto de lineas de detalle
	 *
	 * @param  Collection $data_collection Colección de objetos con el conjunto de datos a validar
	 * @return array                       Arreglo con reglas de validación
	 */
	public function rules_ajustes(Collection $data_collection)
	{
		return $data_collection->map(function($linea_detalle) {
			return [
				[
					'field' => "stock_ajuste_{$linea_detalle->id}",
					'label' => "Cantidad (ubicacion {$linea_detalle->ubicacion}, material {$linea_detalle->catalogo})",
					'rules' => 'trim|integer'
				],
				[
					'field' => "observacion_{$linea_detalle->id}",
					'label' => 'Observacion',
					'rules' => 'trim'
				]
			];
		})->flatten(1)
		->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo validación de un conjunto de lineas de detalle
	 *
	 * @param  Collection $data_collection Colección de objetos con el conjunto de datos a validar
	 * @return array                       Arreglo con reglas de validación
	 */
	public function rules_digitacion(Collection $data_collection)
	{
		return $data_collection->map(function($linea_detalle) {
			return [
				[
					'field' => 'stock_fisico_'.$linea_detalle->id,
					'label' => "Cantidad (ubicacion {$linea_detalle->ubicacion}, material {$linea_detalle->catalogo})",
					'rules' => 'trim|required|integer|greater_than[-1]'
				],
				[
					'field' => 'hu_'.$linea_detalle->id,
					'label' => 'HU',
					'rules' => 'trim'
				],
				[
					'field' => 'observacion_'.$linea_detalle->id,
					'label' => 'Observacion',
					'rules' => 'trim'
				]
			];
		})->flatten(1)
		->all();

	return $rules;
	}

	// --------------------------------------------------------------------

	/**
	 * Reglas de validacion para formulario upload
	 *
	 * @return array
	 */
	public function rules_upload()
	{
		return [
			[
				'field' => 'upload_file',
				'label' => 'Archivo a subir',
				'rules' => ''
			],
			[
				'field' => 'upload_password',
				'label' => 'Clave',
				'rules' => 'trim|required|in_list[logistica2012]'
			],
		];
	}


	// --------------------------------------------------------------------

	/**
	 * Setea validación para formulario editar/agregar linea de detalle
	 *
	 * @return void
	 */
	public function get_validation_editar()
	{
		$this->set_field_validation_rules('ubicacion');
		$this->set_field_validation_rules('hu');
		$this->set_field_validation_rules('catalogo');
		$this->set_field_validation_rules('lote');
		$this->set_field_validation_rules('centro');
		$this->set_field_validation_rules('almacen');
		$this->set_field_validation_rules('um');
		$this->set_field_validation_rules('stock_fisico');
	}

	// --------------------------------------------------------------------

	/**
	 * Setea validación para formulario editar/agregar linea de detalle
	 *
	 * @return void
	 */
	public function get_validation_editar_mobile()
	{
		$this->set_field_validation_rules('stock_fisico');
		$this->set_field_validation_rules('hu');
		$this->set_field_validation_rules('observacion');
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
		$id_inventario = Inventario::create()->get_id_inventario_activo();
		$material      = (new Catalogo)->find_id(request('catalogo'));
		$id_auditor    = $this->get_auditor_hoja($id_inventario, $hoja);

		if ($id_detalle)
		{
			$this->find_id($id_detalle);
		}

		return $this->fill([
			'id'                 => (int) $id_detalle,
			'id_inventario'      => (int) $id_inventario,
			'hoja'               => (int) $hoja,
			'ubicacion'          => strtoupper(request('ubicacion')),
			'hu'                 => strtoupper(request('hu')),
			'catalogo'           => strtoupper(request('catalogo')),
			'descripcion'        => $material->descripcion,
			'lote'               => strtoupper(request('lote')),
			'centro'             => strtoupper(request('centro')),
			'almacen'            => strtoupper(request('almacen')),
			'um'                 => strtoupper(request('um')),
			'stock_sap'          => 0,
			'stock_fisico'       => (int) request('stock_fisico'),
			'digitador'          => (int) Acl::create()->get_id_usr(),
			'auditor'            => (int) $id_auditor,
			'reg_nuevo'          => 'S',
			'fecha_modificacion' => date('Y-m-d H:i:s'),
			'observacion'        => request('observacion'),
			'stock_ajuste'       => 0,
			'glosa_ajuste'       => '',
			'fecha_ajuste'       => NULL,
		]);
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
		return $data_collection
			->filter(function($linea_detalle) {
				return ((int) request('stock_ajuste_'.$linea_detalle->id) !== (int) $linea_detalle->stock_ajuste)
					OR (trim(request('observacion_'.$linea_detalle->id)) !== trim($linea_detalle->glosa_ajuste));
			})->map(function($linea_detalle) {
				return $linea_detalle
					->fill([
						'stock_ajuste' => (int) request('stock_ajuste_'.$linea_detalle->id),
						'glosa_ajuste' => trim(request('observacion_'.$linea_detalle->id)),
						'fecha_ajuste' => date('Y-m-d H:i:s'),
					])->grabar();
			})->count();
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
		return $this->get_hoja_inventario($id_inventario, $hoja)
			->map(function($linea_detalle) use ($id_usr) {
				return $linea_detalle->fill([
					'digitador'          => $id_usr,
					'auditor'            => request('auditor'),
					'stock_fisico'       => request('stock_fisico_'.$linea_detalle->id),
					'hu'                 => request('hu_'.$linea_detalle->id),
					'observacion'        => request('observacion_'.$linea_detalle->id),
					'fecha_modificacion' => date('Y-m-d H:i:s'),
				])->grabar();
			})->count();
	}
}

// End of file detalle_inventario.php
// Location: ./models/Inventario/detalle_inventario.php
