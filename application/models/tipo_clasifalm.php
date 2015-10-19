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
 * Clase Modelo Tipo de Clasificacion Almacen
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Tipo_clasifalm extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipoclasif Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipoclasif = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_tipo_clasifalm_sap'),
				'model_label'        => 'Tipo Clasificaci&oacute;n Almac&eacute;n',
				'model_label_plural' => 'Tipos Clasificaci&oacute;n de Almacenes',
				'model_order_by'     => 'tipo',
			),
			'campos' => array(
				'id_tipoclasif' => array(
						'label'            => 'id',
						'tipo'             => 'int',
						'largo'            => 10,
						'texto_ayuda'      => '',
						'es_id'            => TRUE,
						'es_obligatorio'   => TRUE,
						'es_unico'         => TRUE,
						'es_autoincrement' => TRUE,
				),
				'tipo' => array(
					'label'          => 'Tipo Clasificaci&oacute;n de Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Tipo Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'color' => array(
					'label'          => 'Color del tipo',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Color del tipo para graficar. M&aacute;ximo 20 caracteres.',
					'es_obligatorio' => FALSE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tipoclasif)
		{
			$this->fill($id_tipoclasif);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->tipo;
	}



}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */