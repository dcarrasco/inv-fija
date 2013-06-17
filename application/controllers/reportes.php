<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes extends CI_Controller {

	private $id_inventario = 0;

	private $arr_menu = array(
				'hoja'              => array('url' => '/reportes/listado/hoja', 'texto' => 'Hoja'),
				'material'          => array('url' => '/reportes/listado/material', 'texto' => 'Material'),
				'material_faltante' => array('url' => '/reportes/listado/material_faltante', 'texto' => 'Faltante-Sobrante'),
				'ubicacion'         => array('url' => '/reportes/listado/ubicacion' , 'texto' => 'Ubicacion'),
				'tipos_ubicacion'        => array('url' => '/reportes/listado/tipos_ubicacion', 'texto' => 'Tipos Ubicacion'),
			);


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('reportes');

		$this->load->model('inventario_model');
	}


	public function index()
	{
		$this->listado('hoja');
	}

	/**
	 * [listado description]
	 * @param  string $tipo   [description]
	 * @param  string $param1 [description]
	 * @return [type]
	 */
	public function listado($tipo = 'hoja', $param1 = '')
	{
		// define reglas para usar set_value
		$this->form_validation->set_rules('inv_activo', '', 'trim');
		$this->form_validation->set_rules('elim_sin_dif', '', 'trim');
		$this->form_validation->set_rules('incl_ajustes', '', 'trim');
		$this->form_validation->set_rules('incl_familias', '', 'trim');
		$this->form_validation->set_rules('order_by', '', 'trim');
		$this->form_validation->set_rules('order_sort', '', 'trim');
		$this->form_validation->run();

		$id_inventario = set_value('inv_activo');
		$orden_campo   = set_value('order_by');
		$orden_tipo    = set_value('order_sort');
		$incl_ajustes  = set_value('incl_ajustes');
		$incl_familias = set_value('incl_familias');
		$elim_sin_dif  = set_value('elim_sin_dif');

		if ($id_inventario == '')
		{
			$id_inventario = $this->inventario_model->get_id_inventario_activo();
		}

		$new_orden_tipo  = ($orden_tipo == 'ASC') ? 'DESC' : 'ASC';
		$view = 'listado';

		if ($tipo == 'hoja')
		{
			$orden_campo = ($orden_campo == '') ? 'hoja' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_hoja($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();

			$arr_campos['hoja']             = array('titulo' => 'Hoja',         'class' => '',   'tipo' => 'link', 'href' => 'reportes/listado/detalle_hoja/');
			$arr_campos['auditor']          = array('titulo' => 'Auditor',      'class' => '',   'tipo' => 'texto');
			$arr_campos['digitador']        = array('titulo' => 'Digitador',    'class' => '',   'tipo' => 'texto');

			$arr_campos['sum_stock_sap']    = array('titulo' => 'Cant SAP',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'ac', 'tipo' => 'numero');
			}
			$arr_campos['sum_stock_diff']   = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');

			$arr_campos['sum_valor_sap']    = array('titulo' => 'Valor SAP',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'ac', 'tipo' => 'valor');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'ac', 'tipo' => 'valor');
			}
			$arr_campos['sum_valor_diff']   = array('titulo' => 'Valor Dif',    'class' => 'ac bold', 'tipo' => 'valor');
		}
		elseif ($tipo == 'detalle_hoja')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_detalle_hoja($id_inventario, $orden_campo, $orden_tipo, $param1, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['ubicacion']     = array('titulo' => 'Ubicacion',   'class' => '',   'tipo' => 'texto');
			$arr_campos['catalogo']      = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'link', 'href' => 'reportes/listado/detalle_material/');
			$arr_campos['descripcion']   = array('titulo' => 'Descripcion', 'class' => '',   'tipo' => 'texto');
			$arr_campos['lote']          = array('titulo' => 'lote',        'class' => '',   'tipo' => 'texto');
			$arr_campos['centro']        = array('titulo' => 'centro',      'class' => '',   'tipo' => 'texto');
			$arr_campos['almacen']       = array('titulo' => 'almacen',     'class' => '',   'tipo' => 'texto');
			$arr_campos['stock_sap']     = array('titulo' => 'Cant SAP',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['stock_fisico']  = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			if ($incl_ajustes == '1')
			{
				$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'ac', 'tipo' => 'numero');
			}
			$arr_campos['stock_diff']    = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');
			$arr_campos['valor_sap']     = array('titulo' => 'Valor SAP',     'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['valor_fisico']  = array('titulo' => 'Valor Fisico',  'class' => 'ac', 'tipo' => 'valor');
			if ($incl_ajustes == '1')
			{
				$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'ac', 'tipo' => 'valor');
			}
			$arr_campos['valor_diff']    = array('titulo' => 'Valor Dif',     'class' => 'ac bold', 'tipo' => 'valor');
		}
		elseif ($tipo == 'material')
		{
			$orden_campo = ($orden_campo == '') ? 'catalogo' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_material($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();

			if ($incl_familias == '1')
			{
				$arr_campos['nombre_fam']       = array('titulo' => 'Familia', 'class' => '',   'tipo' => 'subtotal');
			}

			$arr_campos['catalogo']         = array('titulo' => 'Catalogo', 'class' => '',   'tipo' => 'link', 'href' => 'reportes/listado/detalle_material/');
			$arr_campos['descripcion']      = array('titulo' => 'Descripcion',  'class' => '',   'tipo' => 'texto');
			$arr_campos['um']               = array('titulo' => 'UM',           'class' => '',   'tipo' => 'texto');
			$arr_campos['pmp']              = array('titulo' => 'PMP',          'class' => 'ac', 'tipo' => 'valor_pmp');

			$arr_campos['sum_stock_sap']    = array('titulo' => 'Cant SAP',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'ac', 'tipo' => 'numero');
			}
			$arr_campos['sum_stock_dif']    = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');

			$arr_campos['sum_valor_sap']    = array('titulo' => 'Valor SAP',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'ac', 'tipo' => 'valor');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'ac', 'tipo' => 'valor');
			}
			$arr_campos['sum_valor_diff']   = array('titulo' => 'Valor Dif',    'class' => 'ac bold', 'tipo' => 'valor');
		}
		elseif ($tipo == 'material_faltante')
		{
			$orden_campo = ($orden_campo == '') ? 'catalogo' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_material_faltante($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['catalogo']      = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'link', 'href' => 'reportes/listado/detalle_material/');
			$arr_campos['descripcion']      = array('titulo' => 'Descripcion',  'class' => '',   'tipo' => 'texto');
			$arr_campos['um']               = array('titulo' => 'UM',           'class' => '',   'tipo' => 'texto');
			$arr_campos['q_faltante']       = array('titulo' => 'Cant Faltante',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['q_coincidente']    = array('titulo' => 'Cant Coincidente',  'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['q_sobrante']       = array('titulo' => 'Cant Sobrante',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['v_faltante']       = array('titulo' => 'Valor Faltante',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['v_coincidente']    = array('titulo' => 'Valor Coincidente', 'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['v_sobrante']       = array('titulo' => 'Valor Sobrante',    'class' => 'ac', 'tipo' => 'valor');
		}
		elseif ($tipo == 'detalle_material')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_detalle_material($id_inventario, $orden_campo, $orden_tipo, $param1, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['catalogo']      = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'texto');
			$arr_campos['descripcion']   = array('titulo' => 'Descripcion', 'class' => '',   'tipo' => 'texto');
			$arr_campos['ubicacion']     = array('titulo' => 'Ubicacion',   'class' => '',   'tipo' => 'texto');
			$arr_campos['hoja']          = array('titulo' => 'Hoja',        'class' => '',   'tipo' => 'link', 'href' => 'reportes/listado/detalle_hoja/');
			$arr_campos['lote']          = array('titulo' => 'lote',        'class' => '',   'tipo' => 'texto');

			$arr_campos['stock_sap']     = array('titulo' => 'Cant SAP',    'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['stock_fisico']  = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			if ($incl_ajustes == '1')
			{
				$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'ac', 'tipo' => 'numero');
			}
			$arr_campos['stock_diff']    = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');

			$arr_campos['valor_sap']     = array('titulo' => 'Valor SAP',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['valor_fisico']  = array('titulo' => 'Valor Fisico', 'class' => 'ac', 'tipo' => 'valor');
			if ($incl_ajustes == '1')
			{
				$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'ac', 'tipo' => 'valor');
			}
			$arr_campos['valor_diff']    = array('titulo' => 'Valor Dif',    'class' => 'ac bold', 'tipo' => 'valor');
			/*
			$arr_campos['q_faltante']    = array('titulo' => 'Cant Faltante',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['q_coincidente'] = array('titulo' => 'Cant Coincidente',  'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['q_sobrante']    = array('titulo' => 'Cant Sobrante',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['v_faltante']    = array('titulo' => 'Valor Faltante',    'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['v_coincidente'] = array('titulo' => 'Valor Coincidente', 'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['v_sobrante']    = array('titulo' => 'Valor Sobrante',    'class' => 'ac', 'tipo' => 'numero');
			*/
		}
		elseif ($tipo == 'ubicacion')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_ubicacion($id_inventario, $orden_campo, $orden_tipo);

			$arr_campos = array();
			$arr_campos['ubicacion']        = array('titulo' => 'Ubicacion',    'class' => '',   'tipo' => 'texto');
			$arr_campos['sum_stock_sap']    = array('titulo' => 'Cant SAP',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['sum_stock_diff']   = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');
			$arr_campos['sum_valor_sap']    = array('titulo' => 'Valor SAP',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['sum_valor_diff']   = array('titulo' => 'Valor Dif',    'class' => 'ac bold', 'tipo' => 'valor');
		}
		elseif ($tipo == 'tipos_ubicacion')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo_ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_tipos_ubicacion($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['tipo_ubicacion']   = array('titulo' => 'Tipo Ubicacion', 'class' => '',   'tipo' => 'subtotal');
			$arr_campos['ubicacion']        = array('titulo' => 'Ubicacion',      'class' => '',   'tipo' => 'texto');
			$arr_campos['sum_stock_sap']    = array('titulo' => 'Cant SAP',     'class' => 'ac', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'ac', 'tipo' => 'numero');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'ac', 'tipo' => 'numero');
			}
			$arr_campos['sum_stock_diff']   = array('titulo' => 'Cant Dif',     'class' => 'ac bold', 'tipo' => 'numero');

			$arr_campos['sum_valor_sap']    = array('titulo' => 'Valor SAP',    'class' => 'ac', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'ac', 'tipo' => 'valor');
			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'ac', 'tipo' => 'valor');
			}
			$arr_campos['sum_valor_diff']   = array('titulo' => 'Valor Dif',    'class' => 'ac bold', 'tipo' => 'valor');
		}

		$arr_link_campos = array();
		$arr_link_sort   = array();
		$arr_img_orden   = array();
		foreach ($arr_campos as $campo => $valor)
		{
			$arr_link_campos[$campo] = $campo;

			$arr_link_sort[$campo] = (($campo == $orden_campo) ? $new_orden_tipo : 'ASC' );

			$arr_img_orden[$campo]   = ($campo == $orden_campo) ?
											'<img src="' . base_url() . 'img/' . (($orden_tipo == 'ASC') ?
											'ic_up_circle.png' : 'ic_down_circle.png') . '" />': '';
		}

		$data = array(
				'menu_modulo'     => array('menu' => $this->arr_menu, 'mod_selected' => $tipo),
				'datos_hoja'      => $datos_hoja,
				'tipo_reporte'    => $view,
				'nombre_reporte'  => $tipo,
				'filtro_dif'      => '',
				'arr_campos'      => $arr_campos,
				'arr_link_campos' => $arr_link_campos,
				'arr_link_sort'   => $arr_link_sort,
				'arr_img_orden'   => $arr_img_orden,
				'titulo_modulo'   => 'Reportes',
				'combo_inventarios' => $this->inventario_model->get_combo_inventarios(),
				'inventario_activo' => $this->inventario_model->get_id_inventario_activo(),
				'id_inventario'     => $id_inventario,
			);

		$this->load->view('app_header', $data);
		$this->load->view('inventario/reporte', $data);
		$this->load->view('app_footer', $data);

		//echo "<pre>"; var_dump($data); echo "</pre>";
	}

}

/* End of file reportes.php */
/* Location: ./application/controllers/reportes.php */
