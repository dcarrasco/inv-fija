<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_sap extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_usuarios_sap'),
				'model_label'        => 'Usuario SAP',
				'model_label_plural' => 'Usuarios SAP',
				'model_order_by'     => 'usuario',
			),
			'campos' => array(
				'usuario' => array(
					'label'          => 'Codigo Usuario',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'C&oacute;digo del usuario SAP. M&aacute;ximo 10 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'nom_usuario' => array(
					'label'          => 'Nombre de usuario',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del usuario. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->nom_usuario;
	}


}
/* End of file usuario_sap.php */
/* Location: ./application/models/usuario_sap.php */