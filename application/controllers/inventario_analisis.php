<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_analisis extends CI_Controller {

	private $id_inventario = 0;
	private $nombre_inventario = '';
	private $arr_menu = array();



	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('analisis');

		if (ENVIRONMENT != 'production')
		{
			//$this->output->enable_profiler(TRUE);
		}

		$this->arr_menu = array(
			'ajustes'            => array('url' => $this->uri->segment(1) . '/ajustes', 'texto' => 'Ajustes de inventario'),
			'sube_stock'         => array('url' => $this->uri->segment(1) . '/sube_stock', 'texto' => 'Subir Stock'),
			'imprime_inventario' => array('url' => $this->uri->segment(1) . '/imprime_inventario', 'texto' => 'Imprimir Hojas'),
		);

		$inventario_activo = new Inventario;
		$this->id_inventario = $inventario_activo->get_id_inventario_activo();
		$inventario_activo->find_id($this->id_inventario);

		$this->nombre_inventario = $inventario_activo->nombre;
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
		$this->ajustes();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega la página de ajustes de inventarios
	 *
	 * @param  integer $ocultar_regularizadas Oculta los regustros que están modificados
	 * @param  integer $pag                   Página de ajustes a desplegar
	 * @return nada
	 */
	public function ajustes($ocultar_regularizadas = 0, $pag = 0)
	{

		// recupera el detalle de registros con diferencias
		$detalle_ajustes = new Detalle_inventario;
		$links_paginas = $detalle_ajustes->get_ajustes($this->id_inventario, $ocultar_regularizadas, $pag);

		if ($this->input->post('formulario') == 'ajustes')
		{
			foreach($detalle_ajustes->get_model_all() as $detalle)
			{
				$this->form_validation->set_rules('stock_ajuste_' . $detalle->id, 'cantidad', 'trim|integer');
				$this->form_validation->set_rules('observacion_'  . $detalle->id, 'observacion', 'trim');
			}
		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('integer', 'El valor del campo %s debe ser un numero entero');
		$this->form_validation->set_message('greater_than', 'El valor del campo %s debe ser positivo');

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'detalle_ajustes'       => $detalle_ajustes,
					'ocultar_regularizadas' => $ocultar_regularizadas,
					'pag'                   => $pag,
					'links_paginas'         => $links_paginas,
					'msg_alerta'            => $this->session->flashdata('msg_alerta'),
				);

			$this->_render_view('ajustes', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'ajustes')
			{
				$cant_modif = 0;
				foreach($detalle_ajustes->get_model_all() as $detalle)
				{
					if (((int) set_value('stock_ajuste_' . $detalle->id)  != $detalle->stock_ajuste) or
						(trim(set_value('observacion_' . $detalle->id))   != trim($detalle->glosa_ajuste)) )
					{
						$detalle->stock_ajuste = (int) set_value('stock_ajuste_' . $detalle->id);
						$detalle->glosa_ajuste = trim(set_value('observacion_' . $detalle->id));
						$detalle->fecha_ajuste = date('Ymd H:i:s');
						$detalle->grabar();
						$cant_modif += 1;
					}

				}
				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente' : ''));
			}
			redirect($this->uri->segment(1) . '/ajustes/' . $ocultar_regularizadas . '/' . $pag . '/' . time());
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Permite subir un archivo con el stock a cargar a un inventario
	 *
	 * @param  none
	 * @return none
	 */
	public function sube_stock()
	{

		$upload_error = '';
		$script_carga = '';
		$regs_OK      = 0;
		$regs_error   = 0;
		$msj_error    = '';
		$show_script_carga = FALSE;

		if ($this->input->post('formulario') == 'upload')
		{

			if ($this->input->post('password') == 'logistica2012')
			{
				$upload_config = array(
					'upload_path'   => './upload/',
					'allowed_types' => 'txt|cvs',
					'max_size'      => '2000',
					'file_name'     => 'sube_stock.txt',
					'overwrite'     => TRUE,
					);
				$this->load->library('upload', $upload_config);

				if (! $this->upload->do_upload('upload_file'))
				{
					$upload_error = '<br><div class="error round">' . $this->upload->display_errors() . '</div>';
				}
				else
				{
					$upload_data = $this->upload->data();
					$archivo_cargado = $upload_data['full_path'];

					$inventario = new Inventario;
					$inventario->find_id($this->id_inventario);
					$inventario->borrar_detalle_inventario();
					$res_procesa_archivo = $inventario->cargar_datos_archivo($archivo_cargado);

					$script_carga = $res_procesa_archivo['script'];
					$show_script_carga = TRUE;
					$regs_OK      = $res_procesa_archivo['regs_OK'];
					$regs_error   = $res_procesa_archivo['regs_error'];
					$msj_error    = ($regs_error > 0) ? '<br><div class="error round">' . $res_procesa_archivo['msj_termino'] . '</div>' : '';
				}
			}
		}

		$data = array(
				'inventario_id'     => $this->id_inventario,
				'inventario_nombre' => $this->nombre_inventario,
				'upload_error'      => $upload_error,
				'script_carga'      => $script_carga,
				'show_script_carga' => $show_script_carga,
				'regs_OK'           => $regs_OK,
				'regs_error'        => $regs_error,
				'msj_error'         => $msj_error,
				'link_config'       => 'config',
				'link_reporte'      => 'reportes',
				'link_inventario'   => 'inventario',
				'msg_alerta'        => $this->session->flashdata('msg_alerta'),
			);

		$this->_render_view('sube_stock', $data);

	}

	// --------------------------------------------------------------------

	/**
	 * Recibe linea de detalle de inventario por post, y lo graba
	 *
	 * @param  none
	 * @return none
	 */
	public function inserta_linea_archivo()
	{
		$detalle = new Detalle_inventario;
		$detalle->id_inventario      = $this->input->post('id_inv');
		$detalle->hoja               = $this->input->post('hoja');
		$detalle->ubicacion          = $this->input->post('ubic');
		$detalle->hu                 = $this->input->post('hu');
		$detalle->catalogo           = $this->input->post('cat');
		$detalle->descripcion        = $this->input->post('desc');
		$detalle->lote               = $this->input->post('lote');
		$detalle->centro             = $this->input->post('cen');
		$detalle->almacen            = $this->input->post('alm');
		$detalle->um                 = $this->input->post('um');
		$detalle->stock_sap          = $this->input->post('ssap');
		$detalle->stock_fisico       = $this->input->post('sfis');
		$detalle->digitador          = $this->input->post('aud');
		$detalle->auditor            = $this->input->post('dig');
		$detalle->reg_nuevo	         = $this->input->post('nvo');
		$detalle->fecha_modificacion = $this->input->post('fec');
		$detalle->observacion        = $this->input->post('obs');
		$detalle->stock_ajuste       = $this->input->post('saju');
		$detalle->glosa_ajuste       = $this->input->post('gaju');
		$detalle->grabar();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite imprimir el detalle del inventario
	 *
	 * @param  integer $id_inventario Identificador del inventario a imprimir
	 * @return none
	 */
	public function imprime_inventario($id_inventario = 0)
	{
		$inventario = new Inventario;
		$inventario->find_id($this->id_inventario);

		$this->form_validation->set_rules('pag_desde', 'Pagina Desde', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('pag_hasta', 'Pagina Hasta', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('oculta_stock_sap', 'Oculta Stock SAP', '');
		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para [%s]');
		$this->form_validation->set_message('greater_than', 'El valor del campo [%s] debe ser mayor o igual a 1');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'inventario_id'     => $this->id_inventario,
					'inventario_nombre' => $this->nombre_inventario,
					'max_hoja'          => $inventario->get_max_hoja_inventario(),
					'link_config'       => 'config',
					'link_reporte'      => 'reportes',
					'link_inventario'   => 'inventario',
					'msg_alerta'        => $this->session->flashdata('msg_alerta'),
				);

			$this->_render_view('imprime_inventario', $data);
		}
		else
		{
			$oculta_stock_sap = (set_value('oculta_stock_sap') == 'oculta_stock_sap') ? 1 : 0;
			redirect($this->uri->segment(1) . "/imprime_hojas/" . set_value('pag_desde') . '/' . set_value('pag_hasta') . '/' . $oculta_stock_sap . '/' . time());
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Genera las hojas a imprimir
	 *
	 * @param  integer $hoja_desde       Hoja inicial a imprimir
	 * @param  integer $hoja_hasta       Hoja final a imprimir
	 * @param  integer $oculta_stock_sap Permite ocultar la columna con el stock de SAP
	 * @return none
	 */
	public function imprime_hojas($hoja_desde = 1, $hoja_hasta = 1, $oculta_stock_sap = 0)
	{
		$hoja_desde = ($hoja_desde < 1) ? 1 : $hoja_desde;
		$hoja_hasta = ($hoja_hasta < $hoja_desde) ? $hoja_desde : $hoja_hasta;

		$this->load->view('inventario/inventario_print_head');

		for($hoja = $hoja_desde; $hoja <= $hoja_hasta; $hoja++)
		{
			$detalle = new Detalle_inventario;
			$detalle->find('all', array('conditions' => array('id_inventario' => $this->id_inventario, 'hoja' => $hoja)));
			$data = array(
					'datos_hoja'        => $detalle->get_model_all(),
					'oculta_stock_sap'  => $oculta_stock_sap,
					'hoja'              => $hoja,
					'nombre_inventario' => $this->nombre_inventario,
				);

			$this->load->view('inventario/inventario_print_body', $data);
		}

		$this->load->view('inventario/inventario_print_footer');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera las vistas para los métodos de este controlador
	 *
	 * @param  string $vista Nombre de la vista a desplegar
	 * @param  array  $data  Arreglo con las variables a pasar a la vista
	 * @return none
	 */
	private function _render_view($vista = '', $data = array())
	{
		$data['menu_modulo'] = array('menu' => $this->arr_menu, 'mod_selected' => $vista);
		$this->load->view('app_header', $data);
		$this->load->view('inventario/' . $vista, $data);
		$this->load->view('app_footer', $data);
	}



}
/* End of file inventario_analisis.php */
/* Location: ./application/controllers/inventario_analisis.php */
