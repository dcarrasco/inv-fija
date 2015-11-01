<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Controller Configuracion Inventario
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_config extends ORM_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo  = 'config2';

	/**
	 * Titulo del modulo
	 *
	 * @var  string
	 */
	public $titulo_modulo = 'Configuracion';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
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
	 * @param  integer $pagina Numero de pagina a desplegar
	 * @return nada
	 */
	public function ubicacion_tipo_ubicacion($pagina = 0)
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

		$datos_hoja = $this->ubicacion_model->get_ubicacion_tipo_ubicacion($limite_por_pagina, $pagina);

		if ($this->input->post('formulario') === 'editar')
		{
			foreach($datos_hoja as $registro)
			{
				$this->form_validation->set_rules($registro['id'].'-tipo_inventario', 'Tipo Inventario', 'trim|required');
				$this->form_validation->set_rules($registro['id'].'-tipo_ubicacion', 'Tipo Ubicacion', 'trim|required');
				$this->form_validation->set_rules($registro['id'].'-ubicacion', 'Ubicacion', 'trim|required');
			}
		}
		else if ($this->input->post('formulario') === 'agregar')
		{
			$this->form_validation->set_rules('agr-tipo_inventario', 'Tipo de inventario', 'trim|required');
			$this->form_validation->set_rules('agr-ubicacion[]', 'Ubicaciones', 'trim|required');
			$this->form_validation->set_rules('agr-tipo_ubicacion', 'Tipo de ubicacion', 'trim|required');
		}
		else if ($this->input->post('formulario') === 'borrar')
		{
			$this->form_validation->set_rules('id_borrar', '', 'trim|required');
		}

		if ($this->form_validation->run() === FALSE)
		{
			$arr_combo_tipo_ubic = array();

			foreach($datos_hoja as $registro)
			{
				if ( ! array_key_exists($registro['tipo_inventario'], $arr_combo_tipo_ubic))
				{
					$arr_combo_tipo_ubic[$registro['tipo_inventario']] = array();
				}
			}

			foreach($arr_combo_tipo_ubic as $llave => $valor)
			{
				$arr_combo_tipo_ubic[$llave] = $this->ubicacion_model->get_combo_tipos_ubicacion($llave);
			}

			$data = array(
				'menu_modulo'            => array('menu' => $this->arr_menu, 'mod_selected' => 'ubicaciones'),
				'datos_hoja'             => $datos_hoja,
				'combo_tipos_inventario' => $tipo_inventario->find('list'),
				'combo_tipos_ubicacion'  => $arr_combo_tipo_ubic,
				'msg_alerta'             => $this->session->flashdata('msg_alerta'),
				'links_paginas'          => $this->pagination->create_links(),
			);

			app_render_view('ubicacion_tipo_ubicacion', $data);
		}
		else
		{
			if ($this->input->post('formulario') === 'editar')
			{
				$cant_modif = 0;

				foreach($datos_hoja as $registro)
				{
					if ((set_value($registro['id'].'-tipo_inventario') !== $registro['tipo_inventario']) OR
						(set_value($registro['id'].'-ubicacion')       !== $registro['ubicacion']) OR
						(set_value($registro['id'].'-tipo_ubicacion')  !== $registro['id_tipo_ubicacion']))
					{
						$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
							$registro['id'],
							set_value($registro['id'] . '-tipo_inventario'),
							set_value($registro['id'] . '-ubicacion'),
							set_value($registro['id'] . '-tipo_ubicacion')
						);
						$cant_modif += 1;
					}
				}
				set_message(
					($cant_modif > 0)
						? $cant_modif . ' inventario(s) modificados correctamente'
						: ''
				);
			}
			else if ($this->input->post('formulario') === 'agregar')
			{
				$count = 0;

				foreach($this->input->post('agr-ubicacion') as $valor)
				{
					$this->ubicacion_model->guardar_ubicacion_tipo_ubicacion(
						0,
						set_value('agr-tipo_inventario'),
						$valor,
						set_value('agr-tipo_ubicacion')
					);
					$count += 1;
				}

				set_message('Se agregaron ' . $count . ' ubicaciones correctamente');
			}
			else if ($this->input->post('formulario') === 'borrar')
			{
				$this->ubicacion_model->borrar_ubicacion_tipo_ubicacion(set_value('id_borrar'));
				set_message('Registro (id=' . set_value('id_borrar') . ') borrado correctamente');
			}

			redirect($this->router->class . '/ubicacion_tipo_ubicacion/' . $pagina);
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