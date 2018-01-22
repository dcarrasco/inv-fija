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
	protected $benchmark;
	protected $hooks;
	protected $config;
	protected $log;
	protected $utf8;
	protected $uri;
	protected $router;
	protected $output;
	protected $security;
	protected $input;
	protected $lang;
	protected $form_validation;
	protected $session;
	protected $db;
	protected $load;
	protected $table;

	protected static $instance = NULL;

	protected $ci_objects = ['benchmark', 'hooks', 'config', 'log', 'utf8', 'uri', 'router', 'output', 'security', 'input', 'lang', 'form_validation', 'session', 'db', 'load', 'table'];


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
