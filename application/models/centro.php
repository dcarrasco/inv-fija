<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Centro extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_centros'),
				'model_label'        => 'Centro',
				'model_label_plural' => 'Centros',
				'model_order_by'     => 'centro',
			),
			'campos' => array(
				'centro' => array(
					'label'          => 'Centro',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Nombre del centro. M&aacute;ximo 10 caracteres.',
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
		return (string)$this->centro;
	}

}
/* End of file centro.php */
/* Location: ./application/models/centro.php */