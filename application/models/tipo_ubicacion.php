<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_ubicacion extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_tipo_ubicacion',
						'model_label'        => 'Tipo de ubicacion',
						'model_label_plural' => 'Tipos de ubicacion',
						'model_order_by'     => 'tipo_inventario, tipo_ubicacion',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'tipo_inventario' => array(
								'label'          => 'Tipo de inventario',
								'tipo'           =>  'char',
								'largo'          => 30,
								'texto_ayuda'    => 'Seleccione el tipo de inventario.',
								'choices'        => array('MAIMONIDES' => 'Inventario Maimonides', 'FIJA' => 'Inventario Fija'),
								'es_obligatorio' => true,
								'es_unico'       => true,
							),
						'tipo_ubicacion' => array(
								'label'          => 'Tipo de ubicacion',
								'tipo'           =>  'char',
								'largo'          => 30,
								'texto_ayuda'    => 'Maximo 30 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->descripcion;
	}


}

/* End of file tipo_ubicacion.php */
/* Location: ./application/models/tipo_ubicacion.php */
