<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol extends ORM_Model {

	public function __construct($id = NULL)
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
						'label'          => 'Descripcion de la Aplicacion',
						'tipo'           =>  'char',
						'largo'          => 100,
						'texto_ayuda'    => 'Maximo 100 caracteres.',
						'es_obligatorio' => true,
						'es_unico'       => true
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

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */