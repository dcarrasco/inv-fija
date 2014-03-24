MY_Model<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulo extends MY_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'    => 'acl_modulo',
						'model_order_by' => 'id_app, orden, modulo',
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
								'texto_ayuda'    => 'Aplicación a la que pertenece el módulo.',
							),
						'modulo' => array(
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Nombre del módulo. Maximo 50 caracteres.',
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE
							),
						'descripcion' => array(
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Descripción del módulo. Maximo 100 caracteres.',
							),
						'orden' => array(
								'label'          => 'Orden del módulo',
								'tipo'           =>  'int',
								'texto_ayuda'    => 'Orden del modulo en el menu.',
								'es_obligatorio' => TRUE,
							),
						'url' => array(
								'label'          => 'Direccion del módulo',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Dirección web (URL) del módulo. Maximo 50 caracteres.',
							),
						'icono' => array(
								'label'          => 'Icono del módulo',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Nombre de archivo del icono del modulo. Maximo 50 caracteres.',
							),
						'llave_modulo' => array(
								'tipo'           =>  'char',
								'largo'          => 20,
								'texto_ayuda'    => 'Llave del módulo. Maximo 20 caracteres.',
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE
							),
						),
			);

		parent::__construct($cfg);

	}

	public function __toString()
	{
		//$fields = $this->get_model_fields();
		//$relation = $fields['id_app']->get_relation();
		//return $relation['data']->__toString() . ' > ' . $this->modulo;
		return $this->modulo;
	}

}

/* End of file modulo.php */
/* Location: ./application/models/modulo.php */