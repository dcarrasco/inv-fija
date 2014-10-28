<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacen_sap extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_almacenes_sap'),
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
							'FIJA'  => 'Operación Fija'
						),
						'es_obligatorio' => TRUE,
				),
				'tipos' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'tipoalmacen_sap',
						'join_table'    => $this->CI->config->item('bd_tipoalmacen_sap'),
						'id_one_table'  => array('centro','cod_almacen'),
						'id_many_table' => array('id_tipo'),
					),
					'texto_ayuda'    => 'Tipos asociados al almacen.',
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string)$this->centro . '-' . $this->cod_almacen . ' ' .$this->des_almacen;
	}


	// --------------------------------------------------------------------

	public function get_combo_almacenes($tipo_op = '')
	{
		$arr_result = array();
		$arr_combo = array();

		$arr_result = $this->CI->db
			->select('centro + \'-\' + cod_almacen as llave')
			->select('centro + \'-\' + cod_almacen + \' \' + des_almacen as valor')
			->order_by('centro, cod_almacen')
			->where('tipo_op', $tipo_op)
			->get($this->get_model_tabla())
			->result_array();

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	public function almacenes_no_ingresados()
	{
		$alm_movil = $this->CI->db->distinct()
			->select('s.centro, s.cod_bodega')
			->from($this->CI->config->item('bd_stock_movil') . ' s')
			->join($this->CI->config->item('bd_almacenes_sap') . ' a', 's.cod_bodega=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from ' . $this->CI->config->item('bd_stock_movil') . ')')
			->where('a.cod_almacen is null')
			->order_by('s.centro')
			->order_by('s.cod_bodega')
			->get()
			->result_array();

		$alm_fija = $this->CI->db->distinct()
			->select('s.centro, s.almacen as cod_bodega')
			->from($this->CI->config->item('bd_stock_fija') . ' s')
			->join($this->CI->config->item('bd_almacenes_sap') . ' a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
			->where('fecha_stock in (select max(fecha_stock) from ' . $this->CI->config->item('bd_stock_fija') . ')')
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