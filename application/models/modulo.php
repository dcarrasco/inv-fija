<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulo extends ORM_Model {

	public function __construct($recursion_lvl = 0)
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

		parent::__construct($cfg, $recursion_lvl);

	}

	public function __toString()
	{
		$fields = $this->get_model_fields();
		$relation = $fields['id_app']->get_relation();

		return $relation['data']->__toString() . ' > ' . $this->modulo;
	}

}

/* End of file modulo_model.php */
/* Location: ./application/models/modulo_model.php */