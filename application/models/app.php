<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends DataMapper {

	var $table = 'acl_app';

	var $has_many = array(
		'rol',
		);

	var $validation = array(
						'app' => array(
								'label' => 'Aplicacion',
								'rules' => array('required', 'trim', 'unique'),
							),
						'descripcion' => array(
								'label' => 'Descripcion de la aplicacion',
								'rules' => array('required', 'trim', 'unique'),
							),
						'url' => array(
								'label' => 'Direccion de la aplicacion',
								'rules' => array('trim'),
							),
					);


	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	public function __toString()
	{
		return $this->app;
	}

}

/* End of file app_model.php */
/* Location: ./application/models/app_model.php */