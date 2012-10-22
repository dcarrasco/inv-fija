<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
		'modelo' => array(
				'model_tabla'        => 'acl_app',
				'model_label'        => 'Aplicacion',
				'model_label_plural' => 'Aplicaciones',
				'model_order_by'     => 'app',
			),
		'campos' => array(
				'id' => array(
						'tipo'   => 'id',
					),
				'app' => array(
						'label'          => 'Aplicacion',
						'tipo'           => 'char',
						'largo'          => 50,
						'texto_ayuda'    => 'Maximo 50 caracteres.',
						'es_obligatorio' => true,
						'es_unico'       => true
					),
				'descripcion' => array(
						'label'          => 'Descripcion de la Aplicacion',
						'tipo'           =>  'char',
						'largo'          => 50,
						'texto_ayuda'    => 'Maximo 50 caracteres.',
						'es_obligatorio' => true,
						'es_unico'       => true
					),
				'url' => array(
						'label'          => 'Direccion de la Aplicacion',
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
		return $this->app;
	}

}

/* End of file app_model.php */
/* Location: ./application/models/app_model.php */