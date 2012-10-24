<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditor extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_auditores',
						'model_label'        => 'Auditor',
						'model_label_plural' => 'Auditores',
						'model_order_by'     => 'nombre',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'nombre' => array(
								'label'          => 'Nombre del auditor',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'activo' => array(
								'label'          => 'Activo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el auditor esta activo dentro del sistema.',
								'es_obligatorio' => true,
								'default'        => 1
							),
				),
			);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->nombre;
	}


}

/* End of file auditor.php */
/* Location: ./application/models/auditor.php */