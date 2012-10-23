<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol extends ORM_Model {

	public function __construct($recursion_lvl = 0)
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
							),
						'rol' => array(
								'label'          => 'Rol',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'descripcion' => array(
								'label'          => 'Descripcion del rol',
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'modulo' => array(
								'tipo'           => 'has_many',
								'relation'       => array(
										'model'         => 'modulo',
										'join_table'    => 'acl_rol_modulo',
										'id_one_table'  => 'id_rol',
										'id_many_table' => 'id_modulo'
									),
							),
						),
					);
		parent::__construct($cfg, $recursion_lvl);
	}

	public function __toString()
	{
		return (string)$this->rol;
	}

}

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */