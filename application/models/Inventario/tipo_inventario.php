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

use \ORM_Model;
use \ORM_Field;

/**
 * Clase Modelo Tipo de Inventario
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
class Tipo_inventario extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_inventario Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_inventario = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tipos_inventario'),
				'label'        => 'Tipo de inventario',
				'label_plural' => 'Tipos de inventario',
				'order_by'     => 'desc_tipo_inventario',
			],
			'campos' => [
				'id_tipo_inventario' => [
					'label'          => 'Tipo de inventario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
				'desc_tipo_inventario' => [
					'label'          => 'Descripci&oacute;n tipo de inventario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del tipo de inventario. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
			],
		];

		parent::__construct($id_tipo_inventario);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Tipo Inventario
	 */
	public function __toString()
	{
		return (string) $this->desc_tipo_inventario;
	}

}

/* End of file tipo_inventario.php */
/* Location: ./application/models/Inventario/tipo_inventario.php */
