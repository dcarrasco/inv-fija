<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_ubicacion_model extends MY_Model {

	protected $tabla_bd     = 'fija_tipo_ubicacion';
	protected $label        = 'Tipo de ubicacion';
	protected $label_plural = 'Tipos de ubicacion';
	protected $order_by     = 'tipo_inventario, tipo_ubicacion';

	protected $field_info = array(
		'id' => array(
			'tipo'   => 'ID',
		),
		'tipo_inventario' => array(
			'tipo'           =>  'HAS_ONE',
			'relation'       => array(
				'model' => 'tipo_inventario_model'
			),
			'texto_ayuda'    => 'Seleccione el tipo de inventario.',
			'es_obligatorio' => TRUE,
		),
		'tipo_ubicacion' => array(
			'label'          => 'Tipo de ubicacion',
			'tipo'           =>  'CHAR',
			'largo'          => 30,
			'texto_ayuda'    => 'Maximo 30 caracteres.',
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
		return $this->valores['tipo_ubicacion'];
	}


}

/* End of file tipo_ubicacion_model.php */
/* Location: ./application/models/tipo_ubicacion_model.php */
