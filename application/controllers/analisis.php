<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis extends CI_Controller {

	private $id_inventario = 0;


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('analisis');

	}



	/**
	 * accion por defecto del controlador
	 * @return [type]
	 */
	public function index() {
		$this->ajustes();
	}

	/**
	 * Despliega la página de ajustes de inventarios
	 * @param  integer $ocultar_regularizadas Oculta los regustros que están modificados
	 * @return nada
	 */
	public function ajustes($ocultar_regularizadas = 0)
	{
		$this->load->model('usuario');
		$this->load->model('inventario_model');
		$this->load->model('catalogo');

		// recupera el inventario activo
		$this->id_inventario = $this->inventario_model->get_id_inventario_activo();

		$nombre_inventario = $this->inventario_model->get_nombre_inventario($this->id_inventario);
		$datos_hoja        = $this->inventario_model->get_ajustes($this->id_inventario, $ocultar_regularizadas);

		if ($this->input->post('formulario') == 'ajustes')
		{
			foreach($datos_hoja as $reg)
			{
				$this->form_validation->set_rules('stock_ajuste_' . $reg['id'], 'cantidad', 'trim|numeric');
				$this->form_validation->set_rules('observacion_' . $reg['id'], 'observacion', 'trim');
			}			
		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('numeric', 'El valor del campo %s debe ser numerico');
		$this->form_validation->set_message('greater_than', 'El valor del campo %s debe ser positivo');

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'datos_hoja'            => $datos_hoja,
					'nombre_inventario'     => $nombre_inventario,
					'ocultar_regularizadas' => $ocultar_regularizadas,
					'msg_alerta'            => $this->session->flashdata('msg_alerta'),
					'menu_app'              => $this->acl_model->menu_app(),
				);
			
			$data['titulo_modulo'] = 'Ajustes de inventario';
			$this->load->view('app_header', $data);
			$this->load->view('ajustes', $data);
			$this->load->view('app_footer', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'ajustes')
			{
				$cant_modif = 0;
				foreach($datos_hoja as $reg)
				{
					if (((int) set_value('stock_ajuste_' . $reg['id']) != $reg['stock_ajuste']) or 
						(trim(set_value('observacion_' . $reg['id']))   != trim($reg['glosa_ajuste'])) )
					{
						$this->inventario_model->guardar_ajuste(
															$reg['id'], 
															set_value('stock_ajuste_' . $reg['id']), 
															trim(set_value('observacion_' . $reg['id'])), 
															date('Y-d-m H:i:s')
														);
						$cant_modif += 1;						
					}

				}

				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente' : ''));
			}
			redirect('analisis/ajustes/' . $ocultar_regularizadas . '/' . time());
		}

	}
}

/* End of file analisis.php */
/* Location: ./application/controllers/inventario.php */
