<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Familia_model extends MY_Model {

	protected $tabla_bd     = 'fija_familias';
	protected $label        = 'Familia';
	protected $label_plural = 'Familias';
	protected $orden        = 'codigo';

	protected $field_info = array(
		'codigo' => array(
			'label'          => 'Codigo de la familia',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Maximo 50 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE,
		),
		'tipo' => array(
			'label'          => 'Tipo de familia',
			'tipo'           =>  'CHAR',
			'largo'          => 30,
			'texto_ayuda'    => 'Seleccione el tipo de familia.',
			'choices'        => array(
				'FAM' => 'Familia',
				'SUBFAM' => 'SubFamilia'),
			'es_obligatorio' => TRUE,
		),
		'nombre' => array(
			'label'          => 'Nombre de la familia',
			'tipo'           => 'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Maximo 50 caracteres.',
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
		return $this->valores['nombre'];
	}


}

/* End of file familia_model.php */
/* Location: ./application/models/familia_model.php */