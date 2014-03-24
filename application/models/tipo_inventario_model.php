<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_inventario_model extends MY_Model {

	protected $tabla_bd     = 'fija_tipos_inventario';
	protected $label        = 'Tipo de inventario';
	protected $label_plural = 'Tipos de inventario';
	protected $orden        = 'desc_tipo_inventario';

	protected $field_info   = array(
		'id_tipo_inventario' => array(
			'label'          => 'Tipo de inventario',
			'tipo'           => 'CHAR',
			'largo'          => 10,
			'texto_ayuda'    => 'Maximo 10 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		),
		'desc_tipo_inventario' => array(
			'label'          => 'Descripcion tipo de inventario',
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Descripción tipo de inventario. Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		),
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function __toString()
	{
		return $this->valores['desc_tipo_inventario'];
	}


}

/* End of file tipo_inventario_model.php */
/* Location: ./application/models/tipo_inventario_model.php */