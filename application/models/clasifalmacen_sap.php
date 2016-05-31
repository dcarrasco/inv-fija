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
 * Clase Modelo Clasificacion de Almacenes
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
class Clasifalmacen_sap extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_clasif_almacen Identificador de la clasificacion
	 * @return void
	 */
	public function __construct($id_clasif_almacen = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_clasifalm_sap'),
				'model_label'        => 'Clasificaci&oacute;n Almac&eacute;n',
				'model_label_plural' => 'Clasificaci&oacute;n de Almacenes',
				'model_order_by'     => 'tipo_op, orden, clasificacion',
			),
			'campos' => array(
				'id_clasif' => array(
						'label'            => 'id',
						'tipo'             => 'int',
						'largo'            => 10,
						'texto_ayuda'      => '',
						'es_id'            => TRUE,
						'es_obligatorio'   => TRUE,
						'es_unico'         => TRUE,
						'es_autoincrement' => TRUE,
				),
				'clasificacion' => array(
					'label'          => 'Clasificaci&oacute;n de Almac&eacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'orden' => array(
					'label'          => 'Orden de la clasificaci&oacute;n',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Orden de la clasificaci&oacute;n del almac&eacute;n.',
					'es_obligatorio' => TRUE,
				),
				'dir_responsable' => array(
					'label'          => 'Direcci&oacute;n responsable',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Seleccione la direcci&oacute;n responsable',
					'choices'        => array(
						'*'          => 'Por material',
						'TERMINALES' => 'Terminales',
						'REDES'      => 'Redes',
						'EMPRESAS'   => 'Empresas',
						'LOGISTICA'  => 'Log&iacute;stica',
						'TTPP'       => 'Telefon&iacute;a P&uacute;blica',
						'MARKETING'  => 'Marketing',
					),
					'es_obligatorio' => TRUE,
				),
				'estado_ajuste' => array(
					'label'          => 'Estado de ajuste materiales',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Indica confiabilidad de existencia del material.',
					'choices'        => array(
						'EXISTE'     => 'Existe',
						'NO_EXISTE'  => 'No existe',
						'NO_SABEMOS' => 'No sabemos',
					),
					'es_obligatorio' => TRUE,
				),
				'id_tipoclasif' => array(
					'tipo'           =>  'has_one',
					'relation'       => array(
						'model' => 'tipo_clasifalm'
					),
				),
				'tipo_op' => array(
					'label'          => 'Tipo operaci&oacute;n',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Seleccione el tipo de operaci&oacute;n.',
					'choices'        => array(
						'MOVIL' => 'Operaci&oacute;n M&oacute;vil',
						'FIJA'  => 'Operaci&oacute;n Fija'
					),
					'es_obligatorio' => TRUE,
					'onchange'       => "\$('#id_tiposalm').html('');\$.get('".site_url('stock_config/get_select_tipoalmacen')."'+'/'+\$('#id_tipo_op').val(), function(data){\$('#id_tiposalm').html(data);});",
				),
				'tiposalm' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'tipoalmacen_sap',
						'join_table'    => $this->config->item('bd_clasif_tipoalm_sap'),
						'id_one_table'  => array('id_clasif'),
						'id_many_table' => array('id_tipo'),
						'conditions'    => array('tipo_op' => '@field_value:tipo_op:MOVIL')
					),
					'texto_ayuda'    => 'Tipos de almac&eacute;n asociados a la clasificaci&oacute;n.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_clasif_almacen)
		{
			$this->fill($id_clasif_almacen);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Centro
	 */
	public function __toString()
	{
		return (string) $this->clasificacion;
	}



}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */