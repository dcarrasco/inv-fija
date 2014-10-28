<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacen extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_almacenes'),
				'model_label'        => 'Almacen',
				'model_label_plural' => 'Almacenes',
				'model_order_by'     => 'almacen',
			),
			'campos' => array(
				'almacen' => array(
					'label'          => 'Almacen',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Nombre del almacen. Maximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string)$this->almacen;
	}

}

/* End of file almacen.php */
/* Location: ./application/models/almacen.php */