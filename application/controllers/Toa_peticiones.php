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
 * Clase Controller Reporte de consumos TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_peticiones extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'AhYLaPntOUUC';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('toa');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		return $this->peticion();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de una peticion
	 *
	 * @param  string $peticion Identificador de la peticion
	 * @return void
	 */
	public function peticion($peticion = NULL)
	{
		redirect('/toa_consumos/detalle_peticion/'.$peticion);
	}

}
/* End of file Toa_peticiones.php */
/* Location: ./application/controllers/Toa_peticiones.php */
