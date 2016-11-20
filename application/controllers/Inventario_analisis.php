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
 * Clase Controller Análisis de inventarios
 * *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_analisis extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'analisis';

	/**
	 * Identificador del inventario
	 *
	 * @var  integer
	 */
	private $_id_inventario = 0;

	/**
	 * Nombre del inventario
	 *
	 * @var  string
	 */
	private $_nombre_inventario = '';

	/**
	 * Menu de opciones
	 *
	 * @var  array
	 */
	private $_arr_menu = array();

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
		$this->lang->load('inventario');

		$this->_arr_menu = array(
			'ajustes' => array(
				'url'   => $this->router->class . '/ajustes',
				'texto' => $this->lang->line('inventario_menu_ajustes'),
				'icon'  => 'wrench'
			),
			'sube_stock' => array(
				'url'   => $this->router->class . '/sube_stock',
				'texto' => $this->lang->line('inventario_menu_upload'),
				'icon'  => 'cloud-upload'
			),
			'imprime_inventario' => array(
				'url'   => $this->router->class . '/imprime_inventario',
				'texto' => $this->lang->line('inventario_menu_print'),
				'icon'  => 'print'
			),
			'actualiza_precios' => array(
				'url'   => $this->router->class . '/actualiza_precios',
				'texto' => $this->lang->line('inventario_menu_act_precios'),
				'icon'  => 'usd'
			),
		);

	}


	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->ajustes();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los datos del inventario activo
	 *
	 * @return nada
	 */
	private function _get_datos_inventario()
	{
		$inventario_activo = new Inventario;
		$this->_id_inventario = $inventario_activo->get_id_inventario_activo();
		$inventario_activo->find_id($this->_id_inventario);
		$this->_nombre_inventario = $inventario_activo->nombre;
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega la página de ajustes de inventarios
	 *
	 * @return void
	 */
	public function ajustes()
	{
		$this->_get_datos_inventario();
		$pagina = $this->input->get('page');
		$ocultar_reg = $this->input->get('ocultar_reg') ? 1 : 0;

		// recupera el detalle de registros con diferencias
		$detalle_ajustes = new Detalle_inventario;
		$detalles = $detalle_ajustes->get_ajustes($this->_id_inventario, $ocultar_reg, $pagina);
		$links_paginas = $detalle_ajustes->get_pagination_ajustes($this->_id_inventario, $ocultar_reg, $pagina);

		if ($this->input->post('formulario') === 'ajustes')
		{
			$this->form_validation->set_rules($detalle_ajustes->get_validation_ajustes($detalles));
		}

		if ($this->form_validation->run() === FALSE)
		{
			$data = array(
				'menu_modulo'     => array('menu' => $this->_arr_menu, 'mod_selected' => 'ajustes'),
				'inventario'      => $this->_id_inventario.' - '.$this->_nombre_inventario,
				'detalle_ajustes' => $detalles,
				'ocultar_reg'     => $ocultar_reg,
				'pag'             => $pagina,
				'links_paginas'   => $links_paginas,
				'url_form'        => site_url("{$this->router->class}/ajustes?".http_build_query($this->input->get())),
			);

			app_render_view('inventario/ajustes', $data);
		}
		else
		{
			if ($this->input->post('formulario') === 'ajustes')
			{
				$cant_modif = $detalle_ajustes->update_ajustes($detalles);
				set_message(($cant_modif > 0) ? sprintf($this->lang->line('inventario_adjust_msg_save'), $cant_modif)
					: '');
			}

			redirect("{$this->router->class}/ajustes?".http_build_query($this->input->get()));
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Permite subir un archivo con el stock a cargar a un inventario
	 *
	 * @return void
	 */
	public function sube_stock()
	{
		$this->_get_datos_inventario();

		$upload_path       = '../upload/';
		$upload_file       = 'sube_stock.txt';
		$upload_error      = '';
		$script_carga      = '';
		$regs_ok           = 0;
		$regs_error        = 0;
		$msj_error         = '';
		$show_script_carga = FALSE;

		if ($this->input->post('formulario') === 'upload')
		{
			if ($this->input->post('upload_password') === 'logistica2012')
			{
				unlink($upload_path.$upload_file);
				$this->load->library('upload', array(
					'upload_path'   => $upload_path,
					'allowed_types' => 'txt|cvs',
					'max_size'      => '2000',
					'file_name'     => $upload_file,
					'overwrite'     => TRUE,
				));

				if ( ! $this->upload->do_upload('upload_file'))
				{
					set_message($this->lang->line('inventario_upload_error').' ('.$this->upload->display_errors().')', 'danger');
				}
				else
				{
					$upload_data = $this->upload->data();
					$archivo_cargado = $upload_data['full_path'];

					$inventario = new Inventario($this->_id_inventario);
					$inventario->borrar_detalle_inventario();
					$res_procesa_archivo = $inventario->cargar_datos_archivo($archivo_cargado);

					$script_carga = $res_procesa_archivo['script'];
					$show_script_carga = TRUE;
					$regs_ok      = $res_procesa_archivo['regs_ok'];
					$regs_error   = $res_procesa_archivo['regs_error'];
					$msj_error    = ($regs_error > 0)
						? '<br><div class="error round">' . $res_procesa_archivo['msj_termino'] . '</div>'
						: '';
				}
			}
		}

		$data = array(
			'menu_modulo'       => array('menu' => $this->_arr_menu, 'mod_selected' => 'sube_stock'),
			'inventario_id'     => $this->_id_inventario,
			'inventario_nombre' => $this->_nombre_inventario,
			'script_carga'      => $script_carga,
			'show_script_carga' => $show_script_carga,
			'regs_ok'           => $regs_ok,
			'regs_error'        => $regs_error,
			'msj_error'         => $msj_error,
			'link_config'       => 'config',
			'link_reporte'      => 'reportes',
			'link_inventario'   => 'inventario',
		);

		app_render_view('inventario/sube_stock', $data);

	}

	// --------------------------------------------------------------------

	/**
	 * Recibe linea de detalle de inventario por post, y lo graba
	 *
	 * @return void
	 */
	public function inserta_linea_archivo()
	{
		$detalle = new Detalle_inventario;
		$detalle->recuperar_post();
		$detalle->grabar();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite imprimir el detalle del inventario
	 *
	 * @param  integer $id_inventario Identificador del inventario a imprimir
	 * @return void
	 */
	public function imprime_inventario($id_inventario = 0)
	{
		$this->_get_datos_inventario();

		$inventario = new Inventario($this->_id_inventario);

		$this->form_validation->set_rules('pag_desde', $this->lang->line('inventario_print_label_page_from'), 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('pag_hasta', $this->lang->line('inventario_print_label_page_to'), 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('oculta_stock_sap', 'Oculta Stock SAP', '');

		if ($this->form_validation->run() === FALSE)
		{
			$data = array(
				'menu_modulo'       => array('menu' => $this->_arr_menu, 'mod_selected' => 'imprime_inventario'),
				'inventario_id'     => $this->_id_inventario,
				'inventario_nombre' => $this->_nombre_inventario,
				'max_hoja'          => $inventario->get_max_hoja_inventario(),
				'link_config'       => 'config',
				'link_reporte'      => 'reportes',
				'link_inventario'   => 'inventario',
			);

			app_render_view('inventario/imprime_inventario', $data);
		}
		else
		{
			$oculta_stock_sap = (set_value('oculta_stock_sap') === 'oculta_stock_sap') ? 1 : 0;

			redirect(
				$this->router->class . '/imprime_hojas/' .
				set_value('pag_desde') . '/' .
				set_value('pag_hasta') . '/' .
				$oculta_stock_sap . '/' .
				time()
			);
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Genera las hojas a imprimir
	 *
	 * @param  integer $hoja_desde       Hoja inicial a imprimir
	 * @param  integer $hoja_hasta       Hoja final a imprimir
	 * @param  integer $oculta_stock_sap Permite ocultar la columna con el stock de SAP
	 * @return void
	 */
	public function imprime_hojas($hoja_desde = 1, $hoja_hasta = 1, $oculta_stock_sap = 0)
	{
		$this->_get_datos_inventario();

		$hoja_desde = ($hoja_desde < 1) ? 1 : $hoja_desde;
		$hoja_hasta = ($hoja_hasta < $hoja_desde) ? $hoja_desde : $hoja_hasta;

		$this->load->view('inventario/inventario_print_head');

		for ($hoja = $hoja_desde; $hoja <= $hoja_hasta; $hoja++)
		{
			$detalle = new Detalle_inventario;

			$data = array(
				'datos_hoja'        => $detalle->get_hoja($this->_id_inventario, $hoja),
				'oculta_stock_sap'  => $oculta_stock_sap,
				'hoja'              => $hoja,
				'nombre_inventario' => $this->_nombre_inventario,
			);

			$this->load->view('inventario/inventario_print_body', $data);
		}

		$this->load->view('inventario/inventario_print_footer');
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza los precios del catálogo
	 *
	 * @return void
	 */
	public function actualiza_precios()
	{
		$update_status = '';
		$cant_actualizada = 0;

		if ($this->input->post('actualizar') === 'actualizar')
		{
			$catalogo = new Catalogo;
			$cant_actualizada = $catalogo->actualiza_precios();
			$update_status = ' disabled';
		}

		$data = array(
			'menu_modulo'      => array('menu' => $this->_arr_menu, 'mod_selected' => 'actualiza_precios'),
			'update_status'    => $update_status,
			'cant_actualizada' => $cant_actualizada,
		);

		app_render_view('inventario/actualiza_precios', $data);

	}

}
/* End of file inventario_analisis.php */
/* Location: ./application/controllers/inventario_analisis.php */