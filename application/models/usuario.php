<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_usuarios',
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
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'tipo' => array(
								'label'          => 'Descripcion de la Aplicacion',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
							),
						'activo' => array(
								'label'          => 'Activo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el usuario esta activo dentro del sistema.',
								'es_obligatorio' => true,
							),
						'usr' => array(
								'label'          => 'Username',
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
							),
						'rol' => array(
								'tipo'           => 'has_many',
								'relation'       => array(
										'model'         => 'rol',
										'join_table'    => 'acl_usuario_rol',
										'id_one_table'  => 'id_usuario',
										'id_many_table' => 'id_rol'
									),
							),
				),
			);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->nombre;
	}

}

/* End of file usuarios_model.php */
/* Location: ./application/models/usuarios_model.php */