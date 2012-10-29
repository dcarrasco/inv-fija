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
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'descripcion' => array(
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Descripción del módulo. Maximo 100 caracteres.',
							),
						'llave_modulo' => array(
								'tipo'           =>  'char',
								'largo'          => 20,
								'texto_ayuda'    => 'Llave del módulo. Maximo 20 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
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