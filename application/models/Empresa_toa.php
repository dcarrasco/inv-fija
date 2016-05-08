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
 * Clase Modelo Empresa TOA
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
class Empresa_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_empresa Identificador de la empresa
	 * @return void
	 */
	public function __construct($id_empresa = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_empresas_toa'),
				'model_label'        => 'Empresa TOA',
				'model_label_plural' => 'Empresas TOA',
				'model_order_by'     => 'empresa',
			),
			'campos' => array(
				'id_empresa' => array(
					'label'          => 'ID Empresa',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'ID de la empresa. M&aacute;ximo 20 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'empresa' => array(
					'label'          => 'Nombre de la empresa',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la empresa. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'tipoalm' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'tipoalmacen_sap',
						'join_table'    => $this->config->item('bd_empresas_toa_tiposalm'),
						'id_one_table'  => array('id_empresa'),
						'id_many_table' => array('id_tipo'),
						//'conditions'    => array('id_app' => '@field_value:id_app'),
					),
					'texto_ayuda'    => 'Tipos de almacen asociados a empresa TOA.',
				),
				'ciudades' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'ciudad_toa',
						'join_table'    => $this->config->item('bd_empresas_ciudades_toa'),
						'id_one_table'  => array('id_empresa'),
						'id_many_table' => array('id_ciudad'),
						//'conditions'    => array('id_app' => '@field_value:id_app'),
					),
					'texto_ayuda'    => 'Ciudades asociados a empresa TOA.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_empresa)
		{
			$this->fill($id_empresa);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Empresa
	 */
	public function __toString()
	{
		return (string) $this->empresa;
	}

}
/* End of file empresa_toa.php */
/* Location: ./application/models/empresa_toa.php */