<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipoalmacen_sap extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'bd_logistica..cp_tiposalm',
						//'model_tabla'        => 'cp_tiposalm',
						'model_label'        => 'Tipo Almacen',
						'model_label_plural' => 'Tipos de Almacen',
						'model_order_by'     => 'tipo_op, tipo',
					),
				'campos' => array(
						'id_tipo' => array(
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
								'label'          => 'Tipo de Almacen',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Tipo del almacen. Maximo 50 caracteres.',
								'es_obligatorio' => TRUE,
							),
						'tipo_op' => array(
								'label'          => 'Tipo operación',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Seleccione el tipo de operación.',
								'choices'        => array(
									'MOVIL' => 'Operación Móvil',
									'FIJA'  => 'Operación Fija'),
								'es_obligatorio' => TRUE,
							),
						'almacenes' => array(
								'tipo'           => 'has_many',
								'relation'       => array(
										'model'         => 'almacen_sap',
										'join_table'    => 'bd_logistica..cp_tipos_almacenes',
										//'join_table'    => 'cp_tipos_almacenes',
										'id_one_table'  => array('id_tipo'),
										'id_many_table' => array('centro', 'cod_almacen'),
										//'conditions'    => array('tipo_op' => 'tipo_op')
									),
								'texto_ayuda'    => 'Tipos asociados al almacen.',
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return (string)$this->tipo;
	}

}

/* End of file almacen.php */
/* Location: ./application/models/almacen.php */