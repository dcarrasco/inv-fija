<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends ORM_Model {

	var $table = 'fija_usuarios';

	var $validation = array(
						'nombre' => array(
								'label' => 'Nombre de usuario',
								'rules' => array('required', 'trim', 'unique'),
							),
						'tipo' => array(
								'label' => 'Tipo de usuario',
								'rules' => array('required', 'trim', 'unique'),
							),
						'activo' => array(
								'label' => 'Activo?',
								'rules' => array('required', 'trim'),
							),
						'usr' => array(
								'label' => 'Username',
								'rules' => array('required', 'trim', 'unique'),
							),
					);


	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}


}

/* End of file usuarios_model.php */
/* Location: ./application/models/usuarios_model.php */