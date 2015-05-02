<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditor extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_auditores'),
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
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           =>  'boolean',
					'texto_ayuda'    => 'Indica se el auditor est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
					'default'        => 1
				),
			),
		);

		$this->config_model($cfg);

		if ($id)
		{
			$this->fill($id);
		}
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->nombre;
	}


}
/* End of file auditor.php */
/* Location: ./application/models/auditor.php */