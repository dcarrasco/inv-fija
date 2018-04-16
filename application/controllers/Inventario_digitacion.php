<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
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

use Acl\Acl;
use Inventario\catalogo;
use Inventario\Inventario;
use Inventario\detalle_inventario;

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
class Inventario_digitacion extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'inventario';

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'inventario';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('user_agent');
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

		if ($this->agent->is_mobile())
		{
			return $this->hoja_mobile($hoja);
		}

		if ( $hoja === 0 && ! empty(request('hoja')))
		{
			redirect($this->router->class.'/ingreso/'.request('hoja'));
		}

		// recupera el inventario activo
		$hoja = empty($hoja) ? 1 : $hoja;
		$inventario = Inventario::create()->get_inventario_activo();
		$id_auditor = Detalle_inventario::create()->get_auditor_hoja($inventario->get_id(), $hoja);

		app_render_view('inventario/inventario', [
			'detalle_inventario' => Detalle_inventario::create()->get_hoja_inventario($inventario->get_id(), $hoja),
			'hoja'               => $hoja,
			'nombre_inventario'  => $inventario->nombre,
			'combo_auditores'    => Detalle_inventario::create()->fill(['auditor' => $id_auditor])->print_form_field('auditor', FALSE, 'input-sm'),
			'id_auditor'         => $id_auditor,
			'url_form'           => "{$this->router->class}/update_hoja/{$hoja}/{$id_auditor}",
			'link_hoja_ant'      => base_url($this->router->class . '/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . time()),
			'link_hoja_sig'      => base_url($this->router->class . '/ingreso/' . ($hoja + 1) . '/' . time()),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Permite digitar una hoja de detalle de inventario
	 *
	 * @param  integer $hoja Numero de la hoja a visualizar
	 * @return void
	 */
	public function update_hoja($hoja = 0)
	{
		// recupera el inventario activo
		$hoja = empty($hoja) ? 1 : $hoja;
		$inventario = Inventario::create()->get_inventario_activo();

		Detalle_inventario::create()->set_field_validation_rules('hoja');
		Detalle_inventario::create()->set_field_validation_rules('auditor');
		$this->form_validation->set_rules(
			Detalle_inventario::create()->rules_digitacion(
				Detalle_inventario::create()->get_hoja_inventario($inventario->get_id(), $hoja)
			)
		);

		route_validation($this->form_validation->run());

		$id_usuario_login = Acl::create()->get_id_usr();
		$cant_modif = Detalle_inventario::create()->update_digitacion($inventario->get_id(), $hoja, $id_usuario_login);
		set_message(($cant_modif > 0) ? sprintf(lang('inventario_digit_msg_save'), $cant_modif, $hoja) : '');

		redirect($this->router->class.'/ingreso/'.request('hoja'));
	}

	// --------------------------------------------------------------------
	/**
	 * Pantalla de visualización de una hoja de inventario
	 * para dispositivos móviles
	 *
	 * @param  integer $hoja Hoja a desplegar
	 * @return void
	 */
	public function hoja_mobile($hoja = 0)
	{
		// recupera el inventario activo
		$hoja = empty($hoja) ? 1 : $hoja;
		$inventario = Inventario::create()->get_inventario_activo();

		if ($this->form_validation->run() === FALSE)
		{
			app_render_view('inventario/inventario_mobile', [
				'detalle_inventario' => Detalle_inventario::create()->get_hoja_inventario($inventario->get_id(), $hoja),
				'hoja'               => $hoja,
				'nombre_inventario'  => $inventario->nombre,
				'id_auditor'         => Detalle_inventario::create()->get_auditor_hoja($inventario->get_id(), $hoja),
				'link_hoja_ant'      => base_url($this->router->class . '/ingreso/' . (($hoja <= 1) ? 1 : $hoja - 1) . '/' . time()),
				'link_hoja_sig'      => base_url($this->router->class . '/ingreso/' . ($hoja + 1) . '/' . time()),
			]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Pantalla de ingreso de valores de inventario para dispositivos móviles
	 *
	 * @param  integer $hoja        Hoja a desplegar
	 * @param  integer $id_registro ID del registro de detalle de inventario
	 * @return void
	 */
	public function ingreso_mobile($hoja = 0, $id_registro = NULL)
	{
		if ( ! $id_registro)
		{
			return $this->ingreso($hoja);
		}

		$detalle_inventario = new Detalle_inventario;
		$detalle_inventario->find_id($id_registro);
		$detalle_inventario->get_validation_editar_mobile();

		if ($this->form_validation->run() === FALSE)
		{
			app_render_view('inventario/inventario_mobile_ingreso', [
				'detalle_inventario' => $detalle_inventario,
			]);
		}
		else
		{
			$detalle_inventario->recuperar_post();
			$detalle_inventario->fill([
				'digitador' => Acl::create()->get_id_usr(),
				'auditor'   => Acl::create()->get_id_usr(),
				'fecha_modificacion' => date('Y-m-d H:i:s'),
			]);
			$detalle_inventario->grabar();

			set_message(sprintf(lang('inventario_digit_msg_save'), 1, $hoja));
			redirect($this->router->class . '/ingreso/' . $hoja . '/' . time());
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
		$detalle_inventario = new Detalle_inventario($id_registro);

		$arr_catalogo = is_null($id_registro)
			? ['' => 'Buscar y seleccionar material...']
			: [$detalle_inventario->catalogo => $detalle_inventario->descripcion];

		$detalle_inventario->fill(['hoja' => $hoja]);
		$detalle_inventario->get_relation_fields();

		app_render_view('inventario/inventario_editar', [
			'detalle_inventario' => $detalle_inventario,
			'id'                 => $id_registro,
			'hoja'               => $hoja,
			'arr_catalogo'       => $arr_catalogo,
			'url_form'           => site_url("{$this->router->class}/update/{$hoja}/{$id_auditor}/{$id_registro}"),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza un registro de inventario
	 *
	 * @param  integer $hoja        Numero de la hoja a actualizar
	 * @param  integer $id_auditor  ID del auditor que realiza la actualizacion
	 * @param  integer $id_registro ID del registro a actualizar
	 *
	 * @return redirect
	 */
	public function update($hoja = 0, $id_auditor = 0, $id_registro = NULL)
	{
		$detalle_inventario = new Detalle_inventario($id_registro);
		$detalle_inventario->get_editar_post_data($id_registro, $hoja);

		$detalle_inventario->get_validation_editar();
		route_validation($this->form_validation->run());

		if (request('accion') === 'agregar')
		{
			$detalle_inventario->grabar();
			set_message(sprintf(lang('inventario_digit_msg_add'), $hoja));
		}
		else
		{
			$detalle_inventario->borrar();
			set_message(sprintf(lang('inventario_digit_msg_delete'), $id_registro, $hoja));
		}

		redirect("{$this->router->class}/ingreso/{$hoja}/".time());
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
		return $this->output
			->set_content_type('text')
			->set_output(form_print_options(Catalogo::create()->filter_by($filtro)->get_dropdown_list(FALSE)));
	}

}
// End of file Inventario_digitacion.php
// Location: ./controllers/Inventario_digitacion.php
