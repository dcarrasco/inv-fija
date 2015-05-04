<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proveedor extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_proveedores'),
				'model_label'        => 'Proveedor',
				'model_label_plural' => 'Proveedores',
				'model_order_by'     => 'des_proveedor',
			),
			'campos' => array(
				'cod_proveedor' => array(
					'label'          => 'C&oacute;digo del proveedor',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id' => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'des_proveedor' => array(
					'label'          => 'Nombre del proveedor',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
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
		return (string) $this->des_proveedor;
	}


}
/* End of file proveedor.php */
/* Location: ./application/models/proveedor.php */