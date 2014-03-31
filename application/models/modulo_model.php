<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulo_model extends MY_Model {

	protected $tabla_bd    = 'acl_modulo';
	protected $orden       = 'id_app, orden, modulo';

	protected $field_info = array(
		'id' => array(
			'tipo'   => 'ID',
		),
		'id_app' => array(
			'tipo'           => 'HAS_ONE',
			'relation'       => array(
				'model' => 'app_model',
			),
			'texto_ayuda'    => 'Aplicación a la que pertenece el módulo.',
		),
		'modulo' => array(
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre del módulo. Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'descripcion' => array(
			'tipo'           =>  'CHAR',
			'largo'          => 100,
			'texto_ayuda'    => 'Descripción del módulo. Maximo 100 caracteres.',
		),
		'orden' => array(
			'label'          => 'Orden del módulo',
			'tipo'           =>  'INT',
			'texto_ayuda'    => 'Orden del modulo en el menu.',
			'es_obligatorio' => TRUE,
		),
		'url' => array(
			'label'          => 'Direccion del módulo',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Dirección web (URL) del módulo. Maximo 50 caracteres.',
		),
		'icono' => array(
			'label'          => 'Icono del módulo',
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre de archivo del icono del modulo. Maximo 50 caracteres.',
		),
		'llave_modulo' => array(
			'tipo'           =>  'CHAR',
			'largo'          => 20,
			'texto_ayuda'    => 'Llave del módulo. Maximo 20 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
	);

	public function __construct()
	{
		parent::__construct();

	}

	public function __toString()
	{
		//$fields = $this->get_model_fields();
		//$relation = $fields['id_app']->get_relation();
		//return $relation['data']->__toString() . ' > ' . $this->modulo;
		return $this->valores['modulo'];
	}

}

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */