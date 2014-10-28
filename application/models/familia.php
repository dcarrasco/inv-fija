<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Familia extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_familias'),
				'model_label'        => 'Familia',
				'model_label_plural' => 'Familias',
				'model_order_by'     => 'codigo',
			),
			'campos' => array(
				'codigo' => array(
					'label'          => 'Codigo de la familia',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Maximo 50 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'tipo' => array(
					'label'          => 'Tipo de familia',
					'tipo'           =>  'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Seleccione el tipo de familia.',
					'choices'        => array(
						'FAM' => 'Familia',
						'SUBFAM' => 'SubFamilia'
					),
					'es_obligatorio' => TRUE,
				),
				'nombre' => array(
					'label'          => 'Nombre de la familia',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Maximo 50 caracteres.',
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
		return $this->nombre;
	}


}
/* End of file familia.php */
/* Location: ./application/models/familia.php */