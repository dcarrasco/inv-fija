<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis extends CI_Controller {

	private $id_inventario = 0;
	private $nombre_inventario = '';


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('analisis');

		$inventario_activo = new Inv_activo;
		$this->id_inventario     = $inventario_activo->get_id_inventario_activo();
		$inventario_activo->find_id($this->id_inventario);

		$this->nombre_inventario = $inventario_activo->nombre;
	}

	/**
	 * accion por defecto del controlador
	 * @return [type]
	 */
	public function index() {
		$this->ajustes();
	}

	/**
	 * Despliega la página de ajustes de inventarios
	 * @param  integer $ocultar_regularizadas Oculta los regustros que están modificados
	 * @return nada
	 */
	public function ajustes($ocultar_regularizadas = 0)
	{

		// recupera el detalle de registros con diferencias
		$detalle_ajustes   = new Detalle_inventario;
		$detalle_ajustes->get_ajustes($this->id_inventario, $ocultar_regularizadas);

		if ($this->input->post('formulario') == 'ajustes')
		{
			foreach($detalle_ajustes->get_model_all() as $detalle)
			{
				$this->form_validation->set_rules('stock_ajuste_' . $detalle->id, 'cantidad', 'trim|integer');
				$this->form_validation->set_rules('observacion_'  . $detalle->id, 'observacion', 'trim');
			}
		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('integer', 'El valor del campo %s debe ser un numero entero');
		$this->form_validation->set_message('greater_than', 'El valor del campo %s debe ser positivo');

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'detalle_ajustes'       => $detalle_ajustes,
					'ocultar_regularizadas' => $ocultar_regularizadas,
					'msg_alerta'            => $this->session->flashdata('msg_alerta'),
					'menu_app'              => $this->acl_model->menu_app(),
				);

			$this->_render_view('ajustes', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'ajustes')
			{
				$cant_modif = 0;
				foreach($detalle_ajustes->get_model_all() as $detalle)
				{
					if (((int) set_value('stock_ajuste_' . $detalle->id)  != $detalle->stock_ajuste) or
						(trim(set_value('observacion_' . $detalle->id))   != trim($detalle->glosa_ajuste)) )
					{
						$detalle->stock_ajuste = (int) set_value('stock_ajuste_' . $detalle->id);
						$detalle->glosa_ajuste = trim(set_value('observacion_' . $detalle->id));
						$detalle->fecha_ajuste = date('Ymd H:i:s');
						$detalle->grabar();
						$cant_modif += 1;
					}

				}

				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente' : ''));
			}
			redirect('analisis/ajustes/' . $ocultar_regularizadas . '/' . time());
		}

	}


	public function sube_stock()
	{

		$upload_error = '';
		$script_carga = '';
		$regs_OK      = 0;
		$regs_error   = 0;
		$msj_error    = '';

		if ($this->input->post('formulario') == 'upload')
		{

			if ($this->input->post('password') == 'logistica2012')
			{
				$upload_config = array(
					'upload_path'   => './upload/',
					'allowed_types' => 'txt|cvs',
					'max_size'      => '2000',
					'file_name'     => 'sube_stock.txt',
					'overwrite'     => true,
					);
				$this->load->library('upload', $upload_config);

				if (! $this->upload->do_upload('upload_file'))
				{
					$upload_error = '<br><div class="error round">' . $this->upload->display_errors() . '</div>';
				}
				else
				{
					$upload_data = $this->upload->data();
					$archivo_cargado = $upload_data['full_path'];

					$inv_activo = new Inv_activo;
					$inv_activo->find_id($this->id_inventario);
					$inv_activo->borrar_detalle_inventario();

					$res_procesa_archivo = $this->_procesa_archivo_cargado($archivo_cargado);

					$script_carga = $res_procesa_archivo['script'];
					$regs_OK      = $res_procesa_archivo['regs_OK'];
					$regs_error   = $res_procesa_archivo['regs_error'];
					$msj_error    = ($regs_error > 0) ? '<br><div class="error round">' . $res_procesa_archivo['msj_termino'] . '</div>' : '';
				}
			}
		}

		$data = array(
				'inventario_id'     => $this->id_inventario,
				'inventario_nombre' => $this->nombre_inventario,
				'upload_error'      => $upload_error,
				'script_carga'      => $script_carga,
				'regs_OK'           => $regs_OK,
				'regs_error'        => $regs_error,
				'msj_error'         => $msj_error,
				'link_config'       => 'config',
				'link_reporte'      => 'reportes',
				'link_inventario'   => 'inventario',
				'msg_alerta'        => $this->session->flashdata('msg_alerta'),
			);

		$this->_render_view('sube_stock', $data);

	}


	private function _procesa_archivo_cargado($archivo = '')
	{
		$count_OK    = 0;
		$count_error = 0;
		$num_linea   = 0;
		$c           = 0;
		$arr_lineas_error = array();
		$arr_bulk_insert  = array();
		$script_carga = '';
		$fh = fopen($archivo, 'r');
		if ($fh)
		{
			while ($linea = fgets($fh))
			{
				$c += 1;
				$num_linea += 1;
				$resultado_procesa_linea = $this->_procesa_linea($linea);
				if ($resultado_procesa_linea == 'no_procesar')
				{

				}
				elseif ($resultado_procesa_linea == 'error')
				{
					$count_error += 1;
					array_push($arr_lineas_error, $num_linea);
				}
				else
				{
					$count_OK += 1;
					if ($resultado_procesa_linea != '')
					{
						$script_carga .= 'proc_linea_carga(' . $c . ',' . $resultado_procesa_linea . ');' . "\n";
					}
				}
			}
			fclose($fh);
		}

		$msj_termino = 'Total lineas: ' . ($count_OK + $count_error) . ' (OK: ' . $count_OK . '; Error: ' . $count_error . ')';
		if ($count_error > 0)
		{
			$msj_termino .= '<br>Lineas con errores (';
			foreach ($arr_lineas_error as $key => $lin_error)
			{
				if ($key > 0)
				{
					$msj_termino .= ', ';
				}
				$msj_termino .= $lin_error;
			}
			$msj_termino .= ')';
		}

		return array('script' => $script_carga, 'regs_OK' => $count_OK, 'regs_error' => $count_error, 'msj_termino' => $msj_termino);

	}


	private function _procesa_linea($linea = '')
	{
		$arr_linea = explode("\r", $linea);
		if ($arr_linea[0] != '')
		{
			$arr_datos = explode("\t", $arr_linea[0]);

			if (count($arr_datos) == 10)
			{
				$ubicacion   = trim(str_replace("'", '"', $arr_datos[0]));
				$hu          = trim(str_replace("'", '"', $arr_datos[1]));
				$catalogo    = trim(str_replace("'", '"', $arr_datos[2]));
				$descripcion = trim(str_replace("'", '"', $arr_datos[3]));
				$lote        = trim(str_replace("'", '"', $arr_datos[4]));
				$centro      = trim(str_replace("'", '"', $arr_datos[5]));
				$almacen     = trim(str_replace("'", '"', $arr_datos[6]));
				$um          = trim(str_replace("'", '"', $arr_datos[7]));
				$stock_sap   = trim($arr_datos[8]);
				$hoja        = trim($arr_datos[9]);

				if ($ubicacion == 'UBICACION' or $catalogo == 'CATALOGO'  or $centro    == 'CENTRO' or
					$almacen   == 'ALMACEN'   or $lote     == 'LOTE'      or $um        == 'UM'     or
					$stock_sap == 'STOCK_SAP' or $hoja     == 'HOJA'      or $hu        == 'HU')
				{
					// cabecera del archivo, no se hace nada
					return 'no_procesar';
				}
				else
				{
					if (is_numeric($stock_sap) and is_numeric($hoja))
					{
						return (
							'0,' .
							$this->id_inventario . ',' .
							$hoja                . ',' .
							'0,' .
							'0,' .
							'\'' . $ubicacion    . '\',' .
							'\'' . $hu            . '\',' .
							'\'' . $catalogo     . '\',' .
							'\'' . $descripcion  . '\',' .
							'\'' . $lote         . '\',' .
							'\'' . $centro       . '\',' .
							'\'' . $almacen      . '\',' .
							'\'' . $um           . '\',' .
							$stock_sap           . ',' .
							'0,' .
							'\'\',' .
							'\'' . date('Ymd H:i:s') . '\',' .
							'\'\''
							);

					}
					else
					{
						// error: stock y/o hoja no son numericos
						return 'error';
					}
				}

			}
			else
			{
				// error: linea con cantidad de campos <> 9
				return 'error';
			}
		}
		else
		{
			// no error: linea en blanco
			return 'no_procesar';
		}
	}


	public function inserta_linea_archivo()
	{
		$detalle = new Detalle_inventario;
		$detalle->id_inventario      = $this->input->post('id_inv');
		$detalle->hoja               = $this->input->post('hoja');
		$detalle->ubicacion          = $this->input->post('ubic');
		$detalle->hu                 = $this->input->post('hu');
		$detalle->catalogo           = $this->input->post('cat');
		$detalle->descripcion        = $this->input->post('desc');
		$detalle->lote               = $this->input->post('lote');
		$detalle->centro             = $this->input->post('cen');
		$detalle->almacen            = $this->input->post('alm');
		$detalle->um                 = $this->input->post('um');
		$detalle->stock_sap          = $this->input->post('ssap');
		$detalle->stock_fisico       = $this->input->post('sfis');
		$detalle->digitador          = $this->input->post('aud');
		$detalle->auditor            = $this->input->post('dig');
		$detalle->reg_nuevo	         = $this->input->post('nvo');
		$detalle->fecha_modificacion = $this->input->post('fec');
		$detalle->observacion        = $this->input->post('obs');
		$detalle->stock_ajuste       = 0;
		$detalle->glosa_ajuste       = '';
		$detalle->grabar();
	}



	public function imprime_inventario($id_inventario = 0)
	{
		$inv_activo = new Inv_activo;
		$inv_activo->find_id($this->id_inventario);

		$this->form_validation->set_rules('pag_desde', 'Pagina Desde', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('pag_hasta', 'Pagina Hasta', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('oculta_stock_sap', 'Oculta Stock SAP', '');
		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para [%s]');
		$this->form_validation->set_message('greater_than', 'El valor del campo [%s] debe ser mayor o igual a 1');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'inventario_id'     => $this->id_inventario,
					'inventario_nombre' => $this->nombre_inventario,
					'max_hoja'          => $inv_activo->get_max_hoja_inventario(),
					'link_config'       => 'config',
					'link_reporte'      => 'reportes',
					'link_inventario'   => 'inventario',
					'msg_alerta'        => $this->session->flashdata('msg_alerta'),
				);

			$this->_render_view('imprime_inventario', $data);
		}
		else
		{
			$oculta_stock_sap = (set_value('oculta_stock_sap') == 'oculta_stock_sap') ? 1 : 0;
			redirect("analisis/imprime_hojas/" . set_value('pag_desde') . '/' . set_value('pag_hasta') . '/' . $oculta_stock_sap . '/' . time());
		}

	}


	public function imprime_hojas($hoja_desde = 0, $hoja_hasta = 0, $oculta_stock_sap = 0)
	{

		$detalle = new Detalle_inventario;

		if ($hoja_hasta == 0 or $hoja_hasta < $hoja_desde)
		{
			$hoja_hasta = $hoja_desde;
		}


		$this->load->view('inventario/inventario_print_head');

		for($hoja = $hoja_desde; $hoja <= $hoja_hasta; $hoja++)
		{
			$detalle->find('all', array('conditions' => array('id_inventario' => $this->id_inventario, 'hoja' => $hoja)));
			$data = array(
					'datos_hoja'        => $detalle->get_model_all(),
					'oculta_stock_sap'  => $oculta_stock_sap,
					'hoja'              => $hoja,
					'nombre_inventario' => $this->nombre_inventario,
				);

			$this->load->view('inventario/inventario_print_body', $data);
		}

		$this->load->view('inventario/inventario_print_footer');
	}


	private function _menu_ajustes($arr_menu, $op_menu)
	{
		$menu = '<ul>';
		foreach($arr_menu as $key => $val)
		{
			$selected = ($key == $op_menu) ? ' class="selected"' : '';
			$menu .= '<li' . $selected . '>' . anchor($val['url'], $val['texto']) . '</li>';
		}
		$menu .= '</ul>';
		return $menu;
	}


	private function _render_view($vista = '', $data = array())
	{

		$arr_menu = array(
				'ajustes'            => array('url' => 'analisis/ajustes', 'texto' => 'Ajustes de inventario'),
				'sube_stock'         => array('url' => 'analisis/sube_stock', 'texto' => 'Subir Stock'),
				'imprime_inventario' => array('url' => 'analisis/imprime_inventario', 'texto' => 'Imprimir Hojas'),
			);

		$data['titulo_modulo'] = 'Ajustes de inventario: ' . $this->nombre_inventario;
		$data['menu_app'] = $this->acl_model->menu_app();
		$data['menu_ajustes'] = $this->_menu_ajustes($arr_menu, $vista);
		$this->load->view('app_header', $data);
		$this->load->view('inventario/' . $vista, $data);
		$this->load->view('app_footer', $data);
	}



}

/* End of file analisis.php */
/* Location: ./application/controllers/inventario.php */
