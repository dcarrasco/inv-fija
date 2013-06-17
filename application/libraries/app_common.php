<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_common {


	public function __construct()
	{
		$this->load->helper('cookie');
		$this->load->model('acl_model');
	}


	public function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}


	private function _get_arr_modulos()
	{
		$arr_modulos = array();

		if (!$this->input->cookie('movistar_menu_app'))
		{
			$arr_modulos = $this->acl_model->get_menu_usuario($this->input->cookie('movistar_usr'));
			$this->input->set_cookie(array(
				'name'   => 'movistar_menu_app',
				'value'  => json_encode($arr_modulos),
				'expire' => '0'
				));
		}
		else
		{
			$arr_modulos = json_decode($this->input->cookie('movistar_menu_app'), TRUE);
		}

		return $arr_modulos;

	}

	/**
	 * Devuelve arreglo con el menu de las aplicaciones del sistema
	 * @return string    Texto con el menu (<ul>) de las aplicaciones
	 */
	public function menu_app()
	{
		$arr_modulos = $this->_get_arr_modulos();

		$arr_apps = array();
		$arr_mods = array();
		$app_ant = '';
		$app_sel = '';

		foreach($arr_modulos as $modulo)
		{
			if ($modulo['app'] != $app_ant and $app_ant != '')
			{
				array_push($arr_apps, array('selected' => $app_sel, 'icono' => $modulo_ant['app_icono'], 'app' => $modulo_ant['app'], 'modulos' => $arr_mods));
				$arr_mods = array();
				$app_sel = '';
			}

			if ($this->uri->segment(1) == $modulo['url'])
			{
				$app_sel = 'active';
			}

			array_push($arr_mods, array('url' => $modulo['url'], 'icono' => $modulo['modulo_icono'], 'modulo' => $modulo['modulo']));

			$app_ant = $modulo['app'];
			$modulo_ant = $modulo;
		}
		array_push($arr_apps, array('selected' => $app_sel, 'icono' => $modulo_ant['app_icono'], 'app' => $modulo_ant['app'], 'modulos' => $arr_mods));

		return $arr_apps;
	}


	public function titulo_modulo()
	{
		$arr_modulos = $this->_get_arr_modulos();

		foreach ($arr_modulos as $mods)
		{
			if($mods['url'] == $this->uri->segment(1))
			{
				return $mods['modulo'];
			}
		}

	}

	public function menu_modulo($op_selected = '', $menu = array())
	{

		$menu = '<ul class="nav nav-tabs">';
		foreach($menu as $key => $val)
		{
			$selected = ($key == $op_selected) ? ' class="active"' : '';
			$menu .= '<li' . $selected . '>' . anchor($val['url'], $val['texto']) . '</li>';
		}
		$menu .= '</ul>';
		return $menu;
	}



}


/* libraries app_common.php */
/* Location: ./application/libraries/app_common.php */