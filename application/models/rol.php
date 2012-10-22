<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol extends DataMapper {

	var $table = 'acl_rol';

	var $has_one = array(
			'app' => array(
					'auto_populate' => TRUE,
				)
		);

	var $validation = array(
						'rol' => array(
								'label' => 'Rol',
								'rules' => array('required', 'trim', 'unique', 'max_length' => 30),
							),
						'descripcion' => array(
								'label' => 'Descripcion de la aplicacion',
								'rules' => array('required', 'trim', 'max_length' => 50),
							),
					);


	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	public function __toString()
	{
		return (string)$this->rol;
	}

}

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */