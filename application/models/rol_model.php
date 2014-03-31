<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol_model extends MY_Model {

	protected $tabla_bd     = 'acl_rol';
	protected $label        = 'Rol';
	protected $label_plural = 'Roles';
	protected $orden        = 'rol';

	protected $field_info = array(
		'id' => array(
			'tipo'   => 'ID',
		),
		'id_app' => array(
			'tipo'           => 'HAS_ONE',
			'relation'       => array(
				'model' => 'app_model',
			),
			'texto_ayuda'    => 'Aplicación a la que pertenece el rol.',
		),
		'rol' => array(
			'label'          => 'Rol',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre del rol. Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'descripcion' => array(
			'label'          => 'Descripcion del rol',
			'tipo'           =>  'CHAR',
			'largo'          => 100,
			'texto_ayuda'    => 'Descripción del rol. Maximo 100 caracteres.',
		),
		'modulo' => array(
			'tipo'           => 'HAS_MANY',
			'relation'       => array(
				'model'         => 'modulo_model',
				'join_table'    => 'acl_rol_modulo',
				'id_one_table'  => array('id_rol'),
				'id_many_table' => array('id_modulo'),
				'conditions'    => array('id_app' => '@field_value:id_app'),
			),
			'texto_ayuda'    => 'Módulos del rol.',
		),
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function __toString()
	{
		return (string)$this->valores['rol'];
	}

}

/* End of file rol_model.php */
/* Location: ./application/models/rol_model.php */