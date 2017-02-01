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

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_usuario Identificador del modulo
	 * @return void
	 */
	public function __construct($id_usuario = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_usuarios'),
				'model_label'        => 'Usuario',
				'model_label_plural' => 'Usuarios',
				'model_order_by'     => 'nombre',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => Orm_field::TIPO_ID,
				),
				'nombre' => array(
					'label'          => 'Nombre de usuario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 45,
					'texto_ayuda'    => 'Nombre del usuario. M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
/*
				'tipo' => array(
					'label'          => 'Descripcion de la Aplicacion',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
*/
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica se el usuario est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
				),
				'username' => array(
					'label'          => 'Username',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'Username para el ingreso al sistema. M&aacute;ximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'password' => array(
					'label'          => 'Password',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 40,
					'texto_ayuda'    => 'Password para el ingreso al sistema. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'email' => array(
					'label'          => 'Correo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 40,
					'texto_ayuda'    => 'Correo del usuario. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'fecha_login' => array(
					'label'          => 'Fecha &uacute;ltimo login',
					'tipo'           => Orm_field::TIPO_DATETIME,
					'largo'          => 40,
					'texto_ayuda'    => 'Fecha de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'ip_login' => array(
					'label'          => 'Direcci&oacute;n IP',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'Direcci&oacute;n IP de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'agente_login' => array(
					'label'          => 'Agente',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 200,
					'texto_ayuda'    => 'Agente web de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'rol' => array(
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => array(
						'model'         => 'rol',
						'join_table'    => $this->config->item('bd_usuario_rol'),
						'id_one_table'  => array('id_usuario'),
						'id_many_table' => array('id_rol'),
					),
					'texto_ayuda'    => 'Roles asociados al usuario.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_usuario)
		{
			$this->fill($id_usuario);
		}
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
/* End of file usuario.php */
/* Location: ./application/models/usuario.php */