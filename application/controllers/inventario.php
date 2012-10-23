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


	public function ingreso($hoja = 0, $digitador = 0, $auditor = 0)
	{
		$this->load->model('usuario');
		$this->load->model('inventario_model');
		$this->load->model('catalogo');

		// recupera el inventario activo
		$this->id_inventario = $this->inventario_model->get_id_inventario_activo();

		$nombre_inventario = $this->inventario_model->get_nombre_inventario($this->id_inventario);
		$datos_hoja        = $this->inventario_model->get_hoja($this->id_inventario, $hoja);
		$nombre_digitador  = $this->inventario_model->get_usuario_hoja($this->id_inventario, $hoja, 'DIG');
		$nombre_auditor    = $this->inventario_model->get_usuario_hoja($this->id_inventario, $hoja, 'AUD');

		if ($this->input->post('formulario') == 'buscar')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|greater_than[0]');
			$this->form_validation->set_rules('sel_digitador', 'Digitador', 'trim|required|greater_than[0]');
		}
		else if ($this->input->post('formulario') == 'inventario')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|numeric|greater_than[0]');
			$this->form_validation->set_rules('sel_digitador', 'Digitador', 'trim|required|numeric|greater_than[0]');
			foreach($datos_hoja as $reg)
			{
				$this->form_validation->set_rules('stock_fisico_' . $reg['id'], 'cantidad', 'trim|required|numeric|greater_than[-1]');
				$this->form_validation->set_rules('observacion_' . $reg['id'], 'observacion', 'trim');
			}			
		}
		else if ($this->input->post('formulario')=='agregar')
		{
			$this->form_validation->set_rules('sel_hoja', 'Hoja', 'trim|required|numeric');
			$this->form_validation->set_rules('sel_auditor', 'Auditor', 'trim|required|greater_than[0]');
			$this->form_validation->set_rules('sel_digitador', 'Digitador', 'trim|required|greater_than[0]');
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
					'datos_hoja'        => $datos_hoja,
					'hoja'              => $hoja,
					'nombre_inventario' => $nombre_inventario,
					'nombre_digitador'  => $nombre_digitador,
					'nombre_auditor'    => $nombre_auditor,
					'id_digitador'      => $digitador,
					'id_auditor'        => $auditor,
					'combo_auditores'   => $this->usuario->get_combo_usuarios('AUD'),
					'combo_digitadores' => $this->usuario->get_combo_usuarios('DIG'),
					'link_config'       => 'config',
					'link_reporte'      => 'reportes',
					'link_inventario'   => 'inventario',
					'link_hoja_ant'     => 'inventario/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . $digitador . '/' . $auditor . '/' . time(),
					'link_hoja_sig'     => 'inventario/ingreso/' . ($hoja + 1) . '/' . $digitador . '/' . $auditor . '/' . time(),
					'msg_alerta'        => $this->session->flashdata('msg_alerta'),
					'menu_app'          => $this->acl_model->menu_app(),
				);
			
			$this->load->view('inventario', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'inventario')
			{
				$cant_modif = 0;
				foreach($datos_hoja as $reg)
				{
					$this->inventario_model->guardar(
														$reg['id'], 
														$this->id_inventario,
														$hoja, 
														set_value('sel_digitador'), 
														set_value('sel_auditor'), 
														$reg['ubicacion'], 
														$reg['catalogo'], 
														$reg['descripcion'], 
														$reg['lote'], 
														$reg['centro'], 
														$reg['almacen'], 
														$reg['um'], 
														$reg['stock_sap'], 
														set_value('stock_fisico_' . $reg['id']), 
														set_value('observacion_' . $reg['id']), 
														date('Y-d-m H:i:s'),
														$reg['reg_nuevo']
													);
					$cant_modif += 1;
				}

				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente en hoja ' . $hoja : ''));
			}
			else if ($this->input->post('formulario') == 'agregar')
			{
				if ($this->input->post('accion') == 'borrar')
				{
					$this->inventario_model->borrar($this->input->post('agr_id'));
					$this->session->set_flashdata('msg_alerta', 'Linea (id=' . $this->input->post('agr_id') . ') borrada correctamente en hoja '. $hoja);					
				}
				else
				{
					$this->inventario_model->guardar(
														$this->input->post('agr_id'), 
														$this->id_inventario,
														$hoja, 
														set_value('sel_digitador'), 
														set_value('sel_auditor'), 
														$this->input->post('agr_ubicacion'), 
														$this->input->post('agr_material'), 
														$this->catalogo_model->get_descripcion($this->input->post('agr_material')), 
														$this->input->post('agr_lote'), 
														$this->input->post('agr_centro'), 
														$this->input->post('agr_almacen'), 
														$this->input->post('agr_um'), 
														0,
														$this->input->post('agr_cantidad'), 
														$this->input->post('agr_observacion'), 
														date('Y-d-m H:i:s'),
														'S'
													);
					$this->session->set_flashdata('msg_alerta', 'Linea agregada correctamente en hoja '. $hoja);					
				}
			}
			redirect('inventario/ingreso/' . $this->input->post('sel_hoja') . '/' . $this->input->post('sel_digitador') . '/' . $this->input->post('sel_auditor') . '/' . time());
		}

	}

	public function ajax_act_agr_materiales($filtro = '')
	{
		$this->load->model('catalogo_model');
		$datos_combo = $this->catalogo_model->get_combo_catalogo($filtro);
		$options = '';
		foreach ($datos_combo as $key => $val)
		{
			$options .= '<option value="' . $key . '">' . $val . '</option>';
		}
		echo($options);
	}



}

/* End of file inventario.php */
/* Location: ./application/controllers/inventario.php */
