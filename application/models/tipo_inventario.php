<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_inventario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_tipos_inventario',
						'model_label'        => 'Tipo de inventario',
						'model_label_plural' => 'Tipos de inventario',
						'model_order_by'     => 'desc_tipo_inventario',
					),
				'campos' => array(
						'id_tipo_inventario' => array(
								'label'          => 'Tipo de inventario',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Maximo 10 caracteres.',
								'es_id'          => true,
								'es_obligatorio' => true,
								'es_unico'       => true,
							),
						'desc_tipo_inventario' => array(
								'label'          => 'Descripcion tipo de inventario',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Descripción tipo de inventario. Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true,
							),
				),
			);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->desc_tipo_inventario;
	}


}

/* End of file tipo_inventario.php */
/* Location: ./application/models/tipo_inventario.php */