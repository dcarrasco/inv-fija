<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detalle_inventario extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_detalle_inventario'),
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
						'model' => 'inventario',
					),
				),
				'hoja' => array(
					'label'          => 'Hoja',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'N&uacute;mero de la hoja usada en el inventario',
					'es_obligatorio' => TRUE,
				),
				'ubicacion' => array(
					'label'          => 'Ubicaci&oacute;n del material',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Indica la posici&oacute;n del material en el almac&eacute;n.',
					'es_obligatorio' => TRUE,
				),
				'hu' => array(
					'label'          => 'HU del material',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'Indica la HU del material en el almac&eacute;n.',
					'es_obligatorio' => FALSE,
				),
				'catalogo' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'catalogo',
					),
					'texto_ayuda'    => 'Cat&aacute;logo del material.',
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del material',
					'tipo'           => 'char',
					'largo'          => 45,
					'texto_ayuda'    => 'M&aacute;ximo 45 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'lote' => array(
					'label'          => 'Lote del material',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Lote del material.',
					'es_obligatorio' => TRUE,
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
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Stock sist&eacute;mico (SAP) del material.',
					'es_obligatorio' => TRUE,
				),
				'stock_fisico' => array(
					'label'          => 'Stock f&iacute;sico del material',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'Stock f&iacute;sico (inventariado) del material.',
					'es_obligatorio' => TRUE,
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
					'tipo'           => 'boolean',
					'texto_ayuda'    => 'Indica si el registro es nuevo.',
					'es_obligatorio' => TRUE,
				),
				'fecha_modificacion' => array(
					'label'          => 'Fecha de modificacion',
					'tipo'           => 'datetime',
					'texto_ayuda'    => 'Fecha de modificaci&oacute;n del registro.',
					'es_obligatorio' => TRUE,
				),
				'observacion' => array(
					'label'          => 'Observaci&oacute;n de registro',
					'tipo'           => 'char',
					'largo'          => 200,
					'texto_ayuda'    => 'M&aacute;ximo 200 caracteres.',
				),
				'stock_ajuste' => array(
					'label'          => 'Stock de ajuste del material',
					'tipo'           => 'int',
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 100 caracteres.',
				),
				'glosa_ajuste' => array(
					'label'          => 'Observaci&oacute;n del ajuste',
					'tipo'           => 'char',
					'largo'          => 100,
					'texto_ayuda'    => 'M&aacute;ximo 100 caracteres.',
				),
				'fecha_ajuste' => array(
					'label'          => 'Fecha del ajuste',
					'tipo'           => 'datetime',
					'texto_ayuda'    => 'Fecha de modificacion del ajuste.',
				),
			),
		);
		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return $this->hoja;
	}


	// --------------------------------------------------------------------

	public function get_hoja($id_inventario = 0, $hoja = 0)
	{
		$this->find('all', array('conditions' => array('id_inventario' => $id_inventario, 'hoja' => $hoja)));
	}


	// --------------------------------------------------------------------

	public function get_nombre_auditor()
	{
		if (count($this->get_model_all()) > 0)
		{
			$all = $this->get_model_all();
			$all_fields = $all[0]->get_model_fields();
			$rel = $all_fields['auditor']->get_relation();

			return $rel['data']->nombre;
		}
	}


	// --------------------------------------------------------------------

	public function get_id_auditor()
	{
		if(count($this->get_model_all()) > 0)
		{
			$all = $this->get_model_all();

			return $all[0]->auditor;
		}
		else
		{
			return 0;
		}
	}


	// --------------------------------------------------------------------

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


	// --------------------------------------------------------------------

	/**
	 * Recuoera las lineas de detalle del inventario para ajustar
	 * @param  integer $id_inventario         ID del inventario a consultar
	 * @param  integer $ocultar_regularizadas indicador si se ocultan los registros ya regularizados
	 * @param  integer $pag                   pagina a mostrar
	 * @return array                          Arreglo con el detalle de los registros
	 */
	public function get_ajustes($id_inventario = 0, $ocultar_regularizadas = 0, $pag = 0)
	{
		//determina la cantidad de registros
		$total_rows = $this->CI->db
			->where('id_inventario', $id_inventario)
			->where(
				($ocultar_regularizadas == 1) ?
				'stock_fisico - stock_sap + stock_ajuste <> 0' :
				'stock_fisico - stock_sap <> 0',
				NULL, FALSE
			)
			->count_all_results($this->get_model_tabla());

		// recupera el detalle de registros
		$per_page = 50;

		$rs = $this->CI->db
			->order_by('catalogo, lote, centro, almacen, ubicacion')
			->where('id_inventario', $id_inventario)
			->where(
				($ocultar_regularizadas == 1) ?
				'stock_fisico - stock_sap + stock_ajuste <> 0' :
				'stock_fisico - stock_sap <> 0',
				NULL, FALSE
			)
			->limit($per_page, $pag)
			->get($this->get_model_tabla())
			->result_array();

		$this->CI->load->library('pagination');
		$cfg_pagination = array(
			'uri_segment' => 4,
			'num_links'   => 5,

			'full_tag_open'   => '<ul class="pagination">',
			'flil_tag_close'  => '</ul>',

			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',

			'per_page'    => $per_page,
			'total_rows'  => $total_rows,
			'base_url'    => site_url($this->CI->uri->segment(1) . '/ajustes/' . $ocultar_regularizadas . '/'),
			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo (' . (int)($total_rows / $per_page) . ')',
			'prev_link'   => '<span class="glyphicon glyphicon-chevron-left"></span>',
			'next_link'   => '<span class="glyphicon glyphicon-chevron-right"></span>',
		);
		$this->CI->pagination->initialize($cfg_pagination);

		$model_all = array();
		foreach($rs as $reg)
		{
			$o = new Detalle_inventario();
			$o->get_from_array($reg);
			array_push($model_all, $o);
		}
		$this->set_model_all($model_all);

		return $this->CI->pagination->create_links();
	}


}
/* End of file detalle_inventario.php */
/* Location: ./application/models/detalle_inventario.php */