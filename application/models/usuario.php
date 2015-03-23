<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_usuarios'),
				'model_label'        => 'Usuario',
				'model_label_plural' => 'Usuarios',
				'model_order_by'     => 'nombre',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'nombre' => array(
					'label'          => 'Nombre de usuario',
					'tipo'           => 'char',
					'largo'          => 45,
					'texto_ayuda'    => 'Nombre del usuario. M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
/*
				'tipo' => array(
					'label'          => 'Descripcion de la Aplicacion',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
*/
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           => 'boolean',
					'texto_ayuda'    => 'Indica se el usuario est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
				),
				'usr' => array(
					'label'          => 'Username',
					'tipo'           => 'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Username para el ingreso al sistema. M&aacute;ximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'pwd' => array(
					'label'          => 'Password',
					'tipo'           => 'char',
					'largo'          => 40,
					'texto_ayuda'    => 'Password para el ingreso al sistema. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'correo' => array(
					'label'          => 'Correo',
					'tipo'           => 'char',
					'largo'          => 40,
					'texto_ayuda'    => 'Correo del usuario. M&aacute;ximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'fecha_login' => array(
					'label'          => 'Fecha &uacute;ltimo login',
					'tipo'           => 'datetime',
					'largo'          => 40,
					'texto_ayuda'    => 'Fecha de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'ip_login' => array(
					'label'          => 'Direcci&oacute;n IP',
					'tipo'           => 'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Direcci&oacute;n IP de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'agente_login' => array(
					'label'          => 'Agente',
					'tipo'           => 'char',
					'largo'          => 200,
					'texto_ayuda'    => 'Agente web de la &uacute;ltima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'rol' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'rol',
						'join_table'    => $this->CI->config->item('bd_usuario_rol'),
						'id_one_table'  => array('id_usuario'),
						'id_many_table' => array('id_rol'),
					),
					'texto_ayuda'    => 'Roles asociados al usuario.',
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->nombre;
	}


}
/* End of file usuario.php */
/* Location: ./application/models/usuario.php */