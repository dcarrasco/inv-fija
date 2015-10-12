<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clasifalmacen_sap extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_clasifalm_sap'),
				'model_label'        => 'Clasificaci&oacute;n Almac&eacute;n',
				'model_label_plural' => 'Clasificaci&oacute;n de Almacenes',
				'model_order_by'     => 'tipo_op, orden, clasificacion',
			),
			'campos' => array(
				'id_clasif' => array(
						'label'            => 'id',
						'tipo'             => 'int',
						'largo'            => 10,
						'texto_ayuda'      => '',
						'es_id'            => TRUE,
						'es_obligatorio'   => TRUE,
						'es_unico'         => TRUE,
						'es_autoincrement' => TRUE,
				),
				'clasificacion' => array(
					'label'          => 'Clasificaci&oacute;n de Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'orden' => array(
					'label'          => 'Orden de la clasificaci&oacute;n',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Orden de la clasificaci&oacute;n del almac&eacute;n.',
					'es_obligatorio' => TRUE,
				),
				'dir_responsable' => array(
					'label'          => 'Direcci&oacute;n responsable',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Seleccione la direcci&oacute;n responsable',
					'choices'        => array(
						'*'          => 'Por material',
						'TERMINALES' => 'Terminales',
						'REDES'      => 'Redes',
						'EMPRESAS'   => 'Empresas',
						'LOGISTICA'  => 'Log&iacute;stica',
						'TTPP'       => 'Telefon&iacute;a P&uacute;blica',
						'MARKETING'  => 'Marketing',
					),
					'es_obligatorio' => TRUE,
				),
				'estado_ajuste' => array(
					'label'          => 'Estado de ajuste materiales',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Indica confiabilidad de existencia del material.',
					'choices'        => array(
						'EXISTE'     => 'Existe',
						'NO_EXISTE'  => 'No existe',
						'NO_SABEMOS' => 'No sabemos',
					),
					'es_obligatorio' => TRUE,
				),
				'id_tipoclasif' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'tipo_clasifalm'
					),
				),
				'tipo_op' => array(
					'label'          => 'Tipo operaci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Seleccione el tipo de operaci&oacute;n.',
					'choices'        => array(
						'MOVIL' => 'Operaci&oacute;n M&oacute;vil',
						'FIJA'  => 'Operaci&oacute;n Fija'
					),
					'es_obligatorio' => TRUE,
				),
				'tiposalm' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'tipoalmacen_sap',
						'join_table'    => $this->CI->config->item('bd_clasif_tipoalm_sap'),
						'id_one_table'  => array('id_clasif'),
						'id_many_table' => array('id_tipo'),
						//'conditions'    => array('tipo_op' => 'tipo_op')
					),
					'texto_ayuda'    => 'Tipos de almac&eacute;n asociados a la clasificaci&oacute;n.',
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
		return (string) $this->clasificacion;
	}



}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */