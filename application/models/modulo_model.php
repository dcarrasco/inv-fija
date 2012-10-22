<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulo extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'    => 'acl_modulo',
						'model_order_by' => 'modulo',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'id_app' => array(
								'tipo'           =>  'has_one',
								'relation'       => array('rel_table' => 'acl_app', 'local_id' => 'id_app', 'external_id' => 'id', 'rel_modelo' => 'app'),
								'label'          => 'aplicacion',
							),
						'modulo' => array(
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'descripcion' => array(
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'llave_modulo' => array(
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

	public function to_string()
	{
		return $this->modulo;
	}

}

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */