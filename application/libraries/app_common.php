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
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase con functionalidades comunes de la aplicacion
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class App_common {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->load->helper('cookie');
		$this->load->model('acl_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Funcion que permite recuperar objetos de la instancia global
	 *
	 * @param  string $key Llave a recuperar
	 * @return mixed       Objeto CI
	 */
	public function __get($key = '')
	{
		$ci =& get_instance();

		return $ci->$key;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con el menu de las aplicaciones del sistema
	 *
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
			if ($modulo['app'] !== $app_ant AND $app_ant !== '')
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

			if ($this->uri->segment(1) === $modulo['url'])
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


	// --------------------------------------------------------------------

	/**
	 * Devuelve el titulo del modulo
	 *
	 * @return string Titulo del modulo
	 */
	public function titulo_modulo()
	{
		$arr_modulos = $this->acl_model->get_user_menu();

		foreach ($arr_modulos as $modulo)
		{
			if($modulo['url'] === $this->uri->segment(1))
			{
				return '<span class="' . $modulo['modulo_icono'] . ' "></span>&nbsp;&nbsp;' . $modulo['modulo'];
			}
		}

	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve listado con el menu modulo
	 *
	 * @param  string $op_selected opción del módulo seleccionada
	 * @param  array  $menu        menu
	 * @return sring               listado menu
	 */
	public function menu_modulo($op_selected = '', $menu = array())
	{
		$menu = '<ul class="nav nav-tabs">';

		foreach($menu as $llave => $valor)
		{
			$selected = ($llave === $op_selected) ? ' class="active"' : '';
			$menu .= '<li' . $selected . '>' . anchor($valor['url'], $valor['texto']) . '</li>';
		}

		$menu .= '</ul>';

		return $menu;
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un valor de acuerdo a los parametros de un reporte
	 *
	 * @param  string $valor           Valor a formatear
	 * @param  array  $arr_param_campo Parametros del campo
	 * @param  array  $reg             [description]
	 * @param  string $campo           [description]
	 * @return [type]                  [description]
	 */
	public function formato_reporte($valor = '', $arr_param_campo = array(), $registro = array(), $campo = '')
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
							$id_tipo        = array_key_exists('id_tipo', $registro) ? $registro['id_tipo'] : '';
							$centro         = array_key_exists('centro', $registro) ? $registro['centro'] : '';
							$almacen        = array_key_exists('almacen', $registro) ? $registro['almacen'] : '';
							$lote           = array_key_exists('lote', $registro) ? $registro['lote'] : '';
							$estado_stock   = array_key_exists('estado_stock', $registro) ? $registro['estado_stock'] : '';
							$material       = array_key_exists('material', $registro) ? $registro['material'] : '';
							$tipo_material  = array_key_exists('tipo_material', $registro) ? $registro['tipo_material']: '';
							$permanencia    = $campo;

							$href_param  = '?id_tipo=' . $id_tipo;
							$href_param .= '&centro=' . $centro;
							$href_param .= '&almacen=' . $almacen;
							$href_param .= '&lote=' . $lote;
							$href_param .= '&estado_stock=' . $estado_stock;
							$href_param .= '&material=' . $material;
							$href_param .= '&tipo_material=' . $tipo_material;
							$href_param .= '&permanencia=' . $permanencia;

							$valor_desplegar = fmt_cantidad($valor);
							return anchor($arr_param_campo['href'] . $href_param, $valor_desplegar === '' ? ' ' : $valor_desplegar);
							break;
			case 'numero':
							return fmt_cantidad($valor);
							break;
			case 'valor':
							return fmt_monto($valor);
							break;
			case 'valor_pmp':
							return fmt_monto($valor);
							break;
			case 'numero_dif':
							return '<strong>' .
								(($valor > 0)
									? '<p class="text-success">'
									: (($valor < 0) ? '<p class="text-danger">' : '')) .
								(($valor > 0) ? '+' : '') . fmt_cantidad($valor) .
								(($valor !== 0) ? '</p>' : '') .
								'</strong>';
							break;
			case 'valor_dif':
							return '<strong>' .
								(($valor > 0)
									? '<p class="text-success">'
									: (($valor < 0) ? '<p class="text-danger">' : '')) .
								(($valor > 0) ? '+' : '') . fmt_monto($valor) .
								(($valor !== 0) ? '</p>' : '') .
								'</strong>';
							break;
			default:
							return $valor;
							break;
		}

	}


	// --------------------------------------------------------------------

	/**
	 * Imprime reporte
	 *
	 * @param  array $arr_campos Arreglo con los campos del reporte
	 * @param  array $arr_datos  Arreglo con los datos del reporte
	 * @return string             Reporte
	 */
	public function reporte($arr_campos = array(), $arr_datos = array())
	{
		$this->load->library('table');

		$template = array(
			'table_open' => '<table class="table table-striped table-hover table-condensed reporte table-fixed-header">',
		);

		$this->table->set_template($template);

		// dbg($arr_campos);
		// dbg($arr_datos);

		$arr_linea = array();

		$tabla = array();

		$n_linea    = 0;
		$totales    = array();
		$subtotales = array();
		$subtot_ant = array();
		$arr_campos_totalizados = array('numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series');

		// --- ENCABEZADO REPORTE ---
		array_push($arr_linea, '');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			$arr_celda = array(
				'data' => '<span order_by="'.$campo.'" order_sort="'.$arr_param_campo['order_by'].'" data-toggle="tooltip" title="Ordenar por campo \''.$arr_param_campo['titulo'].'\'">'.$arr_param_campo['titulo'].'</span>'.$arr_param_campo['img_orden'],
				'class' => $arr_param_campo === '' ? '' : $arr_param_campo['class'],
			);
			array_push($arr_linea, $arr_celda);

			if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
			{
				$totales[$campo] = 0;
				$subtotales[$campo] = 0;
			}
		}

		$this->table->set_heading($arr_linea);

		// --- CUERPO REPORTE ---
		foreach ($arr_datos as $registro)
		{
			$arr_linea = array();
			array_push($arr_linea, array('data' => ++$n_linea, 'class' => 'text-muted'));

			foreach ($arr_campos as $campo => $arr_param_campo)
			{
				// Si el primer campo es un subtotal, inserta título del campo
				if ($arr_param_campo['tipo'] === 'subtotal')
				{
					if ( ! array_key_exists($campo, $subtot_ant))
					{
						$subtot_ant[$campo] = '';
					}

					// Si el valor del subtotal es distinto al subtotal anterior, mostramos los subtotales
					if ($registro[$campo] !== $subtot_ant[$campo])
					{
						if ($subtot_ant[$campo] !== '')
						{
							foreach ($arr_campos as $c => $arr_c)
							{
								$arr_celda = array(
									'data'  => '<strong>'.(in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '').'</strong>',
									'class' => $arr_c === '' ? '' : 'subtotal '.$arr_c['class'],
								);
								array_push($arr_linea, $arr_celda);
							}
							$this->table->add_row($arr_linea);

							$arr_linea = array();
							array_push($arr_linea, ++$n_linea);
						}

						if ($subtot_ant[$campo] !== '')
						{
							array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');
						}

						$subtot_ant[$campo] = $registro[$campo];
						foreach ($arr_campos as $c => $arr_c)
						{
							$subtotales[$c] = 0;
						}

						array_push($tabla, '<td colspan="' . count($arr_campos) . '" class="subtotal">');
						array_push($tabla, '<strong>');
						array_push($tabla, ($arr_param_campo['tipo'] === 'subtotal')  ? $registro[$campo] : '');
						array_push($tabla, '</strong>');
						array_push($tabla, '</td>');
						array_push($tabla, '</tr>');
						array_push($tabla, '<tr>');
						array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');

					}
				}

				$arr_celda = array(
					'data'  => $this->formato_reporte($registro[$campo], $arr_param_campo, $registro, $campo),
					'class' => $arr_param_campo === '' ? '' : $arr_param_campo['class'],
				);

				array_push($arr_linea, $arr_celda);

				if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
				{
					$totales[$campo] += $registro[$campo];
					$subtotales[$campo] += $registro[$campo];
				}
			}

			$this->table->add_row($arr_linea);

		}

		// --- ULTIMA LINEA DE SUBTOTALES ---
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			if ($arr_param_campo['tipo'] === 'subtotal')
			{
				array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');
				foreach ($arr_campos as $c => $arr_c)
				{
					array_push($tabla, '<td ' . (($arr_c === '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"') . '>');
					array_push($tabla, '<strong>');
					array_push($tabla, in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '');
					array_push($tabla, '</strong>');
					array_push($tabla, '</td>');
				}

				array_push($tabla, '</tr>');
				array_push($tabla, '<tr>');
				array_push($tabla, '<td colspan="' . (count($arr_campos) + 1) . '" class="subtotal">&nbsp;</td>');
				array_push($tabla, '</tr>');
			}
		}

		// --- TOTALES ---
		$arr_linea = array();
		array_push($arr_linea, '');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			$arr_celda = array(
				'data' => in_array($arr_param_campo['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($totales[$campo], $arr_param_campo) : '',
				'class' => 'subtotal ' . ($arr_param_campo === '' ? '' : $arr_param_campo['class']),
			);

			array_push($arr_linea, $arr_celda);
		}
		$this->table->add_row($arr_linea);

		array_push($tabla, '<script type="text/javascript" src="' . base_url() . 'js/reporte.js"></script>');

		return $this->table->generate();
	}


	public function reporte2($arr_campos = array(), $arr_datos = array())
	{
		$tabla = array();

		$n_linea    = 0;
		$totales    = array();
		$subtotales = array();
		$subtot_ant = array();
		$arr_campos_totalizados = array('numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series');

		array_push($tabla, '<br/><table class="table table-striped table-hover table-condensed reporte table-fixed-header">');

		// --- ENCABEZADO REPORTE ---
		array_push($tabla, '<thead class="header">');
		array_push($tabla, '<tr>');
		array_push($tabla, '<th></th>');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			array_push($tabla, '<th ' . (($arr_param_campo === '') ? '' : 'class="' . $arr_param_campo['class'] . '"') . '>');
			array_push($tabla, '<span order_by="' . $campo . '" order_sort="' . $arr_param_campo['order_by'] . '" data-toggle="tooltip" title="Ordenar por campo \'' . $arr_param_campo['titulo'] . '\'">' . $arr_param_campo['titulo'] . '</span>');
			array_push($tabla, $arr_param_campo['img_orden']);
			array_push($tabla, '</th>');

			if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
			{
				$totales[$campo] = 0;
				$subtotales[$campo] = 0;
			}
		}
		array_push($tabla, '</tr>');
		array_push($tabla, '</thead>');

		// --- CUERPO REPORTE ---
		array_push($tabla, '<tbody>');
		foreach ($arr_datos as $registro)
		{
			array_push($tabla, '<tr>');
			array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');

			foreach ($arr_campos as $campo => $arr_param_campo)
			{
				// Si el primer campo es un subtotal, inserta título del campo
				if ($arr_param_campo['tipo'] === 'subtotal')
				{
					if ( ! array_key_exists($campo, $subtot_ant))
					{
						$subtot_ant[$campo] = '';
					}

					// Si el valor del subtotal es distinto al subtotal anterior, mostramos los subtotales
					if ($registro[$campo] !== $subtot_ant[$campo])
					{
						if ($subtot_ant[$campo] !== '')
						{
							foreach ($arr_campos as $c => $arr_c)
							{
								array_push($tabla, '<td ' . (($arr_c === '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"') . '>');
								array_push($tabla, '<strong>');
								array_push($tabla, in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '');
								array_push($tabla, '</strong>');
								array_push($tabla, '</td>');
							}

							array_push($tabla, '</tr>');
							array_push($tabla, '<tr>');
							array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');
							array_push($tabla, '<td colspan="' . count($arr_campos) . '" class="subtotal">&nbsp;</td>');
							array_push($tabla, '</tr>');
							array_push($tabla, '<tr>');
						}

						if ($subtot_ant[$campo] !== '')
						{
							array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');
						}

						$subtot_ant[$campo] = $registro[$campo];
						foreach ($arr_campos as $c => $arr_c)
						{
							$subtotales[$c] = 0;
						}

						array_push($tabla, '<td colspan="' . count($arr_campos) . '" class="subtotal">');
						array_push($tabla, '<strong>');
						array_push($tabla, ($arr_param_campo['tipo'] === 'subtotal')  ? $registro[$campo] : '');
						array_push($tabla, '</strong>');
						array_push($tabla, '</td>');
						array_push($tabla, '</tr>');
						array_push($tabla, '<tr>');
						array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');

					}
				}

				array_push($tabla, '<td ' . (($arr_param_campo === '') ? '' : 'class="' . $arr_param_campo['class'] . '"') . '>');
				array_push($tabla, $this->formato_reporte($registro[$campo], $arr_param_campo, $registro, $campo));
				array_push($tabla, '</td>');

				if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
				{
					$totales[$campo] += $registro[$campo];
				}

				if (in_array($arr_param_campo['tipo'], $arr_campos_totalizados))
				{
					$subtotales[$campo] += $registro[$campo];
				}
			}

			array_push($tabla, '</tr>');
		}

		//ultima linea de subtotales
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			if ($arr_param_campo['tipo'] === 'subtotal')
			{
				array_push($tabla, '<td><span class="text-muted">' . ++$n_linea . '</span></td>');
				foreach ($arr_campos as $c => $arr_c)
				{
					array_push($tabla, '<td ' . (($arr_c === '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"') . '>');
					array_push($tabla, '<strong>');
					array_push($tabla, in_array($arr_c['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($subtotales[$c], $arr_c) : '');
					array_push($tabla, '</strong>');
					array_push($tabla, '</td>');
				}

				array_push($tabla, '</tr>');
				array_push($tabla, '<tr>');
				array_push($tabla, '<td colspan="' . (count($arr_campos) + 1) . '" class="subtotal">&nbsp;</td>');
				array_push($tabla, '</tr>');
			}
		}

		// totales
		array_push($tabla, '</tbody>');
		array_push($tabla, '<tfoot>');
		array_push($tabla, '<tr>');
		array_push($tabla, '<th></th>');
		foreach ($arr_campos as $campo => $arr_param_campo)
		{
			array_push($tabla, '<th ' . (($arr_param_campo === '') ? '' : 'class="subtotal ' . $arr_param_campo['class'] . '"') . '>');
			array_push($tabla, in_array($arr_param_campo['tipo'], $arr_campos_totalizados) ? $this->formato_reporte($totales[$campo], $arr_param_campo) : '');
			array_push($tabla, '</th>');
		}
		array_push($tabla, '</tr>');

		array_push($tabla, '</tfoot>');
		array_push($tabla, '</table>');
		array_push($tabla, '<script type="text/javascript" src="' . base_url() . 'js/reporte.js"></script>');

		return (implode('', $tabla));
	}






}
/* libraries app_common.php */
/* Location: ./application/libraries/app_common.php */