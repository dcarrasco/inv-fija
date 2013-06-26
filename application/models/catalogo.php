<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogo extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_catalogo',
						'model_label'        => 'Catalogo',
						'model_label_plural' => 'Catalogos',
						'model_order_by'     => 'catalogo',
					),
				'campos' => array(
						'catalogo' => array(
								'label'          => 'Catalogo',
								'tipo'           => 'char',
								'largo'          => 20,
								'texto_ayuda'    => 'Código del catálogo. Máximo 20 caracteres',
								'es_id'          => TRUE,
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE
							),
						'descripcion' => array(
								'label'          => 'Descripcion del material',
								'tipo'           =>  'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Descripción del material. Máximo 50 caracteres.',
								'es_obligatorio' => TRUE,
								//'es_unico'       => TRUE
							),
						'pmp' => array(
								'label'          => 'Precio Medio Ponderado (PMP)',
								'tipo'           =>  'real',
								'largo'          => 10,
								'decimales'      => 2,
								'texto_ayuda'    => 'Valor PMP del material',
								'es_obligatorio' => TRUE,
								'es_unico'       => FALSE
							),
						'es_seriado' => array(
								'label'          => 'Material seriado',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el material esta seriado dentro del sistema.',
								'es_obligatorio' => true,
								'default'        => 0
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->descripcion;
	}

	public function get_combo_catalogo($filtro = '')
	{
		$arr_result = array();
		$arr_combo = array();
		$arr_combo[''] = 'Seleccionar material ...';

		$this->db->order_by('catalogo')->like('descripcion', $filtro);
		$this->db->like('descripcion', $filtro);
		$this->db->or_like('catalogo', $filtro);
		$arr_result = $this->db->get('fija_catalogo')->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['catalogo']] = $reg['catalogo'] . ' - ' . $reg['descripcion'];
		}

		return $arr_combo;
	}



	public function get_descripcion($catalogo = '')
	{
		$row = $this->db->get_where('fija_catalogo', array('catalogo' => $catalogo))->row_array();
		return ($row['descripcion']);
	}



	public function get_materiales($filtro = '_', $limit = 0, $offset = 0)
	{
		$this->db->order_by('catalogo ASC');

		if ($filtro != '_')
		{
			$this->db->like('descripcion', $filtro);
		}
		return $this->db->get('fija_catalogo', $limit, $offset)->result_array();
	}



	public function total_materiales($filtro = '_')
	{
		if ($filtro != '_')
		{
			$this->db->like('descripcion', $filtro);
		}

		return $this->db->get('fija_catalogo')->num_rows();
	}


	public function get_cant_registros_catalogo($id = 0)
	{
		return ($this->db->get_where('fija_detalle_inventario', array('catalogo' => $id))->num_rows());
	}

	public function actualiza_precios()
	{

		$this->load->dbforge();

		// actualiza precios nulos --> 0
		$this->db->where('pmp is null');
		$this->db->update('fija_catalogo', array(
												'pmp' => 0,
						));

		// selecciona maxima fecha del stock_sap_fija
		$arr_max_fecha = $this->db->select('max(convert(varchar(8), fecha_stock, 112)) as fecha_stock', false)->get('bd_logistica..bd_stock_sap_fija')->row();
		$max_fecha = $arr_max_fecha->fecha_stock;

		// crea tabla temporal con ultimos precios
		if ($this->db->table_exists('tmp0001'))
		{
			$this->dbforge->drop_table('tmp0001');
		}
		$this->db->select('material, max(valor/cantidad) as pmp into tmp0001', false);
		$this->db->from('bd_logistica..bd_stock_sap_fija');
		$this->db->where('fecha_stock', $max_fecha);
		$this->db->group_by('material');
		$this->db->get();

		//actualiza los precios
		$this->db->query('update fija_catalogo set fija_catalogo.pmp=s.pmp from tmp0001 as s where catalogo collate Latin1_General_CI_AS = s.material collate Latin1_General_CI_AS');

		// cuenta los precios actualizados
		$cant_regs = $this->db->from('fija_catalogo')->join('tmp0001', 'fija_catalogo.catalogo collate Latin1_General_CI_AS = tmp0001.material collate Latin1_General_CI_AS')->count_all_results();

		// borra tabla temporal con ultimos precios
		if ($this->db->table_exists('tmp0001'))
		{
			$this->dbforge->drop_table('tmp0001');
		}

		return $cant_regs;
	}


}

/* End of file catalogo.php */
/* Location: ./application/models/catalogo.php */
