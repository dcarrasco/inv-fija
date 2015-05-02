<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulo extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_modulos'),
				'model_label'        => 'M&oacute;dulo',
				'model_label_plural' => 'M&oacute;dulos',
				'model_order_by'     => 'id_app, orden, modulo',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'id_app' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'app',
					),
					'texto_ayuda'    => 'Aplicaci&oacute;n a la que pertenece el m&oacute;dulo.',
				),
				'modulo' => array(
					'label'          => 'M&oacute;dulo',
					'tipo'           =>  'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 100,
					'texto_ayuda'    => 'Descripci&oacute;n del m&oacute;dulo. M&aacute;ximo 100 caracteres.',
				),
				'orden' => array(
					'label'          => 'Orden del m&oacute;dulo',
					'tipo'           => 'int',
					'texto_ayuda'    => 'Orden del m&oacute;dulo en el men&uacute;.',
					'es_obligatorio' => TRUE,
				),
				'url' => array(
					'label'          => 'Direccion del m&oacute;dulo',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Dirección web (URL) del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				),
				'icono' => array(
					'label'          => '&Iacute;cono del m&oacute;dulo',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de archivo del icono del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				),
				'llave_modulo' => array(
					'label'          => 'Llave del m&oacute;dulo',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Cadena de caracteres de seguridad del m&oacute;dulo. M&aacute;ximo 20 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
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
		return (string) $this->modulo;
	}

}
/* End of file modulo.php */
/* Location: ./application/models/modulo.php */