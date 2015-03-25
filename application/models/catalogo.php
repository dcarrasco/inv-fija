<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogo extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_catalogos'),
				'model_label'        => 'Cat&aacute;logo',
				'model_label_plural' => 'Cat&aacute;logos',
				'model_order_by'     => 'catalogo',
			),
			'campos' => array(
				'catalogo' => array(
					'label'          => 'Cat&aacute;logo',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'C&oacute;digo del cat&aacute;logo. M&aacute;ximo 20 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del material',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del material. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					//'es_unico'       => TRUE
				),
				'pmp' => array(
					'label'          => 'Precio Medio Ponderado (PMP)',
					'tipo'           => 'real',
					'largo'          => 10,
					'decimales'      => 2,
					'texto_ayuda'    => 'Valor PMP del material',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
					'formato'        => 'monto,2',
				),
				'es_seriado' => array(
					'label'          => 'Material seriado',
					'tipo'           => 'boolean',
					'texto_ayuda'    => 'Indica se el material est&aacute; seriado en el sistema.',
					'es_obligatorio' => TRUE,
					'default'        => 0
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->descripcion;
	}


	// --------------------------------------------------------------------

	public function get_combo_catalogo($filtro = '')
	{
		$arr_combo = array();
		$arr_combo[''] = 'Seleccionar material ...';

		$arr_result = $this->CI->db
			->select('catalogo as llave')
			->select("catalogo + ' - ' + descripcion as valor")
			->order_by('catalogo')
			->like('descripcion', $filtro)
			->like('descripcion', $filtro)
			->or_like('catalogo', $filtro)
			->get($this->get_model_tabla())
			->result_array();

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	public function get_descripcion($catalogo = '')
	{
		$row = $this->CI->db
			->get_where($this->get_model_tabla(), array('catalogo' => $catalogo))
			->row_array();

		return ($row['descripcion']);
	}


	// --------------------------------------------------------------------

	public function get_materiales($filtro = '_', $limit = 0, $offset = 0)
	{
		$this->CI->db->order_by('catalogo ASC');

		if ($filtro != '_')
		{
			$this->CI->db->like('descripcion', $filtro);
		}

		return $this->CI->db
			->get($this->get_model_tabla(), $limit, $offset)
			->result_array();
	}


	// --------------------------------------------------------------------

	public function total_materiales($filtro = '_')
	{
		if ($filtro != '_')
		{
			$this->CI->db->like('descripcion', $filtro);
		}

		return $this->CI->db
			->get($this->get_model_tabla())
			->num_rows();
	}


	// --------------------------------------------------------------------

	public function get_cant_registros_catalogo($id = 0)
	{
		return $this->CI->db
			->get_where($this->CI->config->item('bd_detalle_inventario'), array('catalogo' => $id))
			->num_rows();
	}


	// --------------------------------------------------------------------

	public function actualiza_precios()
	{

		$this->CI->load->dbforge();

		// actualiza precios nulos --> 0
		$this->CI->db->where('pmp is null')
			->update($this->get_model_tabla(), array('pmp' => 0));

		// selecciona maxima fecha del stock_sap_fija
		$arr_max_fecha = $this->CI->db
			->select('max(convert(varchar(8), fecha_stock, 112)) as fecha_stock', FALSE)
			->get($this->CI->config->item('bd_stock_fija'))
			->row();

		$max_fecha = $arr_max_fecha->fecha_stock;

		// crea tabla temporal con ultimos precios
		if ($this->CI->db->table_exists('tmp0001'))
		{
			$this->CI->dbforge->drop_table('tmp0001');
		}

		$this->CI->db
			->select('material, max(valor/cantidad) as pmp into tmp0001', FALSE)
			->from($this->CI->config->item('bd_stock_fija'))
			->where('fecha_stock', $max_fecha)
			->where_in('lote', array('I', 'A'))
			->group_by('material')
			->get();

		//actualiza los precios
		$this->CI->db->query('update ' . $this->get_model_tabla() . ' set fija_catalogos.pmp=s.pmp from tmp0001 as s where catalogo collate Latin1_General_CI_AS = s.material collate Latin1_General_CI_AS');

		// cuenta los precios actualizados
		$cant_regs = $this->CI->db
			->query(
				'SELECT count(*) as cant ' .
				'FROM ' . $this->get_model_tabla() . ' as c ' .
				'JOIN tmp0001 as t on (c.catalogo collate Latin1_General_CI_AS = t.material collate Latin1_General_CI_AS)'
			)
			->row()
			->cant;

		// borra tabla temporal con ultimos precios
		if ($this->CI->db->table_exists('tmp0001'))
		{
			$this->CI->dbforge->drop_table('tmp0001');
		}

		return $cant_regs;
	}


}
/* End of file catalogo.php */
/* Location: ./application/models/catalogo.php */
