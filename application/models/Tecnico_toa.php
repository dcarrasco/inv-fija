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
 * Clase Modelo Técnico TOA
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
class Tecnico_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tecnico Identificador del tecnico
	 * @return void
	 */
	public function __construct($id_tecnico = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_tecnicos_toa'),
				'model_label'        => 'T&eacute;cnico TOA',
				'model_label_plural' => 'T&eacute;cnicos TOA',
				'model_order_by'     => 'tecnico',
			),
			'campos' => array(
				'id_tecnico' => array(
					'label'          => 'ID T&eacute;cnico',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'ID del t&eacute;cnico. M&aacute;ximo 20 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'tecnico' => array(
					'label'          => 'Nombre t&eacute;cnico',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del t&eacute;cnico. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					// 'es_unico'       => TRUE
				),
				'rut' => array(
					'label'          => 'RUT del t&eacute;cnico',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'RUT del t&eacute;cnico. Sin puntos, con guion y d&iacute;gito verificador (en min&uacute;scula). M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					// 'es_unico'       => TRUE
				),
				'id_empresa' => array(
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model' => 'empresa_toa',
					),
					'texto_ayuda'    => 'Empresa a la que pertenece el t&eacute;cnico.',
					'onchange'       => form_onchange('id_empresa', 'id_ciudad', 'toa_config/get_select_ciudad'),
				),
				'id_ciudad' => array(
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model' => 'Ciudad_toa',
					),
					'texto_ayuda'    => 'Ciudad a la que pertenece el t&eacute;cnico.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tecnico)
		{
			$this->fill($id_tecnico);
		}

		$this->get_ciudades_tecnico();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Técnico
	 */
	public function __toString()
	{
		return (string) $this->tecnico;
	}

	// --------------------------------------------------------------------
	public function get_ciudades_tecnico()
	{
		if ($this->id_empresa)
		{
			$empresa_ciudad = new Empresa_ciudad_toa;
			$arr_ciudades = $empresa_ciudad->arr_ciudades_por_empresa($this->id_empresa);

			$arr_config_ciudad = array(
				'id_ciudad' => array(
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model'      => 'Ciudad_toa',
						'conditions' => array('id_ciudad' => $arr_ciudades),
					),
					'texto_ayuda'    => 'Ciudad a la que pertenece el t&eacute;cnico.',
				),
			);

			$this->_config_campos($arr_config_ciudad);
			$this->get_relation_fields();
		}
	}


}
/* End of file tecnico_toa.php */
/* Location: ./application/models/tecnico_toa.php */