<?php
namespace Inventario;

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

use Model\Orm_model;
use Model\Orm_field;

/**
 * Clase Modelo Tipo de Ubicacion
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
class Tipo_ubicacion extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_ubicacion Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_ubicacion = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tipo_ubicacion'),
				'label'        => 'Tipo de ubicaci&oacute;n',
				'label_plural' => 'Tipos de ubicaci&oacute;n',
				'order_by'     => 'tipo_inventario, tipo_ubicacion',
			],
			'campos' => [
				'id'              => ['tipo' => Orm_field::TIPO_ID],
				'tipo_inventario' => [
					'tipo'           =>  Orm_field::TIPO_HAS_ONE,
					'relation'       => ['model' => tipo_inventario::class],
					'texto_ayuda'    => 'Seleccione el tipo de inventario.',
					'es_obligatorio' => TRUE,
				],
				'tipo_ubicacion' => [
					'label'          => 'Tipo de ubicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'M&aacute;ximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
			],
		];

		parent::__construct($id_tipo_ubicacion);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Tipo de Ubicacion
	 */
	public function __toString()
	{
		return (string) $this->tipo_ubicacion;
	}

}
/* End of file tipo_ubicacion.php */
/* Location: ./application/models/Inventario/tipo_ubicacion.php */
