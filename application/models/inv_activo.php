<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inv_activo extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_inventario2',
						'model_label'        => 'Inventario',
						'model_label_plural' => 'Inventarios',
						'model_order_by'     => 'nombre',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'nombre' => array(
								'label'          => 'Nombre del inventario',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'activo' => array(
								'label'          => 'Activo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el inventario esta activo dentro del sistema.',
								'es_obligatorio' => true,
							),
						'tipo_inventario' => array(
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'tipo_inventario'
									),
								'texto_ayuda'    => 'Seleccione el tipo de inventario.',
								'es_obligatorio' => true,
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->nombre;
	}

	public function get_id_inventario_activo()
	{
		$this->find('first', array('conditions' => array('activo' => 1)));
		return ($this->{$this->get_model_campo_id()});
	}


}

/* End of file inv_activo.php */
/* Location: ./application/models/inv_activo.php */
