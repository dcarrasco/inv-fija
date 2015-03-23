<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_inventario extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_tipos_inventario'),
				'model_label'        => 'Tipo de inventario',
				'model_label_plural' => 'Tipos de inventario',
				'model_order_by'     => 'desc_tipo_inventario',
			),
			'campos' => array(
				'id_tipo_inventario' => array(
					'label'          => 'Tipo de inventario',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'desc_tipo_inventario' => array(
					'label'          => 'Descripci&oacute;n tipo de inventario',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del tipo de inventario. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->desc_tipo_inventario;
	}


}

/* End of file tipo_inventario.php */
/* Location: ./application/models/tipo_inventario.php */