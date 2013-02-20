<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movil_inventario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'movil_inventario',
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
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
								'es_unico'       => true
							),
						'activo' => array(
								'label'          => 'Activo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el inventario esta activo dentro del sistema.',
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

	public function grabar()
	{
		parent::grabar();
		if ($this->activo)
		{
			$this->db->update($this->get_model_tabla(), array('activo' => 0), 'id<>' . $this->id);
		}
	}

	public function get_id_inventario_activo()
	{
		$this->find('first', array('conditions' => array('activo' => 1)));
		return ($this->{$this->get_model_campo_id()});
	}



}

/* End of file inv_activo.php */
/* Location: ./application/models/inv_activo.php */
