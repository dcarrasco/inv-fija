<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_app'),
				'model_label'        => 'Aplicaci&oacute;n',
				'model_label_plural' => 'Aplicaciones',
				'model_order_by'     => 'app',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'app' => array(
					'label'          => 'Aplicaci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n de la Aplicaci&oacute;n',
					'tipo'           =>  'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Breve descripcion de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'orden' => array(
					'label'          => 'Orden de la Aplicaci&oacute;n',
					'tipo'           =>  'int',
					'texto_ayuda'    => 'Orden de la aplicaci&oacute;n en el menu.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'url' => array(
					'label'          => 'Direcci&oacute;n de la Aplicaci&oacute;n',
					'tipo'           =>  'char',
					'largo'          => 100,
					'texto_ayuda'    => 'Direcci&oacute;n web (URL) de la aplicaci&oacute;n. M&aacute;ximo 100 caracteres.',
				),
				'icono' => array(
					'label'          => '&Iacute;cono de la aplicaci&oacute;n',
					'tipo'           =>  'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del archivo del &iacute;cono de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return $this->app;
	}

}
/* End of file app.php */
/* Location: ./application/models/app.php */