<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proveedor extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'bd_logistica..cp_proveedores',
						'model_label'        => 'Proveedor',
						'model_label_plural' => 'Proveedores',
						'model_order_by'     => 'des_proveedor',
					),
				'campos' => array(
						'cod_proveedor' => array(
								'label'          => 'Codigo del proveedor',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Maximo 10 caracteres.',
								'es_id' => TRUE,
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE,
							),
						'des_proveedor' => array(
								'label'          => 'Nombre del proveedor',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => TRUE,
								'es_unico'       => FALSE,
							),
				),
			);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->des_proveedor;
	}


}

/* End of file proveedor.php */
/* Location: ./application/models/proveedor.php */