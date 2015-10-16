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
 * Clase Modelo Auditor
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
class Auditor extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  integer $id_auditor Identificador del auditor
	 * @return void
	 */
	public function __construct($id_auditor = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_auditores'),
				'model_label'        => 'Auditor',
				'model_label_plural' => 'Auditores',
				'model_order_by'     => 'nombre',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'nombre' => array(
					'label'          => 'Nombre del auditor',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           =>  'boolean',
					'texto_ayuda'    => 'Indica se el auditor est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
					'default'        => 1
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_auditor)
		{
			$this->fill($id_auditor);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Auditor
	 */
	public function __toString()
	{
		return (string) $this->nombre;
	}


}
/* End of file auditor.php */
/* Location: ./application/models/auditor.php */