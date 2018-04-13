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

namespace Toa;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Ciudad TOA
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
class Ciudad_toa extends ORM_Model {

	protected $tabla = 'config::bd_ciudades_toa';
	protected $label = 'Ciudad TOA';
	protected $label_plural = 'Ciudades TOA';
	protected $order_by = 'orden';

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_ciudad Identificador de la ciudad
	 * @return void
	 */
	public function __construct($id_ciudad = NULL)
	{
		$this->model_config = [
			'campos' => [
				'id_ciudad' => [
					'label'          => 'ID de la ciudad',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 5,
					'texto_ayuda'    => 'ID de la ciudad. M&aacute;ximo 50 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
				'ciudad' => [
					'label'          => 'Nombre de la ciudad',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la ciudad. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				],
				'orden' => [
					'label'          => 'Orden de la ciudad',
					'tipo'           => Orm_field::TIPO_INT,
					'texto_ayuda'    => 'Orden de despliegue de la ciudad.',
					'es_obligatorio' => TRUE,
				],
			],
		];

		parent::__construct($id_ciudad);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Ciudad
	 */
	public function __toString()
	{
		return (string) $this->ciudad;
	}

}
// End of file Ciudad_toa.php
// Location: ./models/Toa/Ciudad_toa.php
