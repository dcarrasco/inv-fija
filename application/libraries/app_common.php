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
	 * Devuelve el menu de las aplicaciones del sistema
	 * @return string    Texto con el menu (<ul>) de las aplicaciones
	 */
	public function menu_app()
	{
		$arr_modulos = $this->_get_arr_modulos();

		$arr_apps = array();
		$arr_mods = array();
		$app_sel  = '';

		$app_ant = '';
		foreach($arr_modulos as $modulo)
		{
			if ($modulo['app'] != $app_ant)
			{
				array_push($arr_apps, array('app' => $modulo['app'], 'app_icono' => $modulo['app_icono']));
				$arr_mods[$modulo['app']] = array();
			}
			array_push($arr_mods[$modulo['app']], $modulo);

			if ($this->uri->segment(1) == $modulo['url'])
			{
				$app_sel = $modulo['app'];
			}

			$app_ant = $modulo['app'];
		}

		$menu = '<ul class="nav pull-right">';
		foreach($arr_apps as $apps)
		{
			$app_selected = ($app_sel == $apps['app']) ? ' active' : '';
			$menu .= '<li class="dropdown' . $app_selected . '"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="' . $apps['app_icono'] . '"></i> ' . $apps['app'] . '<b class="caret"></b></a>';

			$menu .= '<ul class="dropdown-menu">';
			foreach ($arr_mods[$apps['app']] as $mods)
			{
				$menu .= '<li><a href="' . site_url($mods['url']) . '"><i class="' . $mods['modulo_icono'] . '"></i> ' . $mods['modulo'] . '</a></li>';

			}
			$menu .= '</ul></li>';
		}
		$menu .= '<li><a href="' . site_url('login') . '"><i class="icon-off"></i> Logout</a>';
		$menu .= "</ul>";

		return $menu;
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