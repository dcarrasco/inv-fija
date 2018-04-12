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

namespace Acl;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Rol
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
class Rol extends ORM_Model {

	protected $tabla = 'config::bd_rol';
	protected $label = 'Rol';
	protected $label_plural = 'Roles';
	protected $order_by = 'rol';

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_rol Identificador del modulo
	 * @return void
	 */
	public function __construct($id_rol = NULL)
	{
		$this->model_config = [
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'id_app' => [
					'tipo'        => Orm_field::TIPO_HAS_ONE,
					'relation'    => ['model' => app::class],
					'texto_ayuda' => 'Aplicaci&oacute;n a la que pertenece el rol.',
					'onchange'    => form_onchange('id_app', 'modulo', 'acl_config/get_select_modulo'),
				],
				'rol' => [
					'label'          => 'Rol',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del rol. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'descripcion' => [
					'label'       => 'Descripci&oacute;n del rol',
					'tipo'        => Orm_field::TIPO_CHAR,
					'largo'       => 100,
					'texto_ayuda' => 'Descripci&oacute;n del rol. M&aacute;ximo 100 caracteres.',
				],
				'modulo' => [
					'tipo'     => Orm_field::TIPO_HAS_MANY,
					'relation' => [
						'model'         => modulo::class,
						'join_table'    => 'acl_rol_modulo',
						'id_one_table'  => ['rol_id'],
						'id_many_table' => ['modulo_id'],
						'conditions'    => ['id_app' => '@field_value:id_app:NULL'],
					],
					'texto_ayuda'    => 'M&oacute;dulos del rol.',
				],
			],
		];

		parent::__construct($id_rol);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->rol;
	}

}

// End of file rol.php
// Location: ./models/Acl/rol.php
