<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Centro_model extends MY_Model {

	protected $tabla_bd     = 'fija_centros';
	protected $label        = 'Centro';
	protected $label_plural = 'Centros';
	protected $orden     = 'centro';

	protected $field_info = array(
		'centro' => array(
				'label'          => 'Centro',
				'tipo'           => 'CHAR',
				'largo'          => 10,
				'texto_ayuda'    => 'Nombre del centro. Maximo 10 caracteres.',
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
		return (string)$this->valores['centro'];
	}

}

/* End of file centro_model.php */
/* Location: ./application/models/centro_model.php */