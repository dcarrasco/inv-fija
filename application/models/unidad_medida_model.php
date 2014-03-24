<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unidad_medida_model extends MY_Model {

	protected $tabla_bd     = 'fija_unidades';
	protected $label        = 'Unidad de medida';
	protected $label_plural = 'Unidades de medida';
	protected $orden        = 'unidad';

	protected $field_info = array(
		'unidad' => array(
			'label'          => 'Unidad',
			'tipo'           => 'CHAR',
			'largo'          => 10,
			'texto_ayuda'    => 'Unidad de medida. Maximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'desc_unidad' => array(
			'label'          => 'Descripcion unidad de medida',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Descripción de la unidad de medida. Maximo 50 caracteres.',
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
		return (string)$this->valores['desc_unidad'];
	}

}

/* End of file unidad_medida_model.php */
/* Location: ./application/models/unidad_medida_model.php */