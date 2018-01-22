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
 * Clase que modela una collección
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class App {

	/**
	 * Arreglo que contiene los datos de la collección
	 *
	 * @var array
	 */
	protected $db;
	protected $form_validation;
	protected $load;
	protected $lang;
	protected $input;
	protected $security;
	protected $output;
	protected $uri;
	protected $utf8;
	protected $log;
	protected $config;
	protected $hooks;
	protected $benchmark;
	protected $table;

	protected static $instance = NULL;

	protected $ci_objects = ['db', 'form_validation', 'load', 'lang', 'input', 'security', 'output', 'uri', 'utf8', 'log', 'config', 'hooks', 'benchmark', 'table'];


	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array $items Arreglo inicial para poblar la colección
	 * @return  void
	 **/
	protected function __construct()
	{
	}

	// --------------------------------------------------------------------

	/**
	 * Inicializa objeto de lal aplicación
	 * @return void
	 */
	public static function instance()
	{
		if (is_null(static::$instance))
		{
			$app = new static;

			$ci =& get_instance();
			foreach ($app->ci_objects as $component)
			{
				if (isset($ci->{$component}))
				{
					$app->add($component, $ci->{$component});
				}
			}

			static::$instance = $app;
		}

		return static::$instance;
	}

	// --------------------------------------------------------------------

	/**
	 * Accede a objetos de la aplicacion
	 * @param  string $item Nombre del objeto
	 * @return mixed
	 */
	public function __get($item = '')
	{
		if (in_array($item, $this->ci_objects))
		{
			return $this->{$item};
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Accede a los objetos de la instancia
	 * @param  string $item Nombre del objeto
	 * @return mixed
	 */
	public static function get($item = NULL)
	{
		if (is_null($item))
		{
			return static::$instance;
		}

		return static::$instance->{$item};
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un objeto a la instancia
	 * @param string $item   Nombre del objeto
	 * @param mixed $object Objeto
	 * @return void
	 */
	public static function add_component($item = '', $object)
	{
		return static::$instance->add($item, $object);
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un objeto a la instancia
	 * @param string $item   Nombre del objeto
	 * @param mixed $object Objeto
	 * @return void
	 */
	public static function mock($item = '', $object)
	{
		return static::$instance->add($item, $object);
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un objeto a la instancia
	 * @param string $item   Nombre del objeto
	 * @param mixed $object Objeto
	 * @return void
	 */
	public function add($component_name = '', $component_object)
	{
		$this->{$component_name} = $component_object;
	}



}
/* End of file App.php */
/* Location: ./application/libraries/App.php */
