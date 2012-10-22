<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config2 extends CI_Controller {

	var $regs_por_pagina = 10;

	public function __construct()
	{
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		$this->load->model('acl_model');
		//$this->load->library('ORM_model');
	}

	public function index()
	{
		$this->listado();

	}

	private function _menu_configuracion($modelo)
	{
		$arr_menu = array(
				'usuario'        => array('url' => '/config2/listado/usuario',       'texto' => 'Usuarios'),
				'materiales'     => array('url' => '/config2/listado/materiales',     'texto' => 'Materiales'),
				'inventario'     => array('url' => '/config2/listado/inventario',     'texto' => 'Inventario Activo'),
				'tipo_ubicacion' => array('url' => '/config2/listado/tipo_ubicacion', 'texto' => 'Tipos Ubicacion'),
				'ubicaciones'    => array('url' => '/config2/listado/ubicacion_tipo_ubicacion', 'texto' => 'Ubicaciones'),
				'app'            => array('url' => '/config2/listado/app',            'texto' => 'Aplicaciones'),
				'rol'            => array('url' => '/config2/listado/rol',            'texto' => 'Roles'),
				'modulo'         => array('url' => '/config2/listado/modulo',         'texto' => 'Modulos'),
				'usuario_rol'    => array('url' => '/config2/listado/usuario_rol',    'texto' => 'Usuarios/Roles'),
			);

		$menu = '<ul>';
		foreach($arr_menu as $key => $val)
		{
			$selected = ($key == $modelo) ? ' class="selected"' : '';
			$menu .= '<li' . $selected . '>' . anchor($val['url'], $val['texto']) . '</li>';
		}
		$menu .= '</ul>';
		return $menu;
	}

	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuracion';
		$data['menu_app'] = $this->acl_model->menu_app();
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}


	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{

		dbg($this->db->field_data('acl_app'));
		dbg($this->db->list_fields('acl_app'));

		$modelos = new $nombre_modelo;

		$cantidad_registros = $modelos->count();
		$modelos->get_iterated($this->regs_por_pagina, $pag);
		//dbg($modelo);


		$data = array(
				'menu_configuracion' => $this->_menu_configuracion($nombre_modelo),
				'modelos'            => $this->_object_list_to_array($modelos),
				'fields'             => $modelos->fields,
				'links_paginas'      => $this->_crea_links_paginas($nombre_modelo, $filtro, $cantidad_registros),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'url_editar'         => site_url('config2/editar/' . $nombre_modelo . '/'),
				'url_borrar'         => site_url('config2/borrar/' . $nombre_modelo . '/'),
			);
		$this->_render_view('orm_listado', $data);
	}


	public function editar($nombre_modelo = '' , $id = NULL)
	{
		$modelo = new $nombre_modelo($id);
		//dbg($modelo);

		$modelo->validate();
		if (!$this->input->post('grabar') and !$this->input->post('borrar'))
		{
			$data = array(
				'menu_configuracion' => $this->_menu_configuracion($nombre_modelo),
				'arr_form'           => $this->_arr_form($modelo),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'modelo'             => $modelo,
				);
			$this->_render_view('orm_editar', $data);
		}
		else
		{
			//$modelo->recuperar_post();
			$this->session->set_flashdata('msg_alerta', ' !!!!!!!!!!');
			if ($this->input->post('grabar'))
			{
				$modelo->save();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' grabado correctamente');
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->delete();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' borrado correctamente');
			}
			redirect('config2/listado/' . $nombre_modelo);
		}

	}

	private function _object_list_to_array($mod)
	{
		$arr_list = array();
		$arr_has_one = array();

		foreach($mod->has_one as $key => $arr)
		{
			$arr_has_one[$arr['join_other_as'] . '_id'] = $key;
		}

		foreach($mod as $o)
		{
			$arr_reg = array();
			foreach($o->fields as $c)
			{
				if (array_key_exists($c, $arr_has_one))
				{
					$arr_reg[$arr_has_one[$c]] = $o->{$arr_has_one[$c]}->__toString();
				}
				else
				{
					$arr_reg[$c] = $o->$c;
				}
			}
			array_push($arr_list, $arr_reg);
		}

		return($arr_list);
	}

	private function _crea_links_paginas($modelo = '', $filtro = '_', $cantidad_registros = 0)
	{
		$this->load->library('pagination');
		$cfg_pagination = array(
					'uri_segment' => 5,
					'num_links'   => 5,
					'per_page'    => $this->regs_por_pagina,
					'total_rows'  => $cantidad_registros,
					'base_url'    => site_url('config2/listado/' . $modelo . '/' . $filtro . '/'),
					'first_link'  => 'Primero',
					'last_link'   => 'Ultimo',
					'next_link'   => '<img src="'. base_url() . 'img/ic_right.png" />',
					'prev_link'   => '<img src="'. base_url() . 'img/ic_left.png" />',
				);
		$this->pagination->initialize($cfg_pagination);
		return $this->pagination->create_links();
	}

	private function _arr_form($modelo)
	{
		$arr_form = '';
		$arr_has_one = array();

		foreach($modelo->has_one as $key => $arr)
		{
			$arr_has_one[$arr['join_other_as'] . '_id'] = $key;
		}

		foreach($modelo->fields as $campo)
		{
				if (array_key_exists($campo, $arr_has_one))
				{
					$arr_op = array();
					$arr_op[] = 'Seleccione una opcion...';
					foreach ($modelo->{$arr_has_one[$campo]} as $o)
					{
						$arr_op[$o->id] = $o->__toString();
					}
					$arr_form[$campo] = form_dropdown($campo, $arr_op, $modelo->$campo);
				}
				else
				{
					$arr_form[$campo] = form_input($campo, $modelo->$campo);
				}
		}
	return($arr_form);
	}

	private function _form_item($tipo = '', $nombre = '', $valor = '', $arr_valores = array())
	{
		if ($tipo == 'input')
		{
			return form_input($nombre, $valor);
		}
	}


}

/* End of file config2.php */
/* Location: ./application/controllers/config2.php */