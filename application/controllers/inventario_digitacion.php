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
 * Clase Controller Digitacion Inventario
 *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_digitacion extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'inventario';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('inventario');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
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
	 * @return void
	 */
	public function ingreso($hoja = 0)
	{
		if ($hoja === 0 OR $hoja === '' OR $hoja === NULL)
		{
			$hoja = 1;
		}

		// recupera el inventario activo
		$inventario = new Inventario;
		$inventario->get_inventario_activo();

		$nuevo_detalle_inventario = new Detalle_inventario;
		$nuevo_detalle_inventario->get_relation_fields();

		//$this->benchmark->mark('detalle_inventario_start');
		$detalle_inventario = new Detalle_inventario;
		$detalle_inventario->get_hoja($inventario->get_id_inventario_activo(), $hoja);
		//$this->benchmark->mark('detalle_inventario_end');

		$detalle_inventario->auditor = $detalle_inventario->get_id_auditor();

		if ($this->input->post('formulario') === 'buscar')
		{
			$nuevo_detalle_inventario->set_validation_rules_field('hoja');
			$nuevo_detalle_inventario->set_validation_rules_field('auditor');
		}
		else if ($this->input->post('formulario') === 'inventario')
		{
			$nuevo_detalle_inventario->set_validation_rules_field('hoja');
			$nuevo_detalle_inventario->set_validation_rules_field('auditor');
			$this->form_validation->set_rules($detalle_inventario->get_validation_digitacion());
		}

		$this->app_common->form_validation_config();

		if ($this->form_validation->run() === FALSE)
		{
			$data = array(
				'detalle_inventario' => $detalle_inventario,
				'hoja'               => $hoja,
				'nombre_inventario'  => $inventario->nombre,
				'id_auditor'         => $detalle_inventario->get_id_auditor(),
				'link_hoja_ant'      => base_url($this->router->class . '/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . time()),
				'link_hoja_sig'      => base_url($this->router->class . '/ingreso/' . ($hoja + 1) . '/' . time()),
			);

			app_render_view('inventario/inventario', $data);
		}
		else
		{
			if ($this->input->post('formulario') === 'inventario')
			{
				$id_usuario_login  = $this->acl_model->get_id_usr();
				$cant_modif = $detalle_inventario->update_digitacion($id_usuario_login);

				set_message(($cant_modif > 0) ? sprintf($this->lang->line('inventario_digit_msg_save'), $cant_modif, $hoja) : '');
			}

			redirect($this->router->class . '/ingreso/' . $this->input->post('hoja') . '/' . time());
		}

	}

	// --------------------------------------------------------------------


	/**
	 * Permite editar una linea de detalle agregada a un inventario
	 *
	 * @param  integer $hoja        Numero de la hoja a visualizar
	 * @param  integer $id_auditor  ID del auditor de la hoja
	 * @param  integer $id_registro ID del registro a editar
	 * @return void
	 */
	public function editar($hoja = 0, $id_auditor = 0, $id_registro = NULL)
	{
		$detalle_inventario = new Detalle_inventario;

		$catalogo = new Catalogo;
		$arr_catalogo = array('' => 'Buscar y seleccionar material...');
		if ($this->input->post('catalogo'))
		{
			$catalogo->find_id($this->input->post('catalogo'));
			$arr_catalogo = array($this->input->post('catalogo') => $catalogo);
		}

		if ($id_registro)
		{
			$detalle_inventario->find_id($id_registro);
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

		if ($this->form_validation->run() === FALSE)
		{
			$data = array(
				'detalle_inventario' => $detalle_inventario,
				'id'                 => $id_registro,
				'hoja'               => $hoja,
				'arr_catalogo'       => $arr_catalogo,
			);

			app_render_view('inventario/inventario_editar', $data);
		}
		else
		{
			$nuevo_material = new Catalogo;
			$nuevo_material->find_id($this->input->post('catalogo'));

			$detalle_inventario->fill(array(
				'id'                 => (int) $id_registro,
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
				'fecha_ajuste'       => NULL,
			));

			if ($this->input->post('accion') === 'agregar')
			{
				$detalle_inventario->grabar();
				set_message(sprintf($this->lang->line('inventario_digit_msg_add'), $hoja));
			}
			else
			{
				$detalle_inventario->borrar();
				set_message(sprintf($this->lang->line('inventario_digit_msg_delete'), $id_registro, $hoja));
			}

			redirect($this->router->class . '/ingreso/' . $hoja . '/' . time());
		}

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega listado con materiales, en formato de formulario select
	 *
	 * @param  string $filtro Filtro de los materiales a desplegar
	 * @return void
	 */
	public function ajax_act_agr_materiales($filtro = '')
	{
		$material = new Catalogo;
		$arr_dropdown = $material->find('list', array('filtro' => $filtro));

		$options = '';
		foreach ($arr_dropdown as $llave => $valor)
		{
			$options .= '<option value="' . $llave . '">' . $valor . '</option>';
		}

		$this->output->set_content_type('text')->set_output($options);
	}


}
/* End of file inventario_digitacion.php */
/* Location: ./application/controllers/inventario_digitacion.php */