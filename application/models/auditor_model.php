<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditor_model extends MY_Model {

	protected $tabla_bd     = 'fija_auditores';
	protected $label        = 'Auditor';
	protected $label_plural = 'Auditores';
	protected $orden        = 'nombre';
	protected $field_info   = array(
									'id' => array(
											'tipo'   => 'id',
										),
									'nombre' => array(
											'label'          => 'Nombre del auditor',
											'tipo'           => 'CHAR',
											'largo'          => 50,
											'texto_ayuda'    => 'Maximo 50 caracteres.',
											'es_obligatorio' => TRUE,
											'es_unico'       => TRUE
										),
									'activo' => array(
											'label'          => 'Activo',
											'tipo'           => 'BOOLEAN',
											'texto_ayuda'    => 'Indica se el auditor esta activo dentro del sistema.',
											'es_obligatorio' => TRUE,
											'default'        => 1
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

/* End of file auditor_model.php */
/* Location: ./application/models/auditor_model.php */