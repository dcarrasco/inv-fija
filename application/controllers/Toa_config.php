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

use Toa\Ciudad_toa;
use Toa\Empresa_ciudad_toa;

/**
 * Clase Controller Configuracion de TOA
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_config extends Orm_controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo  = 'config_toa';

	/**
	 * Namespace de los modelos
	 *
	 * @var string
	 */
	protected $model_namespace = '\\Toa\\';

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
		$this->lang->load('toa');

		$this->set_menu_modulo([
			'tecnico_toa' => [
				'url'   => $this->router->class . '/listado/tecnico_toa',
				'texto' => $this->lang->line('toa_config_menu_tecnico'),
				'icon'  => 'user',
			],
			'empresa_toa' => [
				'url'   => $this->router->class . '/listado/empresa_toa',
				'texto' => $this->lang->line('toa_config_menu_empresa'),
				'icon'  => 'home',
			],
			'tipo_trabajo_toa' => [
				'url'   => $this->router->class . '/listado/tipo_trabajo_toa',
				'texto' => $this->lang->line('toa_config_menu_tipo_trabajo'),
				'icon'  => 'television',
			],
			'tip_material_toa' => [
				'url'   => $this->router->class . '/listado/tip_material_toa',
				'texto' => $this->lang->line('toa_config_menu_tipo_material'),
				'icon'  => 'object-group',
			],
			'ciudad_toa' => [
				'url'   => $this->router->class . '/listado/ciudad_toa',
				'texto' => $this->lang->line('toa_config_menu_ciudad'),
				'icon'  => 'map-marker',
			],
			'empresa_ciudad_toa' => [
				'url'   => $this->router->class . '/listado/empresa_ciudad_toa',
				'texto' => $this->lang->line('toa_config_menu_empresa_ciudad'),
				'icon'  => 'map-marker',
			],
			'ps_vpi_toa' => [
				'url'   => $this->router->class . '/listado/ps_vpi_toa',
				'texto' => $this->lang->line('toa_config_menu_ps_vpi'),
				'icon'  => 'list',
			],
			'clave_cierre' => [
				'url'   => $this->router->class . '/listado/clave_cierre',
				'texto' => $this->lang->line('toa_config_menu_clave_cierre'),
				'icon'  => 'list',
			],
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera ciudades
	 *
	 * @param  string $id_empresa Identificador de la empresa
	 * @return string             Elementos de option de las ciudades de la empresa
	 */
	public function get_select_ciudad($id_empresa = NULL)
	{
		$this->output
			->set_content_type('text')
			->set_output(form_print_options(Ciudad_toa::create()->find('list', [
				'conditions' => ['id_ciudad' => Empresa_ciudad_toa::create()->ciudades_por_empresa($id_empresa)],
				'opc_ini'    => FALSE,
			])));
	}

}
/* End of file toa_config.php */
/* Location: ./application/controllers/toa_config.php */
