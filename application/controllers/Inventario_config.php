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
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Inventario\Ubicacion;
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
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'auditor' => ['url'=>'{{route}}/listado/auditor/', 'texto'=>'lang::inventario_config_menu_auditores', 'icon'=>'user'],
		'familia' => ['url'=>'{{route}}/listado/familia', 'texto'=>'lang::inventario_config_menu_familias', 'icon'=>'th'],
		'catalogo' => ['url'=>'{{route}}/listado/catalogo', 'texto'=>'lang::inventario_config_menu_materiales', 'icon'=>'barcode'],
		'tipo_inventario' => ['url'=>'{{route}}/listado/tipo_inventario', 'texto'=>'lang::inventario_config_menu_tipos_inventarios', 'icon'=>'th'],
		'inventario' => ['url'=>'{{route}}/listado/inventario', 'texto'=>'lang::inventario_config_menu_inventarios', 'icon'=>'list'],
		'tipo_ubicacion' => ['url'=>'{{route}}/listado/tipo_ubicacion', 'texto'=>'lang::inventario_config_menu_tipo_ubicacion', 'icon'=>'th'],
		'ubicaciones' => ['url'=>'{{route}}/ubicacion', 'texto'=>'lang::inventario_config_menu_ubicaciones', 'icon'=>'map-marker'],
		'centro' => ['url'=>'{{route}}/listado/centro', 'texto'=>'lang::inventario_config_menu_centros', 'icon'=>'th'],
		'almacen' => ['url'=>'{{route}}/listado/almacen', 'texto'=>'lang::inventario_config_menu_almacenes', 'icon'=>'home'],
		'unidad_medida' => ['url'=>'{{route}}/listado/unidad_medida', 'texto'=>'lang::inventario_config_menu_unidades_medida', 'icon'=>'balance-scale'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'inventario';

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
	}

	// --------------------------------------------------------------------

	/**
	 * Asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @return nada
	 */
	public function ubicacion()
	{
		$ubicaciones = Ubicacion::create()->paginate();

		$arr_combo_tipo_ubic = collect($ubicaciones)
			->map(function($ubicacion) {
				return $ubicacion->tipo_inventario;
			})->unique()
			->map_with_keys(function($item) {
				return [$item => Ubicacion::create()->get_combo_tipos_ubicacion($item)];
			})->all();

		$errors_id = $this->errors->map_with_keys(function($linea, $linea_key) {
			preg_match('/\[(?<id>\d+)\]/', $linea_key, $matches);
			return [array_get($matches, 'id') => array_get($matches, 'id')];
		})->unique();

		$display_form_agregar = $this->errors->has(['agr-tipo_inventario', 'agr-ubicacion[]', 'agr-tipo_ubicacion'])
			? ''
			: 'display: none;';

		app_render_view('ubicacion_tipo_ubicacion', [
			'menu_modulo'            => $this->get_menu_modulo('ubicaciones'),
			'ubicaciones'            => $ubicaciones,

			'combo_tipos_inventario' => Tipo_inventario::create()->find('list'),
			'combo_tipos_ubicacion'  => $arr_combo_tipo_ubic,
			'add_url_form'           => "{$this->router->class}/add_ubicacion".url_params(),
			'update_url_form'        => "{$this->router->class}/update_ubicacion".url_params(),
			'borrar_url_form'        => "{$this->router->class}/borrar_ubicacion".url_params(),
			'display_form_agregar'   => $display_form_agregar,
			'errors_id'              => $errors_id,
		]);
	}

	// --------------------------------------------------------------------
	/**
	 * Agrega asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @return redirect
	 */
	public function add_ubicacion()
	{
		route_validation(Ubicacion::create()->get_validation_add());

		$count = collect(request('agr-ubicacion'))
			->each(function($ubicacion) {
				return Ubicacion::create()->fill([
					'tipo_inventario'   => request('agr-tipo_inventario'),
					'id_tipo_ubicacion' => request('agr-tipo_ubicacion'),
					'ubicacion'         => $ubicacion,
				])->grabar();
			})->count();
		set_message("Se agregaron {$count} ubicaciones correctamente");

		redirect($this->router->class.'/ubicacion'.url_params());
	}

	// --------------------------------------------------------------------
	/**
	 * Update asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @return redirect
	 */
	public function update_ubicacion()
	{
		$ubicaciones = Ubicacion::create()->paginate();
		route_validation(Ubicacion::create()->get_validation_edit($ubicaciones));

		$cant_modif = collect($ubicaciones)->filter(function($ubicacion) {
			return array_get(request('tipo_inventario'), $ubicacion->id) !== $ubicacion->tipo_inventario
				OR array_get(request('tipo_ubicacion'), $ubicacion->id)  !== $ubicacion->id_tipo_ubicacion
				OR array_get(request('ubicacion'), $ubicacion->id)       !== $ubicacion->ubicacion;
		})->each(function($ubicacion) {
			$ubicacion->fill([
				'tipo_inventario'   => array_get(request('tipo_inventario'), $ubicacion->id),
				'id_tipo_ubicacion' => array_get(request('tipo_ubicacion'), $ubicacion->id),
				'ubicacion'         => array_get(request('ubicacion'), $ubicacion->id),
			])->grabar();
		})->count();
		set_message(($cant_modif > 0) ? $cant_modif . ' inventario(s) modificados correctamente' : '');

		redirect($this->router->class.'/ubicacion'.url_params());
	}

	// --------------------------------------------------------------------
	/**
	 * Borra asociacion de ubicaciones con el tipo de ubicacion
	 *
	 * @return redirect
	 */
	public function borrar_ubicacion()
	{
		route_validation([['field'=>'id_borrar', 'label'=>'', 'rules'=>'trim|required']]);

		Ubicacion::create()->fill(request('id_borrar'))->borrar();
		set_message('Registro (id=' . request('id_borrar') . ') borrado correctamente');

		redirect($this->router->class.'/ubicacion'.url_params());
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
		$arr_ubic = Ubicacion::create()->get_ubicaciones_libres($tipo_inventario);

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
		$arr_ubic = Ubicacion::create()->get_combo_tipos_ubicacion($tipo_inventario);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arr_ubic));
	}

}
// End of file Inventario_config.php
// Location: ./controllers/Inventario_config.php
