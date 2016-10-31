<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Modelo Tipo Material de trabajos TOA
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Tip_material_trabajo_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tecnico Identificador del tecnico
	 * @return void
	 */
	public function __construct($id_tip_material = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_tip_material_trabajo_toa'),
				'model_label'        => 'Tipo Material de trabajo',
				'model_label_plural' => 'Tipos Material de trabajo',
				'model_order_by'     => 'desc_tip_material',
			),
			'campos' => array(
				'id' => array(
					'tipo'           => Orm_field::TIPO_ID,
				),
				'desc_tip_material' => array(
					'label'          => 'Descripci&oacute;n tipo de material',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del tipo de material. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'color' => array(
					'label'          => 'Color tipo material',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'Color o clase que identifica el tipo de material. M&aacute;ximo 50 caracteres.',
				),
				'tip_material' => array(
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => array(
						'model'         => 'catalogo',
						'join_table'    => $this->config->item('bd_catalogo_tip_material_toa'),
						'id_one_table' => array('id_tip_material_trabajo'),
						'id_many_table'  => array('id_catalogo'),
						//'conditions'    => array('id_app' => '@field_value:id_app'),
					),
					'texto_ayuda'    => 'Tipo de material TOA.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tip_material)
		{
			$this->fill($id_tip_material);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Técnico
	 */
	public function __toString()
	{
		return (string) $this->desc_tip_material;
	}

}
/* End of file Tip_material_trabajo_toa.php */
/* Location: ./application/models/Tip_material_trabajo_toa.php */