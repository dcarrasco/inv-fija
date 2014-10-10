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
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'tipo_inventario'
									),
								'texto_ayuda'    => 'Seleccione el tipo de inventario.',
								'es_obligatorio' => TRUE,
							),
						'tipo_ubicacion' => array(
								'label'          => 'Tipo de ubicacion',
								'tipo'           =>  'char',
								'largo'          => 30,
								'texto_ayuda'    => 'Maximo 30 caracteres.',
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE
							),
						),
					);
		parent::__construct($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return $this->tipo_ubicacion;
	}


}
/* End of file tipo_ubicacion.php */
/* Location: ./application/models/tipo_ubicacion.php */
