<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_config extends CI_Controller {

	public $llave_modulo = 'config2';
	private $arr_menu = array();


	public function __construct()
	{
		parent::__construct();

		$this->arr_menu = array(
			'auditor'         => array('url' => $this->uri->segment(1) . '/listado/auditor/', 'texto' => 'Auditores'),
			'familia'         => array('url' => $this->uri->segment(1) . '/listado/familia', 'texto' => 'Familias'),
			'catalogo'        => array('url' => $this->uri->segment(1) . '/listado/catalogo', 'texto' => 'Materiales'),
			'tipo_inventario' => array('url' => $this->uri->segment(1) . '/listado/tipo_inventario', 'texto' => 'Tipos de inventario'),
			'inventario'      => array('url' => $this->uri->segment(1) . '/listado/inventario', 'texto' => 'Inventarios'),
			//'detalle_inventario' => array('url' => $this->uri->segment(1) . '/listado/detalle_inventario', 'texto' => 'Detalle inventario'),
			'tipo_ubicacion'  => array('url' => $this->uri->segment(1) . '/listado/tipo_ubicacion', 'texto' => 'Tipos Ubicacion'),
			'ubicaciones'     => array('url' => $this->uri->segment(1) . '/ubicacion_tipo_ubicacion', 'texto' => 'Ubicaciones'),
			'centro'          => array('url' => $this->uri->segment(1) . '/listado/centro', 'texto' => 'Centros'),
			'almacen'         => array('url' => $this->uri->segment(1) . '/listado/almacen', 'texto' => 'Almacenes'),
			'unidad_medida'   => array('url' => $this->uri->segment(1) . '/listado/unidad_medida', 'texto' => 'Unidades de medida'),
			//'app'           => array('url' => $this->uri->segment(1) . '/listado/app', 'texto' => 'Aplicaciones'),
			//'rol'           => array('url' => $this->uri->segment(1) . '/listado/rol', 'texto' => 'Roles'),
			//'modulo'        => array('url' => $this->uri->segment(1) . '/listado/modulo', 'texto' => 'Modulos'),
			//'usuario'       => array('url' => $this->uri->segment(1) . '/listado/usuario', 'texto' => 'Usuarios'),
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
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
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
		$data['titulo_modulo'] = 'Configuracion';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega listado de los elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $filtro        Permite filtrar los registros a desplegar
	 * @param  string $pag           Numero de la pagina a desplegar
	 * @return none
	 */
	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$modelo = new $nombre_modelo;

		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;
		$modelo->set_model_filtro($filtro);

		$modelo->list_paginated($pag);

		$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'modelo'             => $modelo,
				'url_filtro'         => site_url($this->uri->segment(1) . '/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url($this->uri->segment(1) . '/editar/'  . $nombre_modelo . '/'),
			);
		$this->_render_view('ORM/orm_listado', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edita un elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $id            Identificador del modelo
	 * @return none
	 */
	public function editar($nombre_modelo = '' , $id = NULL)
	{
		$modelo = new $nombre_modelo;
		$modelo->find_id($id);

		if (!$modelo->valida_form())
		{
			$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'modelo'             => $modelo,
				'link_cancelar'      => site_url($this->uri->segment(1) . '/listado/' . $nombre_modelo),
				);
			$this->_render_view('ORM/orm_editar', $data);
		}
		else
		{
			$modelo->recuperar_post();
			if ($this->input->post('grabar'))
			{
				$modelo->grabar();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' (' . $modelo . ') grabado correctamente');
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->borrar();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' ('. $modelo . ') borrado correctamente');
			}
			redirect($this->uri->segment(1) . '/listado/' . $nombre_modelo);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Asociacion de ubicaciones con el tipo de ubicacion
	 *
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
			'base_url'    => site_url($this->uri->segment(1) . '/ubicacion_tipo_ubicacion'),
			'uri_segment' => 3,
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

			'first_link'  => 'Primero',
			'last_link'   => 'Ultimo',
			'prev_link'   => '<span class="glyphicon glyphicon-chevron-left"></span>',
			'next_link'   => '<span class="glyphicon glyphicon-chevron-right"></span>',
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
			redirect($this->uri->segment(1) . '/ubicacion_tipo_ubicacion/' . $pag);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string JSON con las ubicaciones libres
	 *
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con las ubicaciones libres
	 */
	public function get_json_ubicaciones_libres($tipo_inventario = '')
	{
		$this->load->model('ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_ubicaciones_libres($tipo_inventario);
		echo (json_encode($arr_ubic));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string JSON con los tipos de ubicaciones
	 *
	 * @param  string $tipo_inventario Tipo de inventario a buscar
	 * @return JSON con los tipos de ubicacion
	 */
	public function get_json_tipo_ubicacion($tipo_inventario = '')
	{
		$this->load->model('ubicacion_model');
		$arr_ubic = $this->ubicacion_model->get_combo_tipos_ubicacion($tipo_inventario);
		echo (json_encode($arr_ubic));
	}



}
/* End of file inventario_config.php */
/* Location: ./application/controllers/inventario_config.php */