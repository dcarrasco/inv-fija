<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unidad_medida extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_unidades',
						'model_label'        => 'Unidad de medida',
						'model_label_plural' => 'Unidades de medida',
						'model_order_by'     => 'unidad',
					),
				'campos' => array(
						'unidad' => array(
								'label'          => 'Unidad',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Unidad de medida. Maximo 10 caracteres.',
								'es_id'          => true,
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'desc_unidad' => array(
								'label'          => 'Descripcion unidad de medida',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Descripción de la unidad de medida. Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return (string)$this->desc_unidad;
	}

}

/* End of file unidad_medida.php */
/* Location: ./application/models/unidad_medida.php */