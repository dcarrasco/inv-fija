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

	use Inventario\Tipo_inventario;

/**
 * Clase Controller Configuracion Inventario
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_config extends Orm_controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo  = 'config2';

	/**
	 * Namespace de los modelos
	 *
	 * @var string
	 */
	protected $model_namespace = '\\Inventario\\';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('inventario');

		$this->set_menu_modulo([
			'auditor' => [
				'url'   => $this->router->class . '/listado/auditor/',
				'texto' => $this->lang->line('inventario_config_menu_auditores'),
				'icon'  => 'user',
			],
			'familia' => [
				'url'   => $this->router->class . '/listado/familia',
				'texto' => $this->lang->line('inventario_config_menu_familias'),
				'icon'  => 'th',
			],
			'catalogo' => [
				'url'   => $this->router->class . '/listado/catalogo',
				'texto' => $this->lang->line('inventario_config_menu_materiales'),
				'icon'  => 'barcode',
			],
			'tipo_inventario' => [
				'url'   => $this->router->class . '/listado/tipo_inventario',
				'texto' => $this->lang->line('inventario_config_menu_tipos_inventarios'),
				'icon'  => 'th',
			],
			'inventario' => [
				'url'   => $this->router->class . '/listado/inventario',
				'texto' => $this->lang->line('inventario_config_menu_inventarios'),
				'icon'  => 'list',
			],
			'tipo_ubicacion' => [
				'url'   => $this->router->class . '/listado/tipo_ubicacion',
				'texto' => $this->lang->line('inventario_config_menu_tipo_ubicacion'),
				'icon'  => 'th',
			],
			'ubicaciones' => [
				'url'   => $this->router->class . '/ubicacion_tipo_ubicacion',
				'texto' => $this->lang->line('inventario_config_menu_ubicaciones'),
				'icon'  => 'map-marker',
			],
			'centro' => [
				'url'   => $this->router->class . '/listado/centro',
				'texto' => $this->lang->line('inventario_config_menu_centros'),
				'icon'  => 'th',
			],
			'almacen' => [
				'url'   => $this->router->class . '/listado/almacen',
				'texto' => $this->lang->line('inventario_config_menu_almacenes'),
				'icon'  => 'home',
			],
			'unidad_medida' => [
				'url'   => $this->router->class . '/listado/unidad_medida',
				'texto' => $this->lang->line('inventario_config_menu_unidades_medida'),
				'icon'  => 'balance-scale',
			],
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @param  integer $pagina Numero de pagina a desplegar
	 * @return nada
	 */
	public function ubicacion_tipo_ubicacion($pagina = 0)
	{
		$this->load->model('inventario/ubicacion_model');
		$this->load->library('pagination');

		$limite_por_pagina = 15;
		$this->pagination->initialize($this->_ubicacion_pagination_config());

		$datos_hoja = $this->ubicacion_model->get_ubicacion_tipo_ubicacion($this->_ubicacion_limite_por_pagina(), $pagina);

		if ($this->form_validation->run() === FALSE)
		{
			$arr_combo_tipo_ubic = collect($datos_hoja)->pluck('tipo_inventario')
				->unique()
				->map_with_keys(function($item) {
					return [$item => $this->ubicacion_model->get_combo_tipos_ubicacion($item)];
				})->all();

			app_render_view('ubicacion_tipo_ubicacion', [
				'menu_modulo'            => $this->get_menu_modulo('ubicaciones'),
				'datos_hoja'             => $datos_hoja,
				'combo_tipos_inventario' => Tipo_inventario::create()->find('list'),
				'combo_tipos_ubicacion'  => $arr_combo_tipo_ubic,
				'links_paginas'          => $this->pagination->create_links(),
				'url_form'               => "{$this->router->class}/update_ubicacion_tipo_ubicacion/{$pagina}",
			]);
		}
	}

	// --------------------------------------------------------------------
	/**
	 * Ejecuta asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @param  integer $pagina Numero de pagina a desplegar
	 * @return nada
	 */
	public function update_ubicacion_tipo_ubicacion($pagina = 0)
	{
		$this->load->model('inventario/ubicacion_model');
		$datos_hoja = $this->ubicacion_model->get_ubicacion_tipo_ubicacion($this->_ubicacion_limite_por_pagina(), $pagina);

		$rules = [
			'editar'  => $this->ubicacion_model->get_validation_edit($datos_hoja),
			'agregar' => $this->ubicacion_model->get_validation_add(),
			'borrar'  => [['field'=>'id_borrar', 'label'=>'', 'rules'=>'trim|required']],
		];
		route_validation(array_get($rules, request('formulario'), []));

		if (request('formulario') === 'editar')
		{
			$cant_modif = 0;

			foreach($datos_hoja as $registro)
			{
				if ((request($registro['id'].'-tipo_inventario') !== $registro['tipo_inventario']) OR
					(request($registro['id'].'-ubicacion')       !== $registro['ubicacion']) OR
					(request($registro['id'].'-tipo_ubicacion')  !== $registro['id_tipo_ubicacion']))
				{
					$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
						$registro['id'],
						request($registro['id'] . '-tipo_inventario'),
						request($registro['id'] . '-ubicacion'),
						request($registro['id'] . '-tipo_ubicacion')
					);
					$cant_modif += 1;
				}
			}
			set_message(
				($cant_modif > 0)
					? $cant_modif . ' inventario(s) modificados correctamente'
					: ''
			);
		}
		elseif (request('formulario') === 'agregar')
		{
			$count = 0;

			foreach(request('agr-ubicacion') as $valor)
			{
				$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
					0,
					request('agr-tipo_inventario'),
					$valor,
					request('agr-tipo_ubicacion')
				);
				$count += 1;
			}

			set_message('Se agregaron ' . $count . ' ubicaciones correctamente');
		}
		elseif (request('formulario') === 'borrar')
		{
			$this->ubicacion_model->borrar_ubicacion_tipo_ubicacion(request('id_borrar'));
			set_message('Registro (id=' . request('id_borrar') . ') borrado correctamente');
		}

		redirect($this->router->class . '/ubicacion_tipo_ubicacion/' . $pagina);
	}

	// --------------------------------------------------------------------

	private function _ubicacion_limite_por_pagina()
	{
		$limite_por_pagina = 15;

		return $limite_por_pagina;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la configuración del paginador de ubicaciones
	 * @return array
	 */
	private function _ubicacion_pagination_config()
	{
		return [
			'total_rows'  => $this->ubicacion_model->total_ubicacion_tipo_ubicacion(),
			'per_page'    => $this->_ubicacion_limite_por_pagina(),
			'base_url'    => site_url($this->router->class . '/ubicacion_tipo_ubicacion'),
			'uri_segment' => 3,
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

			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',
		];
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string JSON con las ubicaciones libres
	 *
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con las ubicaciones libres
	 */
	public function get_json_ubicaciones_libres($tipo_inventario = '')
	{
		$this->load->model('inventario/ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_ubicaciones_libres($tipo_inventario);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arr_ubic));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string JSON con los tipos de ubicaciones
	 *
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con los tipos de ubicacion
	 */
	public function get_json_tipo_ubicacion($tipo_inventario = '')
	{
		$this->load->model('inventario/ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_combo_tipos_ubicacion($tipo_inventario);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arr_ubic));
	}



}
/* End of file inventario_config.php */
/* Location: ./application/controllers/inventario_config.php */
