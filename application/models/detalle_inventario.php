<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detalle_inventario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_detalle_inventario',
						'model_label'        => 'Detalle inventario',
						'model_label_plural' => 'Detalles inventario',
						'model_order_by'     => 'id',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'id_inventario' => array(
								'tipo'           => 'has_one',
								'relation'       => array(
										'model' => 'inv_activo',
									),
							),
						'hoja' => array(
								'label'          => 'Hoja',
								'tipo'           => 'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Numero de la hoja usada en el inventario',
								'es_obligatorio' => true,
							),
						'ubicacion' => array(
								'label'          => 'Ubicación del material',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Indica la posición del material en el almacén.',
								'es_obligatorio' => true,
							),
						'catalogo' => array(
								'tipo'           => 'has_one',
								'relation'       => array(
										'model' => 'catalogo',
									),
								'texto_ayuda'    => 'Catálogo del material.',
							),
						'descripcion' => array(
								'label'          => 'Descripcion del material',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Maximo 45 caracteres.',
								'es_obligatorio' => true,
							),
						'lote' => array(
								'label'          => 'Lote del material',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Lote del material.',
								'es_obligatorio' => true,
							),
						'centro' => array(
								'label'          => 'Centro SAP del material',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Centro SAP del material.',
								'es_obligatorio' => true,
								'choices'        => array(
														''     => 'Seleccione un centro...',
														'CH01' => 'CH01',
														'CH02' => 'CH02',
														'CH04' => 'CH04',
														'CH05' => 'CH05',
														'CH11' => 'CH11',
														'CH19' => 'CH19',
														'CH24' => 'CH24',
														'CH28' => 'CH28',
														'CH29' => 'CH29',
													),
							),
						'almacen' => array(
								'label'          => 'Almacen SAP del material',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Almacén SAP del material.',
								'es_obligatorio' => true,
								'choices'        => array(
														''     => 'Seleccione un almacen...',
														'CM01' => 'CM01',
														'CM02' => 'CM02',
														'CM03' => 'CM03',
														'CM04' => 'CM04',
														'CM11' => 'CM11',
														'CM15' => 'CM15',
														'GM01' => 'GM01',
														'GM03' => 'GM03',
													),
							),
						'um' => array(
								'label'          => 'Unidad de medida',
								'tipo'           =>  'char',
								'largo'          => 45,
								'texto_ayuda'    => 'Maximo 45 caracteres.',
								'es_obligatorio' => true,
								'choices'        => array(
														''     => 'Seleccione una unidad...',
														'UN' => 'UN',
														'M' => 'M',
														'KG' => 'KG',
														'ST' => 'ST',
														'EA' => 'EA',
													),
							),
						'stock_sap' => array(
								'label'          => 'Stock SAP del material',
								'tipo'           =>  'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Stock sistémico (SAP) del material.',
								'es_obligatorio' => true,
							),
						'stock_fisico' => array(
								'label'          => 'Stock Fisico del material',
								'tipo'           =>  'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Stock físico (inventariado) del material.',
								'es_obligatorio' => true,
							),
						'digitador' => array(
								'tipo'           => 'has_one',
								'relation'       => array(
										'model' => 'usuario',
									),
								'texto_ayuda'    => 'Digitador de la hoja.',
							),
						'auditor' => array(
								'tipo'           => 'has_one',
								'relation'       => array(
										'model' => 'auditor',
									),
								'texto_ayuda'    => 'Auditor de la hoja.',
							),
						'reg_nuevo' => array(
								'label'          => 'Registro nuevo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica si el registro es nuevo.',
								'es_obligatorio' => true,
							),
						'fecha_modificacion' => array(
								'label'          => 'Fecha de modificacion',
								'tipo'           =>  'datetime',
								'texto_ayuda'    => 'Fecha de modificación del registro.',
								'es_obligatorio' => true,
							),
						'observacion' => array(
								'label'          => 'Observacion de registro',
								'tipo'           =>  'char',
								'largo'          => 200,
								'texto_ayuda'    => 'Maximo 200 caracteres.',
								'es_obligatorio' => true,
							),
						'stock_ajuste' => array(
								'label'          => 'Stock de ajuste del material',
								'tipo'           =>  'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
							),
						'glosa_ajuste' => array(
								'label'          => 'Observacion del ajuste',
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
								'es_obligatorio' => true,
							),
						'fecha_ajuste' => array(
								'label'          => 'Fecha del ajuste',
								'tipo'           =>  'datetime',
								'texto_ayuda'    => 'Fecha de modificacion del ajuste.',
								'es_obligatorio' => true,
							),
				),
			);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->hoja;
	}

	public function get_hoja($id_inventario = 0, $hoja = 0)
	{
		$this->get_all_where(array('id_inventario'=>$id_inventario, 'hoja'=>$hoja));
	}

	public function get_nombre_auditor()
	{
		if(count($this->get_model_all()) > 0)
		{
			$all = $this->get_model_all();
			$all_fields = $all[0]->get_model_fields();
			$rel = $all_fields['auditor']->get_relation();
			return $rel['data']->nombre;
		}
	}

	public function get_nombre_digitador()
	{
		if(count($this->get_model_all()) > 0)
		{
			$all = $this->get_model_all();
			$all_fields = $all[0]->get_model_fields();
			$rel = $all_fields['digitador']->get_relation();
			return $rel['data']->nombre;
		}
		else if ($this->digitador != 0)
		{
			return $this->digitador;
		}
		else
		{
			return '[nombre digitador]';
		}
	}

}

/* End of file detalle_inventario.php */
/* Location: ./application/models/detalle_inventario.php */