<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_ubicacion extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_tipo_ubicacion'),
				'model_label'        => 'Tipo de ubicaci&oacute;n',
				'model_label_plural' => 'Tipos de ubicaci&oacute;n',
				'model_order_by'     => 'tipo_inventario, tipo_ubicacion',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'tipo_inventario' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'tipo_inventario'
					),
					'texto_ayuda'    => 'Seleccione el tipo de inventario.',
					'es_obligatorio' => TRUE,
				),
				'tipo_ubicacion' => array(
					'label'          => 'Tipo de ubicaci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 30,
					'texto_ayuda'    => 'M&aacute;ximo 30 caracteres.',
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
		return (string) $this->tipo_ubicacion;
	}


}
/* End of file tipo_ubicacion.php */
/* Location: ./application/models/tipo_ubicacion.php */
