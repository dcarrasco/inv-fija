<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario extends CI_Controller {

	private $id_inventario = 0;


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('inventario');
	}


	public function index() {
		$this->ingreso();
	}


	public function ingreso($hoja = 0, $auditor = 0)
	{
		//$this->load->model('usuario');

		$obj_auditor = new Auditor;

		// recupera el inventario activo
		$inv_activo = new Inv_activo;
		$this->id_inventario = $inv_activo->get_id_inventario_activo();
		$inv_activo->get_id($this->id_inventario);

		$detalle_inventario = new Detalle_inventario;
		$detalle_inventario->get_hoja($this->id_inventario, $hoja);
		//dbg($detalle_inventario);

		$nombre_inventario = $inv_activo->nombre;
		$nombre_auditor    = $detalle_inventario->get_nombre_auditor();
		$nombre_digitador  = $detalle_inventario->get_nombre_digitador();

		$id_usuario_login  = $this->acl_model->get_id_usr();

		if ($this->input->post('formulario') == 'buscar')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|greater_than[0]');
		}
		else if ($this->input->post('formulario') == 'inventario')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|numeric|greater_than[0]');
			foreach($detalle_inventario->get_model_all() as $linea_detalle)
			{
				$this->form_validation->set_rules('stock_fisico_' . $linea_detalle->id, 'cantidad', 'trim|required|numeric|greater_than[-1]');
				$this->form_validation->set_rules('observacion_' . $linea_detalle->id, 'observacion', 'trim');
			}
		}
		else if ($this->input->post('formulario')=='agregar')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|greater_than[0]');
			$this->form_validation->set_rules('agr_ubicacion', 'Ubicacion', 'trim|required');
			$this->form_validation->set_rules('agr_material', 'Material', 'trim|required');
			$this->form_validation->set_rules('agr_lote', 'Lote', 'trim|required');
			$this->form_validation->set_rules('agr_centro', 'Centro', 'trim|required');
			$this->form_validation->set_rules('agr_almacen', 'Almacen', 'trim|required');
			$this->form_validation->set_rules('agr_um', 'Unidad de Medida', 'trim|required');
			$this->form_validation->set_rules('agr_cantidad', 'Cantidad', 'trim|required|numeric|greater_than[-1]');

		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('numeric', 'El valor del campo %s debe ser numerico');
		$this->form_validation->set_message('greater_than', 'El valor del campo %s debe ser positivo');

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'detalle_inventario' => $detalle_inventario,
					'hoja'               => $hoja,
					'nombre_inventario'  => $nombre_inventario,
					'nombre_digitador'   => $nombre_digitador,
					'nombre_auditor'     => $nombre_auditor,
					//'id_digitador'      => $digitador,
					'id_auditor'         => $auditor,
					'combo_auditores'    => $obj_auditor->get_combo(),
					'link_config'        => 'config',
					'link_reporte'       => 'reportes',
					'link_inventario'    => 'inventario',
					'link_hoja_ant'      => 'inventario/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . $auditor . '/' . time(),
					'link_hoja_sig'      => 'inventario/ingreso/' . ($hoja + 1) . '/' . $auditor . '/' . time(),
					'msg_alerta'         => $this->session->flashdata('msg_alerta'),
					'menu_app'           => $this->acl_model->menu_app(),
				);

			$this->_render_view('inventario', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'inventario')
			{
				$cant_modif = 0;
				foreach($detalle_inventario->get_model_all() as $linea_detalle)
				{
					$linea_detalle->auditor            = set_value('sel_auditor');
					$linea_detalle->stock_fisico       = set_value('stock_fisico_' . $linea_detalle->id);
					$linea_detalle->observacion        = set_value('observacion_'  . $linea_detalle->id);
					$linea_detalle->fecha_modificacion = date('Y-d-m H:i:s');
					//dbg($linea_detalle);
					$linea_detalle->grabar();
					$cant_modif += 1;
				}

				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente en hoja ' . $hoja : ''));
			}
			else if ($this->input->post('formulario') == 'agregar')
			{
				if ($this->input->post('accion') == 'borrar')
				{
					$nuevo_detalle_inventario = new Detalle_inventario;
					$nuevo_detalle_inventario->get_id($this->input->post('agr_id'));
					$nuevo_detalle_inventario->borrar();
					$this->session->set_flashdata('msg_alerta', 'Linea (id=' . $this->input->post('agr_id') . ') borrada correctamente en hoja '. $hoja);
				}
				else
				{
					$nuevo_detalle_inventario = new Detalle_inventario;
					$nuevo_material = new Catalogo;
					$nuevo_material->get_id($this->input->post('agr_material'));

					$nuevo_detalle_inventario->get_from_array(array(
														'id'            => $this->input->post('agr_id'),
														'id_inventario' => $this->id_inventario,
														'hoja'          => $hoja,
														'digitador'     => $id_usuario_login,
														'auditor'       => set_value('sel_auditor'),
														'ubicacion'     => $this->input->post('agr_ubicacion'),
														'catalogo'      => $this->input->post('agr_material'),
														'descripcion'   => $nuevo_material->descripcion,
														'lote'          => $this->input->post('agr_lote'),
														'centro'        => $this->input->post('agr_centro'),
														'almacen'       => $this->input->post('agr_almacen'),
														'um'            => $this->input->post('agr_um'),
														'stock_sap'     => 0,
														'stock_fisico'  => $this->input->post('agr_cantidad'),
														'observacion'   => $this->input->post('agr_observacion'),
														'fecha_modificacion' => date('Y-d-m H:i:s'),
														'reg_nuevo'     => 'S',
												));
					$nuevo_detalle_inventario->grabar();
					$this->session->set_flashdata('msg_alerta', 'Linea agregada correctamente en hoja '. $hoja);
				}
			}
			redirect('inventario/ingreso/' . $this->input->post('sel_hoja') . '/' . $this->input->post('sel_auditor') . '/' . time());
		}

	}

	public function ajax_act_agr_materiales($filtro = '')
	{
		$material = new catalogo;
		$material->get_combo(false, $filtro);

		$options = '';
		foreach ($material->get_model_all() as $o)
		{
			$options .= '<option value="' . $o->catalogo . '">' . $o . '</option>';
		}
		echo($options);
	}


	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Ingreso de Inventario ('.$data['nombre_inventario'].')';
		$data['menu_app'] = $this->acl_model->menu_app();
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}



}

/* End of file inventario.php */
/* Location: ./application/controllers/inventario.php */
