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
 * Clase Modelo Catalogo
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
class Catalogo extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_catalogo Identificador del catalogo
	 * @return void
	 */
	public function __construct($id_catalogo = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_catalogos'),
				'model_label'        => 'Cat&aacute;logo',
				'model_label_plural' => 'Cat&aacute;logos',
				'model_order_by'     => 'catalogo',
			),
			'campos' => array(
				'catalogo' => array(
					'label'          => 'Cat&aacute;logo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'C&oacute;digo del cat&aacute;logo. M&aacute;ximo 20 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del material',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del material. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					//'es_unico'       => TRUE
				),
				'pmp' => array(
					'label'          => 'Precio Medio Ponderado (PMP)',
					'tipo'           => Orm_field::TIPO_REAL,
					'largo'          => 10,
					'decimales'      => 2,
					'texto_ayuda'    => 'Valor PMP del material',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
					'formato'        => 'monto,1',
				),
				'es_seriado' => array(
					'label'          => 'Material seriado',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica si el material est&aacute; seriado en el sistema.',
					'es_obligatorio' => TRUE,
					'default'        => 0
				),
				// 'foto' => array(
				// 	'label'          => 'Foto del material',
				// 	'tipo'           => 'picture',
				// 	'texto_ayuda'    => 'Foto o imagen del material.',
				// 	'es_obligatorio' => FALSE,
				// ),
				'tip_material' => array(
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => array(
						'model'         => 'Tip_material_trabajo_toa',
						'join_table'    => $this->config->item('bd_catalogo_tip_material_toa'),
						'id_one_table'  => array('id_catalogo'),
						'id_many_table' => array('id_tip_material_trabajo'),
						//'conditions'    => array('id_app' => '@field_value:id_app'),
					),
					'texto_ayuda'    => 'Tipo de material TOA.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_catalogo)
		{
			$this->fill($id_catalogo);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Catalogo
	 */
	public function __toString()
	{
		return (string) $this->descripcion;
	}



	// --------------------------------------------------------------------

	/**
	 * Actualiza los precios del catálogos de productos, con el último PMP
	 *
	 * @return int Cantidad de registros modificados
	 */
	public function actualiza_precios()
	{
		$tabla_temporal_precios = 'tmp_actualiza_precios';

		$this->load->dbforge();

		// actualiza precios nulos --> 0
		$this->db->where('pmp is null')
			->update($this->get_model_tabla(), array('pmp' => 0));

		// selecciona maxima fecha del stock_sap_fija
		$arr_max_fecha = $this->db
			->select('max(fecha_stock) as fecha_stock', FALSE)
			->get($this->config->item('bd_stock_fija'))
			->row();

		$max_fecha = fmt_fecha_db($arr_max_fecha->fecha_stock);

		// crea tabla temporal con ultimos precios
		if ($this->db->table_exists($tabla_temporal_precios))
		{
			$this->dbforge->drop_table($tabla_temporal_precios);
		}

		$this->db
			->select('material, max(valor/cantidad) as pmp into '.$tabla_temporal_precios, FALSE)
			->from($this->config->item('bd_stock_fija'))
			->where('fecha_stock', $max_fecha)
			->where_in('lote', array('I', 'A'))
			->group_by('material')
			->get();

		//actualiza los precios
		$this->db->query(
			'UPDATE '.$this->get_model_tabla().' '.
			'SET fija_catalogos.pmp=s.pmp '.
			'FROM '.$tabla_temporal_precios.' as s '.
			'WHERE catalogo collate Latin1_General_CI_AS = s.material collate Latin1_General_CI_AS'
		);

		// cuenta los precios actualizados
		$cant_regs = $this->db->query(
			'SELECT count(*) as cant '.
			'FROM '.$this->get_model_tabla().' as c '.
			'JOIN '.$tabla_temporal_precios.' as t on (c.catalogo collate Latin1_General_CI_AS = t.material collate Latin1_General_CI_AS)'
		)->row()->cant;

		// borra tabla temporal con ultimos precios
		if ($this->db->table_exists($tabla_temporal_precios))
		{
			$this->dbforge->drop_table($tabla_temporal_precios);
		}

		return $cant_regs;
	}


}
/* End of file catalogo.php */
/* Location: ./application/models/catalogo.php */
