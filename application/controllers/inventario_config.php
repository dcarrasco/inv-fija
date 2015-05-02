<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_config extends ORM_Controller {

	public $llave_modulo  = 'config2';
	public $titulo_modulo = 'Configuracion';



	public function __construct()
	{
		parent::__construct();

		$this->arr_menu = array(
			'auditor'         => array('url' => $this->router->class . '/listado/auditor/', 'texto' => 'Auditores'),
			'familia'         => array('url' => $this->router->class . '/listado/familia', 'texto' => 'Familias'),
			'catalogo'        => array('url' => $this->router->class . '/listado/catalogo', 'texto' => 'Materiales'),
			'tipo_inventario' => array('url' => $this->router->class . '/listado/tipo_inventario', 'texto' => 'Tipos de inventario'),
			'inventario'      => array('url' => $this->router->class . '/listado/inventario', 'texto' => 'Inventarios'),
			//'detalle_inventario' => array('url' => $this->router->class . '/listado/detalle_inventario', 'texto' => 'Detalle inventario'),
			'tipo_ubicacion'  => array('url' => $this->router->class . '/listado/tipo_ubicacion', 'texto' => 'Tipos Ubicacion'),
			'ubicaciones'     => array('url' => $this->router->class . '/ubicacion_tipo_ubicacion', 'texto' => 'Ubicaciones'),
			'centro'          => array('url' => $this->router->class . '/listado/centro', 'texto' => 'Centros'),
			'almacen'         => array('url' => $this->router->class . '/listado/almacen', 'texto' => 'Almacenes'),
			'unidad_medida'   => array('url' => $this->router->class . '/listado/unidad_medida', 'texto' => 'Unidades de medida'),
			//'app'           => array('url' => $this->router->class . '/listado/app', 'texto' => 'Aplicaciones'),
			//'rol'           => array('url' => $this->router->class . '/listado/rol', 'texto' => 'Roles'),
			//'modulo'        => array('url' => $this->router->class . '/listado/modulo', 'texto' => 'Modulos'),
			//'usuario'       => array('url' => $this->router->class . '/listado/usuario', 'texto' => 'Usuarios'),
		);
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
		$tipo_inventario = new Tipo_inventario;
		$this->load->model('ubicacion_model');

		$this->load->library('pagination');
		$limite_por_pagina = 15;
		$config_pagination = array(
			'total_rows'  => $this->ubicacion_model->total_ubicacion_tipo_ubicacion(),
			'per_page'    => $limite_por_pagina,
			'base_url'    => site_url($this->router->class . '/ubicacion_tipo_ubicacion'),
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

		$this->app_common->form_validation_config();

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
				'combo_tipos_inventario' => $tipo_inventario->find('list'),
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
						$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
							$reg['id'],
							set_value($reg['id'] . '-tipo_inventario'),
							set_value($reg['id'] . '-ubicacion'),
							set_value($reg['id'] . '-tipo_ubicacion')
						);
						$cant_modif += 1;
					}
				}
				$this->session->set_flashdata('msg_alerta', (
					($cant_modif > 0)
						? $cant_modif . ' inventario(s) modificados correctamente'
						: ''
				));
			}
			else if ($this->input->post('formulario') == 'agregar')
			{
				$count = 0;
				foreach($this->input->post('agr-ubicacion') as $val)
				{
					$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
						0,
						set_value('agr-tipo_inventario'),
						$val,
						set_value('agr-tipo_ubicacion')
					);
					$count += 1;
				}
				$this->session->set_flashdata('msg_alerta', 'Se agregaron ' . $count . ' ubicaciones correctamente');
			}
			else if ($this->input->post('formulario') == 'borrar')
			{
				$this->ubicacion_model->borrar_ubicacion_tipo_ubicacion(set_value('id_borrar'));
				$this->session->set_flashdata('msg_alerta', 'Registro (id=' . set_value('id_borrar') . ') borrado correctamente');
			}

			redirect($this->router->class . '/ubicacion_tipo_ubicacion/' . $pag);
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

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arr_ubic));
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

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($arr_ubic));
	}



}
/* End of file inventario_config.php */
/* Location: ./application/controllers/inventario_config.php */