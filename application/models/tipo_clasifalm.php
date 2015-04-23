<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_clasifalm extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_tipo_clasifalm_sap'),
				'model_label'        => 'Tipo Clasificaci&oacute;n Almac&eacute;n',
				'model_label_plural' => 'Tipos Clasificaci&oacute;n de Almacenes',
				'model_order_by'     => 'tipo',
			),
			'campos' => array(
				'id_tipoclasif' => array(
						'label'            => 'id',
						'tipo'             => 'int',
						'largo'            => 10,
						'texto_ayuda'      => '',
						'es_id'            => TRUE,
						'es_obligatorio'   => TRUE,
						'es_unico'         => TRUE,
						'es_autoincrement' => TRUE,
				),
				'tipo' => array(
					'label'          => 'Tipo Clasificaci&oacute;n de Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Tipo Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'color' => array(
					'label'          => 'Color del tipo',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Color del tipo para graficar. M&aacute;ximo 20 caracteres.',
					'es_obligatorio' => FALSE,
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->tipo;
	}



}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */