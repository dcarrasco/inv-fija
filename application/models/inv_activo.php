<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inv_activo extends ORM_Model {

	public function __construct($recursion_lvl = 0)
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
								'es_id'          => true,
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
								'label'          => 'Tipo de inventario',
								'tipo'           =>  'char',
								'largo'          => 30,
								'texto_ayuda'    => 'Seleccione el tipo de inventario.',
								'choices'        => array('MAIMONIDES' => 'Inventario Maimonides', 'FIJA' => 'Inventario Fija'),
								'es_obligatorio' => true,
								'es_unico'       => true,
							),
						),
					);
		parent::__construct($cfg, $recursion_lvl);
	}

	public function __toString()
	{
		return $this->descripcion;
	}


}

/* End of file catalogo_model.php */
/* Location: ./application/models/catalogo_model.php */
