<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'acl_rol',
						'model_label'        => 'Rol',
						'model_label_plural' => 'Roles',
						'model_order_by'     => 'rol',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'id_app' => array(
								'tipo'           => 'has_one',
								'relation'       => array(
										'model' => 'app',
									),
								'texto_ayuda'    => 'Aplicación a la que pertenece el rol.',
							),
						'rol' => array(
								'label'          => 'Rol',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Nombre del rol. Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'descripcion' => array(
								'label'          => 'Descripcion del rol',
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Descripción del rol. Maximo 100 caracteres.',
							),
						'modulo' => array(
								'tipo'           => 'has_many',
								'relation'       => array(
										'model'         => 'modulo',
										'join_table'    => 'acl_rol_modulo',
										'id_one_table'  => array('id_rol'),
										'id_many_table' => array('id_modulo'),
										'conditions'    => array('id_app' => '@field_value:id_app'),
									),
								'texto_ayuda'    => 'Módulos del rol.',
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return (string)$this->rol;
	}

}

/* End of file rol.php */
/* Location: ./application/models/rol.php */