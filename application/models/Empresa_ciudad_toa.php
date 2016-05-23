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
 * Clase Modelo Empresa Ciudad TOA
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
class Empresa_ciudad_toa extends ORM_Model {

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
				'model_tabla'        => $this->config->item('bd_empresas_ciudades_toa'),
				'model_label'        => 'Empresa ciudad TOA',
				'model_label_plural' => 'Empresas ciudades TOA',
				'model_order_by'     => 'id_empresa, id_ciudad',
			),
			'campos' => array(
				'id_empresa' => array(
					'tipo'           =>  'has_one',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => array(
						'model' => 'empresa_toa'
					),
					'texto_ayuda'    => 'Seleccione una empresa TOA.',
				),
				'id_ciudad' => array(
					'tipo'           =>  'has_one',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'relation'       => array(
						'model' => 'ciudad_toa'
					),
					'texto_ayuda'    => 'Seleccione una Ciudad TOA.',
				),
				'almacenes' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'almacen_sap',
						'join_table'    => $this->config->item('bd_empresas_ciudades_almacenes_toa'),
						'id_one_table'  => array('id_empresa', 'id_ciudad'),
						'id_many_table' => array('centro', 'cod_almacen'),
						//'conditions'    => array('tipo_op' => 'tipo_op')
					),
					'texto_ayuda'    => 'Almacenes asociados a la empresa - ciudad.',
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
		return (string) $this->id_empresa;
	}

}
/* End of file empresa_toa.php */
/* Location: ./application/models/empresa_toa.php */