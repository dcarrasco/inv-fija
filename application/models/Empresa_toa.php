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
		$this->_model_config = [
			'modelo' => [
				'model_tabla'        => config('bd_empresas_toa'),
				'model_label'        => 'Empresa TOA',
				'model_label_plural' => 'Empresas TOA',
				'model_order_by'     => 'empresa',
			],
			'campos' => [
				'id_empresa' => [
					'label'          => 'ID Empresa',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'ID de la empresa. M&aacute;ximo 20 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'empresa' => [
					'label'          => 'Nombre de la empresa',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la empresa. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'tipoalm' => [
					'tipo'     => Orm_field::TIPO_HAS_MANY,
					'relation' => [
						'model'         => 'tipoalmacen_sap',
						'join_table'    => config('bd_empresas_toa_tiposalm'),
						'id_one_table'  => ['id_empresa'],
						'id_many_table' => ['id_tipo'],
						//'conditions'    => ['id_app' => '@field_value:id_app'),
					],
					'texto_ayuda' => 'Tipos de almacen asociados a empresa TOA.',
				],
				'ciudades' => [
					'tipo'     => Orm_field::TIPO_HAS_MANY,
					'relation' => [
						'model'         => 'ciudad_toa',
						'join_table'    => config('bd_empresas_ciudades_toa'),
						'id_one_table'  => ['id_empresa'],
						'id_many_table' => ['id_ciudad'],
						//'conditions'    => ['id_app' => '@field_value:id_app'],
					],
					'texto_ayuda' => 'Ciudades asociados a empresa TOA.',
				],
			],
		];

		parent::__construct($id_empresa);
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
