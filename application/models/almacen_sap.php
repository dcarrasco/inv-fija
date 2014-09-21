<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacen_sap extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'bd_logistica..cp_almacenes',
						//'model_tabla'        => 'cp_almacenes',
						'model_label'        => 'Almacen',
						'model_label_plural' => 'Almacenes',
						'model_order_by'     => 'centro, cod_almacen',
					),
				'campos' => array(
						'centro' => array(
								'label'          => 'Centro',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Codigo SAP del centro. Maximo 10 caracteres.',
								'es_id'          => TRUE,
								'es_obligatorio' => TRUE,
							),
						'cod_almacen' => array(
								'label'          => 'Almacen',
								'tipo'           => 'char',
								'largo'          => 10,
								'texto_ayuda'    => 'Codigo SAP del almacen. Maximo 10 caracteres.',
								'es_id'          => TRUE,
								'es_obligatorio' => TRUE,
							),
						'des_almacen' => array(
								'label'          => 'Descripcion Almacen',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Descripcion del almacen. Maximo 50 caracteres.',
								'es_obligatorio' => TRUE,
							),
						'uso_almacen' => array(
								'label'          => 'Uso Almacen',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Indica para que se usa el almacén. Maximo 50 caracteres.',
							),
						'responsable' => array(
								'label'          => 'Responsable',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Nombre del responsable del almacen. Maximo 50 caracteres.',
							),
						'tipo_op' => array(
								'label'          => 'Tipo operacion',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Seleccione el tipo de operación.',
								'choices'        => array(
									'MOVIL' => 'Operación Móvil',
									'FIJA'  => 'Operación Fija'),
								'es_obligatorio' => TRUE,
							),
						'tipos' => array(
								'tipo'           => 'has_many',
								'relation'       => array(
										'model'         => 'tipoalmacen_sap',
										'join_table'    => 'bd_logistica..cp_tipos_almacenes',
										//'join_table'    => 'cp_tipos_almacenes',
										'id_one_table'  => array('centro','cod_almacen'),
										'id_many_table' => array('id_tipo'),
									),
								'texto_ayuda'    => 'Tipos asociados al almacen.',
							),
						),
					);
		parent::__construct($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string)$this->centro . '-' . $this->cod_almacen . ' ' .$this->des_almacen;
	}


	// --------------------------------------------------------------------

	public function almacenes_no_ingresados()
	{
		$alm_movil = $this->CI->db->distinct()
			->select('s.centro, s.cod_bodega')
			->from('bd_logistica..stock_scl s')
			->join('bd_logistica..cp_almacenes a', 's.cod_bodega=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from bd_logistica..stock_scl)')
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.cod_bodega')
			->get()
			->result_array();

		$alm_fija = $this->CI->db->distinct()
			->select('s.centro, s.almacen as cod_bodega')
			->from('bd_logistica..bd_stock_sap_fija s')
			->join('bd_logistica..cp_almacenes a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from bd_logistica..bd_stock_sap_fija)')
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.almacen')
			->get()
			->result_array();

		return array_merge($alm_movil, $alm_fija);
	}

}
/* End of file almacen.php */
/* Location: ./application/models/almacen.php */