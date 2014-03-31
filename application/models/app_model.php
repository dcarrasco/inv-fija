<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_model extends MY_Model {

	protected $tabla_bd     = 'acl_app';
	protected $label        = 'Aplicacion';
	protected $label_plural = 'Aplicaciones';
	protected $orden        = 'app';

	protected $field_info = array(
		'id' => array(
			'tipo'   => 'ID',
		),
		'app' => array(
			'label'          => 'Aplicacion',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre de la aplicación. Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'descripcion' => array(
			'label'          => 'Descripcion de la Aplicacion',
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Breve descripcion de la aplicacion. Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		),
		'orden' => array(
			'label'          => 'Orden de la Aplicacion',
			'tipo'           =>  'INT',
			'texto_ayuda'    => 'Orden de la aplicacion en el menu.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'url' => array(
			'label'          => 'Direccion de la Aplicacion',
			'tipo'           =>  'CHAR',
			'largo'          => 100,
			'texto_ayuda'    => 'Dirección web (URL) de la aplicacion. Maximo 100 caracteres.',
		),
		'icono' => array(
			'label'          => 'Icono de la apliacion',
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre del archivo del icono de la aplicacion. Maximo 50 caracteres.',
		),
	);



	public function __construct()
	{
		parent::__construct();
	}

	public function __toString()
	{
		return $this->valores['app'];
	}

}

/* End of file app_model.php */
/* Location: ./application/models/app_model.php */