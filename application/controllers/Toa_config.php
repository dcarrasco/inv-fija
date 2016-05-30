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

		$this->arr_menu = array(
			'tecnico_toa' => array(
				'url'   => $this->router->class . '/listado/tecnico_toa',
				'texto' => $this->lang->line('toa_config_menu_tecnico'),
				'icon'  => 'user',
			),
			'empresa_toa' => array(
				'url'   => $this->router->class . '/listado/empresa_toa',
				'texto' => $this->lang->line('toa_config_menu_empresa'),
				'icon'  => 'home',
			),
			'tipo_trabajo_toa' => array(
				'url'   => $this->router->class . '/listado/tipo_trabajo_toa',
				'texto' => $this->lang->line('toa_config_menu_tipo_trabajo'),
				'icon'  => 'television',
			),
			'tip_material_trabajo_toa' => array(
				'url'   => $this->router->class . '/listado/tip_material_trabajo_toa',
				'texto' => $this->lang->line('toa_config_menu_tipo_material_trabajo'),
				'icon'  => 'object-group',
			),
			'ciudad_toa' => array(
				'url'   => $this->router->class . '/listado/ciudad_toa',
				'texto' => $this->lang->line('toa_config_menu_ciudad'),
				'icon'  => 'map-marker',
			),
			'empresa_ciudad_toa' => array(
				'url'   => $this->router->class . '/listado/empresa_ciudad_toa',
				'texto' => $this->lang->line('toa_config_menu_empresa_ciudad'),
				'icon'  => 'map-marker',
			),
		);
	}

	public function get_select_ciudad($id_empresa = NULL)
	{
		$empresa_ciudad = new Empresa_ciudad_toa;
		$ciudad = new Ciudad_toa;

		$this->output
			->set_content_type('text')
			->set_output(form_print_options($ciudad->find('list', array('conditions' => array('id_ciudad' => $empresa_ciudad->arr_ciudades_por_empresa($id_empresa))))));
	}


}
/* End of file toa_config.php */
/* Location: ./application/controllers/toa_config.php */