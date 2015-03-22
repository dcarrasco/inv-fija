<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_reportes extends CI_Controller {

	public $llave_modulo = 'reportes';
	private $id_inventario = 0;
	private $arr_menu = array();

	// --------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		$this->load->model('inventario_model');
		$this->lang->load('inventario');

		$this->arr_menu = array(
			'hoja' => array(
				'url'   => $this->uri->segment(1) . '/listado/hoja',
				'texto' => $this->lang->line('inventario_menu_reporte_hoja'),
			),
			'material' => array(
				'url'   => $this->uri->segment(1) . '/listado/material',
				'texto' => $this->lang->line('inventario_menu_reporte_mat'),
			),
			'material_faltante' => array(
				'url'   => $this->uri->segment(1) . '/listado/material_faltante',
				'texto' => $this->lang->line('inventario_menu_reporte_faltante'),
			),
			'ubicacion' => array(
				'url'   => $this->uri->segment(1) . '/listado/ubicacion' ,
				'texto' => $this->lang->line('inventario_menu_reporte_ubicacion'),
			),
			'tipos_ubicacion' => array(
				'url'   => $this->uri->segment(1) . '/listado/tipos_ubicacion',
				'texto' => $this->lang->line('inventario_menu_reporte_tip_ubic'),
			),
			'ajustes' => array(
				'url'   => $this->uri->segment(1) . '/listado/ajustes',
				'texto' => $this->lang->line('inventario_menu_reporte_ajustes'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @param  none
	 * @return none
	 */
	public function index()
	{
		$this->listado('hoja');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera los diferentes listados de reportes de inventario
	 *
	 * @param  string $tipo   Tipo de reporte
	 * @param  string $param1 Parámetro variable
	 * @return none
	 */
	public function listado($tipo = 'hoja', $param1 = '')
	{
		// define reglas para usar set_value
		$this->form_validation->set_rules('inv_activo',    '', 'trim');
		$this->form_validation->set_rules('elim_sin_dif',  '', 'trim');
		$this->form_validation->set_rules('incl_ajustes',  '', 'trim');
		$this->form_validation->set_rules('incl_familias', '', 'trim');
		$this->form_validation->set_rules('order_by',      '', 'trim');
		$this->form_validation->set_rules('order_sort',    '', 'trim');
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

			$arr_campos['hoja'] = array('titulo' => 'Hoja',         'class' => '',   'tipo' => 'link', 'href' => $this->uri->segment(1) . '/listado/detalle_hoja/');
			$arr_campos['auditor'] = array('titulo' => 'Auditor',      'class' => '',   'tipo' => 'texto');
			$arr_campos['digitador'] = array('titulo' => 'Digitador',    'class' => '',   'tipo' => 'texto');

			$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP',     'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['sum_stock_diff'] = array('titulo' => 'Cant Dif',     'class' => 'text-center', 'tipo' => 'numero_dif');

			$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP',    'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif',    'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'detalle_hoja')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_detalle_hoja($id_inventario, $orden_campo, $orden_tipo, $param1, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion',   'class' => '',   'tipo' => 'texto');
			$arr_campos['catalogo'] = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'link', 'href' => $this->uri->segment(1) . '/listado/detalle_material/');
			$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '',   'tipo' => 'texto');
			$arr_campos['lote'] = array('titulo' => 'lote',        'class' => '',   'tipo' => 'texto');
			$arr_campos['centro'] = array('titulo' => 'centro',      'class' => '',   'tipo' => 'texto');
			$arr_campos['almacen'] = array('titulo' => 'almacen',     'class' => '',   'tipo' => 'texto');

			$arr_campos['stock_sap'] = array('titulo' => 'Cant SAP',     'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['stock_diff'] = array('titulo' => 'Cant Dif',     'class' => 'text-center', 'tipo' => 'numero_dif');
			$arr_campos['valor_sap'] = array('titulo' => 'Valor SAP',     'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['valor_fisico'] = array('titulo' => 'Valor Fisico',  'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['valor_diff'] = array('titulo' => 'Valor Dif',     'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'material')
		{
			$orden_campo = ($orden_campo == '') ? 'catalogo' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_material($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();

			if ($incl_familias == '1')
			{
				$arr_campos['nombre_fam'] = array('titulo' => 'Familia', 'class' => '', 'tipo' => 'subtotal');
			}

			$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'class' => '', 'tipo' => 'link', 'href' => $this->uri->segment(1) . '/listado/detalle_material/');
			$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '', 'tipo' => 'texto');
			$arr_campos['um'] = array('titulo' => 'UM', 'class' => '', 'tipo' => 'texto');
			$arr_campos['pmp'] = array('titulo' => 'PMP', 'class' => 'text-center', 'tipo' => 'valor_pmp');

			$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['sum_stock_dif'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

			$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'material_faltante')
		{
			$orden_campo = ($orden_campo == '') ? 'catalogo' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_material_faltante($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['catalogo'] = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'link', 'href' => $this->uri->segment(1) . '/listado/detalle_material/');
			$arr_campos['descripcion'] = array('titulo' => 'Descripcion',  'class' => '',   'tipo' => 'texto');
			$arr_campos['um'] = array('titulo' => 'UM',           'class' => '',   'tipo' => 'texto');
			$arr_campos['q_faltante'] = array('titulo' => 'Cant Faltante',     'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['q_coincidente'] = array('titulo' => 'Cant Coincidente',  'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['q_sobrante'] = array('titulo' => 'Cant Sobrante',     'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['v_faltante'] = array('titulo' => 'Valor Faltante',    'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['v_coincidente'] = array('titulo' => 'Valor Coincidente', 'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['v_sobrante'] = array('titulo' => 'Valor Sobrante',    'class' => 'text-center', 'tipo' => 'valor');
		}
		elseif ($tipo == 'detalle_material')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_detalle_material($id_inventario, $orden_campo, $orden_tipo, $param1, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['catalogo'] = array('titulo' => 'Catalogo',    'class' => '',   'tipo' => 'texto');
			$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '',   'tipo' => 'texto');
			$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion',   'class' => '',   'tipo' => 'texto');
			$arr_campos['hoja'] = array('titulo' => 'Hoja',        'class' => '',   'tipo' => 'link', 'href' => $this->uri->segment(1) . '/listado/detalle_hoja/');
			$arr_campos['lote'] = array('titulo' => 'lote',        'class' => '',   'tipo' => 'texto');

			$arr_campos['stock_sap'] = array('titulo' => 'Cant SAP',    'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['stock_diff'] = array('titulo' => 'Cant Dif',     'class' => 'text-center', 'tipo' => 'numero_dif');

			$arr_campos['valor_sap'] = array('titulo' => 'Valor SAP',    'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['valor_diff'] = array('titulo' => 'Valor Dif',    'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'ubicacion')
		{
			$orden_campo = ($orden_campo == '') ? 'ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_ubicacion($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion',    'class' => '',   'tipo' => 'texto');

			$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP',     'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico',  'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste',  'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['sum_stock_dif'] = array('titulo' => 'Cant Dif',     'class' => 'text-center', 'tipo' => 'numero_dif');

			$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP',    'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste',  'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['sum_valor_dif'] = array('titulo' => 'Valor Dif',    'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'tipos_ubicacion')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo_ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_tipos_ubicacion($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['tipo_ubicacion'] = array('titulo' => 'Tipo Ubicacion', 'class' => '', 'tipo' => 'subtotal');
			$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'texto');
			$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
			}

			$arr_campos['sum_stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

			$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
			$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

			if ($incl_ajustes == '1')
			{
				$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
			}

			$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');
		}
		elseif ($tipo == 'ajustes')
		{
			$orden_campo = ($orden_campo == '') ? 'tipo_ubicacion' : $orden_campo;
			$datos_hoja = $this->inventario_model->get_reporte_ajustes($id_inventario, $orden_campo, $orden_tipo, $elim_sin_dif);

			$arr_campos = array();
			$arr_campos['catalogo'] = array('titulo' => 'Material', 'class' => '', 'tipo' => 'text');
			$arr_campos['descripcion']  = array('titulo' => 'Desc Material', 'class' => '', 'tipo' => 'text');
			$arr_campos['lote'] = array('titulo' => 'Lote', 'class' => '', 'tipo' => 'text');
			$arr_campos['centro'] = array('titulo' => 'Centro', 'class' => '', 'tipo' => 'text');
			$arr_campos['almacen'] = array('titulo' => 'Almacen', 'class' => '', 'tipo' => 'text');
			$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'text');
			$arr_campos['hoja'] = array('titulo' => 'Hoja', 'class' => '', 'tipo' => 'text');
			$arr_campos['um'] = array('titulo' => 'UM', 'class' => '', 'tipo' => 'text');
			$arr_campos['stock_sap'] = array('titulo' => 'Stock SAP', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['stock_fisico'] = array('titulo' => 'Stock Fisico', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['stock_ajuste'] = array('titulo' => 'Stock Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['stock_dif'] = array('titulo' => 'Stock Dif', 'class' => 'text-center', 'tipo' => 'numero');
			$arr_campos['tipo'] = array('titulo' => 'Tipo Dif', 'class' => 'text-center', 'tipo' => 'texto');
			$arr_campos['glosa_ajuste'] = array('titulo' => 'Observacion', 'class' => '', 'tipo' => 'texto');
		}

		$arr_link_campos = array();
		$arr_link_sort   = array();
		$arr_img_orden   = array();

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = ($campo == $orden_campo) ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] == 'ASC') ? 'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down';
			$arr_campos[$campo]['img_orden'] = ($campo == $orden_campo) ? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>': '';
		}

		$tipo = ($tipo == 'detalle_hoja') ? 'hoja' : $tipo;
		$tipo = ($tipo == 'detalle_material') ? 'material' : $tipo;

		$data = array(
			'menu_modulo'       => array('menu' => $this->arr_menu, 'mod_selected' => $tipo),
			'reporte'           => $this->app_common->reporte($arr_campos, $datos_hoja),
			'tipo_reporte'      => $view,
			'nombre_reporte'    => $tipo,
			'filtro_dif'        => '',
			'titulo_modulo'     => 'Reportes',
			'combo_inventarios' => $this->inventario_model->get_combo_inventarios(),
			'inventario_activo' => $this->inventario_model->get_id_inventario_activo(),
			'id_inventario'     => $id_inventario,
		);

		$this->load->view('app_header', $data);
		$this->load->view('inventario/reporte', $data);
		$this->load->view('app_footer', $data);
	}



}
/* End of file inventario_reportes.php */
/* Location: ./application/controllers/inventario_reportes.php */