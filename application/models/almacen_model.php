<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacen_model extends MY_Model {

	protected $tabla_bd     = 'fija_almacenes';
	protected $label        = 'Almacen';
	protected $label_plural = 'Almacenes';
	protected $orden        = 'almacen';

	protected $field_info = array(
		'almacen' => array(
			'label'          => 'Almacen',
			'tipo'           => 'CHAR',
			'largo'          => 10,
			'texto_ayuda'    => 'Nombre del almacen. Maximo 10 caracteres.',
			'es_id'          => TRUE,
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
		return (string)$this->valores['almacen'];
	}

}

/* End of file almacen_model.php */
/* Location: ./application/models/almacen_model.php */