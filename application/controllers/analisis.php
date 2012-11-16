<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis extends CI_Controller {

	private $id_inventario = 0;


	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('analisis');

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
		$this->load->model('usuario');
		$this->load->model('inventario_model');
		$this->load->model('catalogo');

		// recupera el inventario activo
		$this->id_inventario = $this->inventario_model->get_id_inventario_activo();

		$nombre_inventario = $this->inventario_model->get_nombre_inventario($this->id_inventario);
		$datos_hoja        = $this->inventario_model->get_ajustes($this->id_inventario, $ocultar_regularizadas);

		if ($this->input->post('formulario') == 'ajustes')
		{
			foreach($datos_hoja as $reg)
			{
				$this->form_validation->set_rules('stock_ajuste_' . $reg['id'], 'cantidad', 'trim|numeric');
				$this->form_validation->set_rules('observacion_' . $reg['id'], 'observacion', 'trim');
			}
		}

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('numeric', 'El valor del campo %s debe ser numerico');
		$this->form_validation->set_message('greater_than', 'El valor del campo %s debe ser positivo');

		$msg_alerta = '';

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'datos_hoja'            => $datos_hoja,
					'id_inventario'         => $this->id_inventario,
					'nombre_inventario'     => $nombre_inventario,
					'ocultar_regularizadas' => $ocultar_regularizadas,
					'msg_alerta'            => $this->session->flashdata('msg_alerta'),
					'menu_app'              => $this->acl_model->menu_app(),
				);

			$data['titulo_modulo'] = 'Ajustes de inventario';
			$this->load->view('app_header', $data);
			$this->load->view('ajustes', $data);
			$this->load->view('app_footer', $data);
		}
		else
		{
			if ($this->input->post('formulario') == 'ajustes')
			{
				$cant_modif = 0;
				foreach($datos_hoja as $reg)
				{
					if (((int) set_value('stock_ajuste_' . $reg['id']) != $reg['stock_ajuste']) or
						(trim(set_value('observacion_' . $reg['id']))   != trim($reg['glosa_ajuste'])) )
					{
						$this->inventario_model->guardar_ajuste(
															$reg['id'],
															set_value('stock_ajuste_' . $reg['id']),
															trim(set_value('observacion_' . $reg['id'])),
															date('Y-d-m H:i:s')
														);
						$cant_modif += 1;
					}

				}

				$this->session->set_flashdata('msg_alerta', (($cant_modif > 0) ? $cant_modif . ' linea(s) modificadas correctamente' : ''));
			}
			redirect('analisis/ajustes/' . $ocultar_regularizadas . '/' . time());
		}

	}


	public function sube_stock($id_inventario = 0)
	{
		$this->load->model('inventario_model');

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

					$this->inventario_model->borrar_inventario($id_inventario);

					$res_procesa_archivo = $this->_procesa_archivo_cargado($id_inventario, $archivo_cargado);

					$script_carga = $res_procesa_archivo['script'];
					$regs_OK      = $res_procesa_archivo['regs_OK'];
					$regs_error   = $res_procesa_archivo['regs_error'];
					$msj_error    = ($regs_error > 0) ? '<br><div class="error round">' . $res_procesa_archivo['msj_termino'] . '</div>' : '';
				}
			}
		}

		$data = array(
				'inventario_id'     => $id_inventario,
				'inventario_nombre' => $this->inventario_model->get_nombre_inventario($id_inventario),
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


	private function _procesa_archivo_cargado($id_inventario = '', $archivo = '')
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
				$resultado_procesa_linea = $this->_procesa_linea($id_inventario, $linea);
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


	private function _procesa_linea($id_inventario = '', $linea = '')
	{
		$arr_linea = explode("\r", $linea);
		if ($arr_linea[0] != '')
		{
			$arr_datos = explode("\t", $arr_linea[0]);

			if (count($arr_datos) == 9)
			{
				$ubicacion   = trim(str_replace("'", '"', $arr_datos[0]));
				$catalogo    = trim(str_replace("'", '"', $arr_datos[1]));
				$descripcion = trim(str_replace("'", '"', $arr_datos[2]));
				$lote        = trim(str_replace("'", '"', $arr_datos[3]));
				$centro      = trim(str_replace("'", '"', $arr_datos[4]));
				$almacen     = trim(str_replace("'", '"', $arr_datos[5]));
				$um          = trim(str_replace("'", '"', $arr_datos[6]));
				$stock_sap   = trim($arr_datos[7]);
				$hoja        = trim($arr_datos[8]);

				if ($ubicacion == 'UBICACION' or $catalogo == 'CATALOGO' or $centro    == 'CENTRO' or
					$almacen   == 'ALMACEN'   or $lote     == 'LOTE'     or $um        == 'UM'     or
					$stock_sap == 'STOCK_SAP' or $hoja     == 'HOJA')
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
							$id_inventario      . ',' .
							$hoja               . ',' .
							'0,' .
							'0,' .
							'\'' . $ubicacion   . '\',' .
							'\'' . $catalogo    . '\',' .
							'\'' . $descripcion . '\',' .
							'\'' . $lote        . '\',' .
							'\'' . $centro      . '\',' .
							'\'' . $almacen     . '\',' .
							'\'' . $um          . '\',' .
							$stock_sap          . ',' .
							'0,' .
							'\'\',' .
							'\'' . date('Y-d-m H:i:s') . '\',' .
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
		$this->load->model('inventario_model');
		$this->inventario_model->guardar(
			$this->input->post('id'),
			$this->input->post('id_inv'),
			$this->input->post('hoja'),
			$this->input->post('aud'),
			$this->input->post('dig'),
			$this->input->post('ubic'),
			$this->input->post('cat'),
			$this->input->post('desc'),
			$this->input->post('lote'),
			$this->input->post('cen'),
			$this->input->post('alm'),
			$this->input->post('um'),
			$this->input->post('ssap'),
			$this->input->post('sfis'),
			$this->input->post('obs'),
			$this->input->post('fec'),
			$this->input->post('nvo')
			);


	}



	public function imprime_inventario($id_inventario = 0)
	{
		$this->load->model('inventario_model');

		$this->form_validation->set_rules('pag_desde', 'Pagina Desde', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('pag_hasta', 'Pagina Hasta', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('oculta_stock_sap', 'Oculta Stock SAP', '');
		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para [%s]');
		$this->form_validation->set_message('greater_than', 'El valor del campo [%s] debe ser mayor o igual a 1');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
					'inventario_id'     => $id_inventario,
					'inventario_nombre' => $this->inventario_model->get_nombre_inventario($id_inventario),
					'max_hoja'          => $this->inventario_model->get_max_hoja_inventario($id_inventario),
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

		if ($hoja_hasta == 0 or $hoja_hasta < $hoja_desde)
		{
			$hoja_hasta = $hoja_desde;
		}

		$this->load->model('inventario_model');

		// recupera el inventario activo
		$this->id_inventario = $this->inventario_model->get_id_inventario_activo();
		$nombre_inventario = $this->inventario_model->get_nombre_inventario($this->id_inventario);

		$this->load->view('inventario_print_head');

		for($hoja = $hoja_desde; $hoja <= $hoja_hasta; $hoja++)
		{
			$datos_hoja        = $this->inventario_model->get_hoja($this->id_inventario, $hoja);
			$data = array(
					'datos_hoja'        => $datos_hoja,
					'oculta_stock_sap'  => $oculta_stock_sap,
					'hoja'              => $hoja,
					'nombre_inventario' => $nombre_inventario,
				);

			$this->load->view('inventario_print_body', $data);
		}

		$this->load->view('inventario_print_footer');

	}




	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Ajustes de inventario';
		$data['menu_app'] = $this->acl_model->menu_app();
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}



}

/* End of file analisis.php */
/* Location: ./application/controllers/inventario.php */
