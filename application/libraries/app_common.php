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



	/**
	 * Devuelve arreglo con el menu de las aplicaciones del sistema
	 * @return string    Texto con el menu (<ul>) de las aplicaciones
	 */
	public function menu_app()
	{
		$arr_modulos = $this->acl_model->get_user_menu();

		$arr_apps = array();
		$arr_mods = array();
		$app_ant = '';
		$app_sel = '';

		foreach($arr_modulos as $modulo)
		{
			if ($modulo['app'] != $app_ant and $app_ant != '')
			{
				array_push($arr_apps, array(
											'selected' => $app_sel,
											'icono'    => $modulo_ant['app_icono'],
											'app'      => $modulo_ant['app'],
											'modulos'  => $arr_mods
										));
				$arr_mods = array();
				$app_sel = '';
			}

			if ($this->uri->segment(1) == $modulo['url'])
			{
				$app_sel = 'active';
			}

			array_push($arr_mods, array(
									'url'    => $modulo['url'],
									'icono'  => $modulo['modulo_icono'],
									'modulo' => $modulo['modulo']
									));

			$app_ant = $modulo['app'];
			$modulo_ant = $modulo;
		}
		array_push($arr_apps, array(
									'selected' => $app_sel,
									'icono'    => $modulo_ant['app_icono'],
									'app'      => $modulo_ant['app'],
									'modulos'  => $arr_mods
								));

		return $arr_apps;
	}


	public function titulo_modulo()
	{
		$arr_modulos = $this->acl_model->get_user_menu();

		foreach ($arr_modulos as $mods)
		{
			if($mods['url'] == $this->uri->segment(1))
			{
				return '<span class="' . $mods['modulo_icono'] . ' "></span>&nbsp;&nbsp;' . $mods['modulo'];
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


	public function formato_reporte($valor = '', $arr_param_campo = array(), $reg = array(), $campo = '')
	{
		switch ($arr_param_campo['tipo'])
		{
			case 'texto':
				return $valor;
				break;
			case 'link':
				return anchor($arr_param_campo['href'] . $valor, $valor);
				break;
			case 'link_detalle_series':
				$id_tipo        = (array_key_exists('id_tipo', $reg)) ? $reg['id_tipo'] : '';
				$centro         = (array_key_exists('centro', $reg)) ? $reg['centro'] : '';
				$almacen        = (array_key_exists('almacen', $reg)) ? $reg['almacen'] : '';
				$lote           = (array_key_exists('lote', $reg)) ? $reg['lote'] : '';
				$estado_stock   = (array_key_exists('estado_stock', $reg)) ? $reg['estado_stock'] : '';
				$material       = (array_key_exists('material', $reg)) ? $reg['material'] : '';
				$tipo_material  = (array_key_exists('tipo_material', $reg)) ? $reg['tipo_material']: '';
				$permanencia    = $campo;

				$href_param  = '?id_tipo=' . $id_tipo;
				$href_param .= '&centro=' . $centro;
				$href_param .= '&almacen=' . $almacen;
				$href_param .= '&lote=' . $lote;
				$href_param .= '&estado_stock=' . $estado_stock;
				$href_param .= '&material=' . $material;
				$href_param .= '&tipo_material=' . $tipo_material;
				$href_param .= '&permanencia=' . $permanencia;

				return anchor($arr_param_campo['href'] . $href_param, number_format($valor, 0, ',', '.'));
				break;
			case 'numero':
				return fmt_cantidad($valor);
				break;
			case 'valor':
				return fmt_monto($valor);
				break;
			case 'valor_pmp':
				return '$ ' . fmt_monto($valor);
				break;
			case 'numero_dif':
				return (($valor > 0) ? '<span class="label label-warning">' : (($valor < 0) ? '<span class="label label-danger">' : '')) . (($valor > 0) ? '+' : '') . fmt_cantidad($valor) . (($valor != 0) ? '</span>' : '');
				break;
			case 'valor_dif':
				return (($valor > 0) ? '<span class="label label-warning">' : (($valor < 0) ? '<span class="label label-danger">' : '')) . (($valor > 0) ? '+' : '') . fmt_monto($valor) . (($valor != 0) ? '</span>' : '');
				break;
			default:
				return $valor;
				break;
		}

	}


	public function reporte($arr_campos = array(), $arr_datos = array(), $arr_link_campos = array(), $arr_link_sort = array(), $arr_img_orden = array())
	{
		$tabla = array();

		$n_linea    = 0;
		$totales    = array();
		$subtotales = array();
		$subtot_ant = array();
		$arr_campos_totalizados = array('numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series');

		array_push($tabla, '<table class="table table-bordered table-striped table-hover table-condensed">');
		array_push($tabla, '<thead>');
		array_push($tabla, '<tr>');
		array_push($tabla, '<th></th>');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			array_push($tabla, '<th ' . (($arr_param_campo == '') ? '' : 'class="' . $arr_param_campo['class'] . '"') . '>');
			array_push($tabla, anchor('#', $arr_param_campo['titulo'], array('order_by' => $arr_link_campos[$campo], 'order_sort' => $arr_link_sort[$campo])));
			array_push($tabla, $arr_img_orden[$campo]);
			array_push($tabla, '</th>');

			if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
			{
				$totales[$campo] = 0;
				$subtotales[$campo] = 0;
			}
		}
		array_push($tabla, '</tr>');
		array_push($tabla, '</thead>');

		array_push($tabla, '<tbody>');
		foreach ($arr_datos as $reg)
		{
			array_push($tabla, '<tr>');
			array_push($tabla, '<td><span class="muted">' . ++$n_linea . '</span></td>');

			foreach ($arr_campos as $campo => $arr_param_campo)
			{
				// Si el primer campo es un subtotal, inserta título del campo
				if ($arr_param_campo['tipo'] == 'subtotal')
				{
					if (!array_key_exists($campo, $subtot_ant))
					{
						$subtot_ant[$campo] = '';
					}

					// Si el valor del subtotal es distinto al subtotal anterior, mostramos los subtotales
					if ($reg[$campo] != $subtot_ant[$campo])
					{
						if ($subtot_ant[$campo] != '')
						{
							foreach ($arr_campos as $c => $arr_c)
							{
								array_push($tabla, '<td ' . (($arr_c == '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"') - '>');
								array_push($tabla, in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '');
								array_push($tabla, '</td>');
							}

							array_push($tabla, '</tr>');
							array_push($tabla, '<tr>');
							array_push($tabla, '<td><span class="muted">' . ++$n_linea . '</span></td>');
							array_push($tabla, '<td colspan="' . count($arr_campos) . '" class="subtotal">&nbsp;</td>');
							array_push($tabla, '</tr>');
							array_push($tabla, '<tr>');
						}

						if ($subtot_ant[$campo] != '')
						{
							array_push($tabla, '<td><span class="muted">' . ++$n_linea . '</span></td>');
						}

						$subtot_ant[$campo] = $reg[$campo];
						foreach ($arr_campos as $c => $arr_c)
						{
							$subtotales[$c] = 0;
						}

						array_push($tabla, '<td colspan="' . count($arr_campos) . '" class="subtotal">');
						array_push($tabla, ($arr_param_campo['tipo'] == 'subtotal')  ? $reg[$campo] : '');
						array_push($tabla, '</td>');
						array_push($tabla, '</tr>');
						array_push($tabla, '<tr>');
						array_push($tabla, '<td><span class="muted">' . ++$n_linea . '</span></td>');

					}
				}

				array_push($tabla, '<td ' . (($arr_param_campo == '') ? '' : 'class="' . $arr_param_campo['class'] . '"') . '>');
				array_push($tabla, $this->formato_reporte($reg[$campo], $arr_param_campo, $reg, $campo));
				array_push($tabla, '</td>');

				if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
				{
					$totales[$campo] += $reg[$campo];
				}

				if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
				{
					$subtotales[$campo] += $reg[$campo];
				}
			}

			array_push($tabla, '</tr>');
		}

		//ultima linea de subtotales
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			if ($arr_param_campo['tipo'] == 'subtotal')
			{
				array_push($tabla, '<td><span class="muted">' . ++$n_linea . '</span></td>');
				foreach ($arr_campos as $c => $arr_c)
				{
					array_push($tabla, 'xx <td ' . (($arr_c == '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"') . '>');
					array_push($tabla, in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '');
					array_push($tabla, '</td>');
				}

				array_push($tabla, '</tr>');
				array_push($tabla, '<tr>');
				array_push($tabla, '<td colspan="' . count($arr_campos) + 1 . '" class="subtotal">&nbsp;</td>');
				array_push($tabla, '</tr>');
			}
		}

		// totales
		array_push($tabla, '<tr>');
		array_push($tabla, '<td></td>');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			array_push($tabla, '<td ' . (($arr_param_campo == '') ? '' : 'class="subtotal ' . $arr_param_campo['class'] . '"') . '>');
			array_push($tabla, in_array($arr_param_campo['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($totales[$campo], $arr_param_campo) : '');
			array_push($tabla, '</td>');
		}
		array_push($tabla, '</tr>');

		array_push($tabla, '</tbody>');
		array_push($tabla, '</table>');

		return (implode('', $tabla));
	}






}
/* libraries app_common.php */
/* Location: ./application/libraries/app_common.php */