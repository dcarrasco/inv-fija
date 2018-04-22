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

use Toa\Ciudad_toa;
use Toa\Empresa_ciudad_toa;

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

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'tecnico_toa'        => ['icon'=>'user'],
		'empresa_toa'        => ['icon'=>'home'],
		'tipo_trabajo_toa'   => ['icon'=>'television'],
		'tip_material_toa'   => ['icon'=>'object-group'],
		'ciudad_toa'         => ['icon'=>'map-marker'],
		'empresa_ciudad_toa' => ['icon'=>'map-marker'],
		'ps_vpi_toa'         => ['icon'=>'list'],
		'clave_cierre'       => ['icon'=>'list'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'toa';

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
			->set_output(form_print_options(Ciudad_toa::create()
				->where('id_ciudad', Empresa_ciudad_toa::create()->ciudades_por_empresa($id_empresa))
				->get_dropdown_list(FALSE)
			));
	}

}
// End of file Toa_config.php
// Location: ./controllers/Toa_config.php
