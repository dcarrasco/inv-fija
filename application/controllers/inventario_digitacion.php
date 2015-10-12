<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_digitacion extends CI_Controller {

	private $id_inventario = 0;
	public $llave_modulo = 'inventario';


	public function __construct()
	{
		parent::__construct();
		$this->lang->load('inventario');
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
		$this->ingreso();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite digitar una hoja de detalle de inventario
	 *
	 * @param  integer $hoja Numero de la hoja a visualizar
	 * @return none
	 */
	public function ingreso($hoja = 0)
	{
		if ($hoja == 0 OR $hoja == "" or $hoja == NULL)
		{
			$hoja = 1;
		}

		// recupera el inventario activo
		$inventario = new Inventario;
		$this->id_inventario = $inventario->get_id_inventario_activo();
		$inventario->find_id($this->id_inventario);

		$nuevo_detalle_inventario = new Detalle_inventario;
		$nuevo_detalle_inventario->get_relation_fields();

		//$this->benchmark->mark('detalle_inventario_start');
		$detalle_inventario = new Detalle_inventario;
		$detalle_inventario->get_hoja($this->id_inventario, $hoja);
		//$this->benchmark->mark('detalle_inventario_end');

		$auditor = $detalle_inventario->get_id_auditor();
		$detalle_inventario->auditor = $auditor;

		$nombre_inventario = $inventario->nombre;
		$nombre_auditor    = $detalle_inventario->get_nombre_auditor();
		$nombre_digitador  = $detalle_inventario->get_nombre_digitador();

		$id_usuario_login  = $this->acl_model->get_id_usr();

		if ($this->input->post('formulario') == 'buscar')
		{
			$nuevo_detalle_inventario->set_validation_rules_field('hoja');
			$nuevo_detalle_inventario->set_validation_rules_field('auditor');
		}
		else if ($this->input->post('formulario') == 'inventario')
		{
			$nuevo_detalle_inventario->set_validation_rules_field('hoja');
			$nuevo_detalle_inventario->set_validation_rules_field('auditor');

			foreach($detalle_inventario->get_model_all() as $linea_detalle)
			{
				$this->form_validation->set_rules('stock_fisico_' . $linea_detalle->id, 'cantidad', 'trim|required|integer|greater_than[-1]');
				$this->form_validation->set_rules('hu_' . $linea_detalle->id, 'hu', 'trim');
				$this->form_validation->set_rules('observacion_' . $linea_detalle->id, 'observacion', 'trim');
			}
		}

		$this->app_common->form_validation_config();

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
				'nuevo_detalle_inventario' => $nuevo_detalle_inventario,
				'detalle_inventario' => $detalle_inventario,
				'hoja'               => $hoja,
				'nombre_inventario'  => $nombre_inventario,
				'nombre_digitador'   => $nombre_digitador,
				'nombre_auditor'     => $nombre_auditor,
				//'id_digitador'      => $digitador,
				'id_auditor'         => $auditor,
				'link_config'        => 'config',
				'link_reporte'       => 'reportes',
				'link_inventario'    => 'inventario',
				'link_hoja_ant'      => base_url($this->router->class . '/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . time()),
				'link_hoja_sig'      => base_url($this->router->class . '/ingreso/' . ($hoja + 1) . '/' . time()),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
			);

			app_render_view('inventario/inventario', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'inventario')
			{
				$cant_modif = 0;
				foreach($detalle_inventario->get_model_all() as $linea_detalle)
				{
					$linea_detalle->digitador          = $id_usuario_login;
					$linea_detalle->auditor            = set_value('auditor');
					$linea_detalle->stock_fisico       = set_value('stock_fisico_' . $linea_detalle->id);
					$linea_detalle->hu                 = set_value('hu_' . $linea_detalle->id);
					$linea_detalle->observacion        = set_value('observacion_'  . $linea_detalle->id);
					$linea_detalle->fecha_modificacion = date('Ymd H:i:s');
					$linea_detalle->grabar();
					$cant_modif += 1;
				}

				$msg_alerta = ($cant_modif > 0) ? sprintf($this->lang->line('inventario_digit_msg_save'), $cant_modif, $hoja) : '';
			}

			$this->session->set_flashdata('msg_alerta', $msg_alerta);
			redirect($this->router->class . '/ingreso/' . $this->input->post('hoja') . '/' . time());
		}

	}

	// --------------------------------------------------------------------


	/**
	 * Permite editar una linea de detalle agregada a un inventario
	 *
	 * @param  integer $hoja        Numero de la hoja a visualizar
	 * @param  integer $id_auditor  ID del auditor de la hoja
	 * @param  integer $id          ID del registro a editar
	 * @return none
	 */
	public function editar($hoja = 0, $id_auditor = 0, $id = null)
	{
		$detalle_inventario = new Detalle_inventario;

		$catalogo = new Catalogo;
		$arr_catalogo = array('' => 'Buscar y seleccionar material...');
		if ($this->input->post('catalogo'))
		{
			$catalogo->find_id($this->input->post('catalogo'));
			$arr_catalogo = array($this->input->post('catalogo') => $catalogo);
		}

		if ($id)
		{
			$detalle_inventario->find_id($id);
			$arr_catalogo = array($detalle_inventario->catalogo => $detalle_inventario->descripcion);
		}

		$detalle_inventario->get_relation_fields();

		// recupera el inventario activo
		$inventario = new Inventario;

		$detalle_inventario->hoja = $hoja;

		$nombre_digitador  = $detalle_inventario->get_nombre_digitador();
		$id_auditor = $detalle_inventario->get_id_auditor();

		$detalle_inventario->set_validation_rules_field('ubicacion');
		$detalle_inventario->set_validation_rules_field('hu');
		$detalle_inventario->set_validation_rules_field('catalogo');
		$detalle_inventario->set_validation_rules_field('lote');
		$detalle_inventario->set_validation_rules_field('centro');
		$detalle_inventario->set_validation_rules_field('almacen');
		$detalle_inventario->set_validation_rules_field('um');
		$detalle_inventario->set_validation_rules_field('stock_fisico');

		$this->app_common->form_validation_config();

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
				'detalle_inventario' => $detalle_inventario,
				'id'                 => $id,
				'hoja'               => $hoja,
				'arr_catalogo'       => $arr_catalogo,
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
			);

			app_render_view('inventario/inventario_editar', $data);
		}
		else
		{
			$nuevo_material = new Catalogo;
			$nuevo_material->find_id($this->input->post('catalogo'));

			$detalle_inventario->fill(array(
				'id'                 => (int) $id,
				'id_inventario'      => (int) $inventario->get_id_inventario_activo(),
				'hoja'               => (int) $hoja,
				'ubicacion'          => strtoupper($this->input->post('ubicacion')),
				'hu'                 => strtoupper($this->input->post('hu')),
				'catalogo'           => strtoupper($this->input->post('catalogo')),
				'descripcion'        => $nuevo_material->descripcion,
				'lote'               => strtoupper($this->input->post('lote')),
				'centro'             => strtoupper($this->input->post('centro')),
				'almacen'            => strtoupper($this->input->post('almacen')),
				'um'                 => strtoupper($this->input->post('um')),
				'stock_sap'          => 0,
				'stock_fisico'       => (int) $this->input->post('stock_fisico'),
				'digitador'          => (int) $this->acl_model->get_id_usr(),
				'auditor'            => (int) $id_auditor,
				'reg_nuevo'          => 'S',
				'fecha_modificacion' => date('Ymd H:i:s'),
				'observacion'        => $this->input->post('observacion'),
				'stock_ajuste'       => 0,
				'glosa_ajuste'       => '',
				'fecha_ajuste'       => null,
			));

			if ($this->input->post('accion') == 'agregar')
			{
				$detalle_inventario->grabar();
				$this->session->set_flashdata('msg_alerta', sprintf($this->lang->line('inventario_digit_msg_add'), $hoja));
			}
			else
			{
				$detalle_inventario->borrar();
				$this->session->set_flashdata('msg_alerta', sprintf($this->lang->line('inventario_digit_msg_delete'), $id, $hoja));
			}

			redirect($this->router->class . '/ingreso/' . $hoja . '/' . time());
		}

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega listado con materiales, en formato de formulario select
	 *
	 * @param  string $filtro Filtro de los materiales a desplegar
	 * @return none
	 */
	public function ajax_act_agr_materiales($filtro = '')
	{
		$material = new Catalogo;
		$arr_dropdown = $material->find('list', array('filtro' => $filtro));

		$options = '';
		foreach ($arr_dropdown as $key => $val)
		{
			$options .= '<option value="' . $key . '">' . $val . '</option>';
		}

		$this->output->set_content_type('text')->set_output($options);
	}


}
/* End of file inventario_digitacion.php */
/* Location: ./application/controllers/inventario_digitacion.php */
