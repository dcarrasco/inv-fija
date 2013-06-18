<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config extends CI_Controller {

	private $arr_menu = array(
				'auditor'        => array('url' => '/config2/listado/auditor', 'texto' => 'Auditores'),
				'familia'        => array('url' => '/config2/listado/familia', 'texto' => 'Familias'),
				'catalogo'       => array('url' => '/config2/listado/catalogo', 'texto' => 'Materiales'),
				'tipo_inventario' => array('url' => '/config2/listado/tipo_inventario', 'texto' => 'Tipos de inventario'),
				'inv_activo'     => array('url' => '/config2/listado/inv_activo', 'texto' => 'Inventario Activo'),
				//'detalle_inventario' => array('url' => '/config2/listado/detalle_inventario', 'texto' => 'Detalle inventario'),
				'tipo_ubicacion' => array('url' => '/config2/listado/tipo_ubicacion', 'texto' => 'Tipos Ubicacion'),
				'ubicaciones'    => array('url' => '/config/ubicacion_tipo_ubicacion', 'texto' => 'Ubicaciones'),
				'centro'         => array('url' => '/config2/listado/centro', 'texto' => 'Centros'),
				'almacen'        => array('url' => '/config2/listado/almacen', 'texto' => 'Almacenes'),
				'unidad_medida'  => array('url' => '/config2/listado/unidad_medida', 'texto' => 'Unidades de medida'),
				//'app'            => array('url' => '/config2/listado/app', 'texto' => 'Aplicaciones'),
				//'rol'            => array('url' => '/config2/listado/rol', 'texto' => 'Roles'),
				//'modulo'         => array('url' => '/config2/listado/modulo', 'texto' => 'Modulos'),
				//'usuario'        => array('url' => '/config2/listado/usuario', 'texto' => 'Usuarios'),
			);


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('config2');

	}


	public function index() {
		$this->usuarios();
	}


	public function act_precio_materiales()
	{
		$this->load->model('catalogo_model');
		$cant_regs = $this->catalogo_model->actualiza_precios();
		$this->session->set_flashdata('msg_alerta',  'Actualizacion terminada (' . $cant_regs . ' precios actualizados correctamente).');
		redirect('config/materiales');
	}



	/**
	 * Asociacion de ubicaciones con el tipo de ubicacion
	 * @param  integer $pag Numero de pagina a desplegar
	 * @return nada
	 */
	public function ubicacion_tipo_ubicacion($pag = 0)
	{
		$this->load->model('inventario_model');
		$this->load->model('ubicacion_model');

		$this->load->library('pagination');
		$limite_por_pagina = 15;
		$config_pagination = array(
									'total_rows'  => $this->ubicacion_model->total_ubicacion_tipo_ubicacion(),
									'per_page'    => $limite_por_pagina,
									'base_url'    => site_url('config/ubicacion_tipo_ubicacion'),
									'uri_segment' => 3,
									'num_links'   => 5,

									'full_tag_open'   => '<ul>',
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

									'first_link'  => 'Primero',
									'last_link'   => 'Ultimo',
									'prev_link'   => '&laquo;',
									'next_link'   => '&raquo;',
								);
		$this->pagination->initialize($config_pagination);


		$datos_hoja = $this->ubicacion_model->get_ubicacion_tipo_ubicacion($limite_por_pagina, $pag);

		if ($this->input->post('formulario')=='editar')
		{
			foreach($datos_hoja as $reg)
			{
				$this->form_validation->set_rules($reg['id'].'-tipo_inventario', 'Tipo Inventario', 'trim|required');
				$this->form_validation->set_rules($reg['id'].'-tipo_ubicacion',  'Tipo Ubicacion', 'trim|required');
				$this->form_validation->set_rules($reg['id'].'-ubicacion',  'Ubicacion', 'trim|required');
			}
		}
		else if ($this->input->post('formulario')=='agregar')
		{
			$this->form_validation->set_rules('agr-tipo_inventario', 'Tipo de inventario', 'trim|required');
			$this->form_validation->set_rules('agr-ubicacion[]', 'Ubicaciones', 'trim|required');
			$this->form_validation->set_rules('agr-tipo_ubicacion', 'Tipo de ubicacion', 'trim|required');
		}
		else if ($this->input->post('formulario')=='borrar')
		{
			$this->form_validation->set_rules('id_borrar', '', 'trim|required');
		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');


		if ($this->form_validation->run() == FALSE)
		{
			$arr_combo_tipo_ubic = array();
			foreach($datos_hoja as $reg)
			{
				if (!array_key_exists($reg['tipo_inventario'], $arr_combo_tipo_ubic))
				{
					$arr_combo_tipo_ubic[$reg['tipo_inventario']] = array();
				}
			}
			foreach($arr_combo_tipo_ubic as $key => $val)
			{
				$arr_combo_tipo_ubic[$key] = $this->ubicacion_model->get_combo_tipos_ubicacion($key);
			}


			$data = array(
					'menu_modulo'            => array('menu' => $this->arr_menu, 'mod_selected' => 'ubicaciones'),
					'datos_hoja'             => $datos_hoja,
					'combo_tipos_inventario' => $this->inventario_model->get_combo_tipos_inventario(),
					'combo_tipos_ubicacion'  => $arr_combo_tipo_ubic,
					'msg_alerta'             => $this->session->flashdata('msg_alerta'),
					'links_paginas'          => $this->pagination->create_links(),
				);

			$this->_render_view('ubicacion_tipo_ubicacion', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'editar')
			{
				$cant_modif = 0;

				foreach($datos_hoja as $reg)
				{
					if ((set_value($reg['id'].'-tipo_inventario') != $reg['tipo_inventario']) or
						(set_value($reg['id'].'-ubicacion')       != $reg['ubicacion']) or
						(set_value($reg['id'].'-tipo_ubicacion')  != $reg['id_tipo_ubicacion']))
					{
						$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion($reg['id'], set_value($reg['id'].'-tipo_inventario'), set_value($reg['id'].'-ubicacion'), set_value($reg['id'].'-tipo_ubicacion'));
						$cant_modif += 1;
					}
				}
				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' inventario(s) modificados correctamente' : ''));
			}
			else if ($this->input->post('formulario') == 'agregar')
			{
				$count = 0;
				foreach($this->input->post('agr-ubicacion') as $val)
				{
					$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(0, set_value('agr-tipo_inventario'), $val, set_value('agr-tipo_ubicacion'));
					$count += 1;
				}
				$this->session->set_flashdata('msg_alerta', 'Se agregaron ' . $count . ' ubicaciones correctamente');
			}
			else if ($this->input->post('formulario') == 'borrar')
			{
				$this->ubicacion_model->borrar_ubicacion_tipo_ubicacion(set_value('id_borrar'));
				$this->session->set_flashdata('msg_alerta', 'Registro (id=' . set_value('id_borrar') . ') borrado correctamente');
			}
			redirect('config/ubicacion_tipo_ubicacion/' . $pag);
		}
	}


	/**
	 * Devuelve string JSON con las ubicaciones libres
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con las ubicaciones libres
	 */
	public function get_json_ubicaciones_libres($tipo_inventario = '')
	{
		$this->load->model('ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_ubicaciones_libres($tipo_inventario);
		echo (json_encode($arr_ubic));
	}

	/**
	 * Devuelve string JSON con los tipos de ubicaciones
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con los tipos de ubicacion
	 */
	public function get_json_tipo_ubicacion($tipo_inventario = '')
	{
		$this->load->model('ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_combo_tipos_ubicacion($tipo_inventario);
		echo (json_encode($arr_ubic));
	}




	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuracion';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}


}

/* End of file config.php */
/* Location: ./application/controllers/config.php */
