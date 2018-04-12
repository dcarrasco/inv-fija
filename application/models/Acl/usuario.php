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
 * Clase Modelo Usuario
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
class Usuario extends ORM_Model {

	protected $tabla = 'config::bd_usuarios';
	protected $label = 'Usuario';
	protected $label_plural = 'Usuarios';
	protected $order_by = 'nombre';

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_usuario Identificador del modulo
	 * @return void
	 */
	public function __construct($id_usuario = NULL)
	{
		$this->model_config = [
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'nombre' => [
					'label'          => 'Nombre de usuario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 45,
					'texto_ayuda'    => 'Nombre del usuario. M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],

				// 'tipo' => [
				// 	'label'          => 'Descripcion de la Aplicacion',
				// 	'tipo'           => Orm_field::TIPO_CHAR,
				// 	'largo'          => 50,
				// 	'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
				// 	'es_obligatorio' => TRUE,
				// ],

				'activo' => [
					'label'          => 'Activo',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica se el usuario est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
				],
				'username' => [
					'label'          => 'Username',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'Username para el ingreso al sistema. M&aacute;ximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'password' => [
					'label'          => 'Password',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 40,
					'texto_ayuda'    => 'Password para el ingreso al sistema. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				],
				'email' => [
					'label'          => 'Correo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 40,
					'texto_ayuda'    => 'Correo del usuario. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				],
				'fecha_login' => [
					'label'          => 'Fecha &uacute;ltimo login',
					'tipo'           => Orm_field::TIPO_DATETIME,
					'largo'          => 40,
					'texto_ayuda'    => 'Fecha de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				],
				'ip_login' => [
					'label'          => 'Direcci&oacute;n IP',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'Direcci&oacute;n IP de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				],
				'agente_login' => [
					'label'          => 'Agente',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 200,
					'texto_ayuda'    => 'Agente web de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				],
				'rol' => [
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => [
						'model'         => rol::class,
						'join_table'    => config('bd_usuario_rol'),
						'id_one_table'  => ['id_usuario'],
						'id_many_table' => ['id_rol'],
					],
					'texto_ayuda'    => 'Roles asociados al usuario.',
				],
			],
		];

		parent::__construct($id_usuario);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del usuario
	 *
	 * @return string Usuario
	 */
	public function __toString()
	{
		return (string) $this->nombre;
	}

}

// End of file usuario.php
// Location: ./models/Acl/usuario.php
