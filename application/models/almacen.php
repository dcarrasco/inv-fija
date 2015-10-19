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
 * Clase Modelo Almacén
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
class Almacen extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_almacen Identificador del almacen
	 * @return void
	 */
	public function __construct($id_almacen = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_almacenes'),
				'model_label'        => 'Almac&eacute;n',
				'model_label_plural' => 'Almacenes',
				'model_order_by'     => 'almacen',
			),
			'campos' => array(
				'almacen' => array(
					'label'          => 'Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Nombre del almac&eacute;n. M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_almacen)
		{
			$this->fill($id_almacen);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Almacén
	 */
	public function __toString()
	{
		return (string) $this->almacen;
	}

}

/* End of file almacen.php */
/* Location: ./application/models/almacen.php */