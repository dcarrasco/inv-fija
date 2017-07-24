<?php
namespace Stock;

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
 * Clase Modelo Proveedor
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Proveedor extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_proveedor Identificador del modulo
	 * @return void
	 */
	public function __construct($id_proveedor = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_proveedores'),
				'label'        => 'Proveedor',
				'label_plural' => 'Proveedores',
				'order_by'     => 'des_proveedor',
			],
			'campos' => [
				'cod_proveedor' => [
					'label'          => 'C&oacute;digo del proveedor',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
				'des_proveedor' => [
					'label'          => 'Nombre del proveedor',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
				],
			],
		];

		parent::__construct($id_proveedor);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->des_proveedor;
	}

}
/* End of file proveedor.php */
/* Location: ./application/models/proveedor.php */
