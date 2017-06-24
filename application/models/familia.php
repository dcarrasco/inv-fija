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
 * Clase Modelo Familia
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
class Familia extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_familia Identificador de la familia
	 * @return void
	 */
	public function __construct($id_familia = NULL)
	{
		$this->_model_config = [
			'modelo' => [
				'model_tabla'        => $this->config->item('bd_familias'),
				'model_label'        => 'Familia',
				'model_label_plural' => 'Familias',
				'model_order_by'     => 'codigo',
			],
			'campos' => [
				'codigo' => [
					'label'          => 'C&oacute;digo de la familia',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
				'tipo' => [
					'label'       => 'Tipo de familia',
					'tipo'        => Orm_field::TIPO_CHAR,
					'largo'       => 30,
					'texto_ayuda' => 'Seleccione el tipo de familia.',
					'choices'     => [
						'FAM'    => 'Familia',
						'SUBFAM' => 'SubFamilia',
					],
					'es_obligatorio' => TRUE,
				],
				'nombre' => [
					'label'          => 'Nombre de la familia',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				],
			],
		];

		parent::__construct($id_familia);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Familia
	 */
	public function __toString()
	{
		return (string) $this->nombre;
	}

}
/* End of file familia.php */
/* Location: ./application/models/familia.php */
