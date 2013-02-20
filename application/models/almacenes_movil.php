<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacenes_movil extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'movil_almacenes_stock',
						'model_label'        => 'Almacenes Movil',
						'model_label_plural' => 'Almacenes Movil',
						'model_order_by'     => 'centro, almacen',
					),
				'campos' => array(
						'centro' => array(
								'label'          => 'Centro',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Centro SAP',
								'es_id'          => true,
								'es_obligatorio' => true,
								//'es_unico'       => true,
							),
						'almacen' => array(
								'label'          => 'Almacen',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Centro SAP',
								'es_id'          => true,
								'es_obligatorio' => true,
								//'es_unico'       => true,
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return (string)$this->centro . '-' . $this->almacen;
	}

}

/* End of file almacenes_movil.php */
/* Location: ./application/models/almacenes_movil.php */