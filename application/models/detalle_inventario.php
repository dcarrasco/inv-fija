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
								'largo'          => 10,
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
								'largo'          => 10,
								'texto_ayuda'    => 'Lote del material.',
								'es_obligatorio' => true,
							),
						'centro' => array(
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'centro'
									),
							),
						'almacen' => array(
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'almacen'
									),
							),
						'um' => array(
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'unidad_medida'
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
										'model'      => 'auditor',
										'conditions' => array('activo' => 1),
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
							),
						'stock_ajuste' => array(
								'label'          => 'Stock de ajuste del material',
								'tipo'           =>  'int',
								'largo'          => 10,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
							),
						'glosa_ajuste' => array(
								'label'          => 'Observacion del ajuste',
								'tipo'           =>  'char',
								'largo'          => 100,
								'texto_ayuda'    => 'Maximo 100 caracteres.',
							),
						'fecha_ajuste' => array(
								'label'          => 'Fecha del ajuste',
								'tipo'           =>  'datetime',
								'texto_ayuda'    => 'Fecha de modificacion del ajuste.',
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
		$this->find('all', array('conditions' => array('id_inventario'=>$id_inventario, 'hoja'=>$hoja)));
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


	/**
	 * Recuoera las lineas de detalle del inventario para ajustar
	 * @param  integer $id_inventario         ID del inventario a consultar
	 * @param  integer $ocultar_regularizadas indicador si se ocultan los registros ya regularizados
	 * @return array                          Arreglo con el detalle de los registros
	 */
	public function get_ajustes($id_inventario = 0, $ocultar_regularizadas = 0)
	{
		$this->db->order_by('catalogo, lote, centro, almacen, ubicacion');
		$this->db->where('id_inventario', $id_inventario);
		if ($ocultar_regularizadas == 1)
		{
			$this->db->where('stock_fisico - stock_sap + stock_ajuste <> 0');
		}
		else
		{
			$this->db->where('stock_fisico - stock_sap <> 0');
		}

		$rs = $this->db->get('fija_detalle_inventario')->result_array();
		$model_all = array();
		foreach($rs as $reg)
		{
			$o = new Detalle_inventario();
			$o->get_from_array($reg);
			array_push($model_all, $o);
		}
		$this->set_model_all($model_all);
	}




}

/* End of file detalle_inventario.php */
/* Location: ./application/models/detalle_inventario.php */