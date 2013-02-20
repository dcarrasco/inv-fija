<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movil_detalle_inventario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'movil_detalle_inventario',
						'model_label'        => 'Detalle inventario',
						'model_label_plural' => 'Detalles inventarios',
						'model_order_by'     => 'id_inventario, serie',
					),
				'campos' => array(
						'id_inventario' => array(
								'label'          => 'Identificador de inventario',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Identificador de inventario',
								'es_obligatorio' => true,
								'es_id'          => true,
							),
						'id_captura' => array(
								'label'          => 'Identificador del proceso de captura',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Identificador del proceso de captura',
								'es_obligatorio' => true,
							),
						'serie' => array(
								'label'          => 'Serie capturada',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
								'es_id'          => true,
							),
						'ind_faltante' => array(
								'label'          => 'Indicador de faltante',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => '(NO se usa)',
								'es_obligatorio' => true,
							),
						'fecha_captura' => array(
								'label'          => 'Fecha de captura',
								'tipo'           => 'datetime',
								'texto_ayuda'    => 'Fecha de la captura de la serie',
								'es_obligatorio' => true,
							),
						'material' => array(
								'label'          => 'Material de la serie',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Material de la serie capturada',
								'es_obligatorio' => true,
							),
						'ubicacion' => array(
								'label'          => 'Ubicacion de la serie',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Ubicacion fisica de la serie en el almacen',
								'es_obligatorio' => true,
							),
						'linea' => array(
								'label'          => 'Numero de linea',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Linea en la cual fue capturada la serie',
								'es_obligatorio' => true,
							),
						'lote' => array(
								'label'          => 'Lote',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Lote SAP de la serie capturada',
								'es_obligatorio' => true,
							),
						'caja' => array(
								'label'          => 'Caja',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Numero de la caja en la cual se encontró la serie',
								'es_obligatorio' => true,
							),
						'almacen' => array(
								'label'          => 'Almacen',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Almacen SAP de la serie capturada',
								'es_obligatorio' => true,
							),
						'indic_invent' => array(
								'label'          => 'Estado faltante',
								'tipo'           => 'char',
								'largo'          => 1,
								'texto_ayuda'    => 'Indicador de serie faltante, sobrante o coincidente',
								'es_obligatorio' => true,
							),
						'serie_sap' => array(
								'label'          => 'Serie SAP',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Serie capturada en formato SAP',
								'es_obligatorio' => true,
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->serie;
	}



}

/* End of file Movil_detalle_inventario.php */
/* Location: ./application/models/Movil_detalle_inventario.php */
