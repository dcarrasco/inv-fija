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
 * Clase que modela una interfaz para Google Maps
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class Google_map {

	/**
	 * Arreglo que contiene los marcadores de posicion
	 *
	 * @var array
	 */
	private $_markers = [];

	/**
	 * Identificador del DIV que contendrá el mapa
	 *
	 * @var string
	 */
	private $_map_id = 'canvas_gmap';

	/**
	 * Texto CSS para dar estilo a DIV del mapa
	 *
	 * @var string
	 */
	private $_map_css = 'height: 100px';

	/**
	 * Zoom del mapa
	 *
	 * @var integer
	 */
	private $_map_zoom = 15;

	/**
	 * Texto javascript a escribir
	 *
	 * @var string
	 */
	private $_txt_js = '';

	/**
	 * URL de la API
	 *
	 * @var string
	 */
	private $_url_js = 'https://maps.googleapis.com/maps/api/js';

	/**
	 * Llave de la API
	 *
	 * @var string
	 */
	private $_api_key = 'AIzaSyDLt78yzJgaZAZNaFNvJ4RbD9pY2hSTTY0';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @return  void
	 **/
	public function __construct($config = [])
	{
		if ( ! empty($config))
		{
			return $this->initialize($config);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Inicializa el módulo
	 *
	 * @param  array $config Arreglo con la configuración del módulo
	 * @return void
	 */
	public function initialize($config = [])
	{
		collect($config)->each(function($config_value, $config_key) {
			$this->{'_'.$config_key} = $config_value;
		});

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Crea mapa
	 *
	 * @return void
	 */
	public function create_map()
	{
		$function_name = 'initMap_'.$this->_map_id;

		$arr_url_param = [
			'key'      => $this->_api_key,
			'callback' => $function_name,
		];
		$url_js = $this->_url_js.url_params($arr_url_param);


		$txt_js  = "<div id=\"{$this->_map_id}\" style=\"{$this->_map_css}\"></div>\n";
		$txt_js .= "<script type=\"text/javascript\">\n";
		$txt_js .= "function {$function_name}() {\n";
		$txt_js .= "var map = new google.maps.Map(document.getElementById('{$this->_map_id}'), {center: {lat:0, lng:0}, zoom: {$this->_map_zoom}});\n";
		$txt_js .= "var bounds = new google.maps.LatLngBounds();\n";
		$txt_js .= $this->_txt_js;
		$txt_js .= (count($this->_markers) === 1) ? "map.setCenter(ubic_1);\n" : "map.fitBounds(bounds);\n";
		$txt_js .= "}\n";
		$txt_js .= "</script>\n";
		$txt_js .= "<script type=\"text/javascript\" src=\"{$url_js}\" defer async></script>\n";

		return $txt_js;
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega marcadores desde un arreglo de peticiones TOA
	 *
	 * @param array $peticiones Arreglo de peticiones
	 * @return $this
	 */
	public function add_peticiones_markers($peticiones)
	{
		collect($peticiones)->each(function($peticion) {
			$this->add_marker([
				'lat'   => $peticion['acoord_y'],
				'lng'   => $peticion['acoord_x'],
				'title' => $peticion['empresa'].' - '.$peticion['tecnico'].' - '.$peticion['referencia'],
			]);
		});

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un marcador
	 *
	 * @param  array $marker Definición del marcador
	 * @return void
	 */
	public function add_marker($marker = NULL)
	{
		$marker_config = [
			'lat'    => 0,
			'lng'    => 0,
			'map'    => 'map',
			'title'  => 'title',
			'zindex' => 100,
		];

		$marker_config = collect($marker_config)
			->map(function($marker_item, $index) use ($marker) {
				return array_get($marker, $index, $marker_item);
			})
			->all();

		if ($marker_config['lat'] !== 0 AND $marker_config['lng'] !== 0
			AND $marker_config['lat'] !== $marker_config['lng'] )
		{
			array_push($this->_markers, $marker_config);
			$n_marker = count($this->_markers);

			$this->_txt_js .= "var ubic_{$n_marker} = new google.maps.LatLng({$marker_config['lat']}, {$marker_config['lng']});\n";
			$this->_txt_js .= "var marker_{$n_marker} = new google.maps.Marker({position: ubic_{$n_marker}, title: '{$marker_config['title']}'});\n";
			$this->_txt_js .= "marker_{$n_marker}.setMap(map);\n";
			$this->_txt_js .= "bounds.extend(marker_{$n_marker}.position);\n\n";
		}

		return $this;
	}

	// --------------------------------------------------------------------


}
/* End of file Google_map.php */
/* Location: ./application/libraries/Google_map.php */
