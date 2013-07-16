<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario extends ORM_Model {

	public function __construct()
	{
		$cfg = array(
				'modelo' => array(
						'model_tabla'        => 'fija_inventarios',
						'model_label'        => 'Inventario',
						'model_label_plural' => 'Inventarios',
						'model_order_by'     => 'nombre',
					),
				'campos' => array(
						'id' => array(
								'tipo'   => 'id',
							),
						'nombre' => array(
								'label'          => 'Nombre del inventario',
								'tipo'           => 'char',
								'largo'          => 50,
								'texto_ayuda'    => 'Maximo 50 caracteres.',
								'es_obligatorio' => TRUE,
								'es_unico'       => TRUE
							),
						'activo' => array(
								'label'          => 'Activo',
								'tipo'           =>  'boolean',
								'texto_ayuda'    => 'Indica se el inventario esta activo dentro del sistema.',
								'es_obligatorio' => TRUE,
							),
						'tipo_inventario' => array(
								'tipo'           =>  'has_one',
								'relation'       => array(
										'model' => 'tipo_inventario'
									),
								'texto_ayuda'    => 'Seleccione el tipo de inventario.',
								'es_obligatorio' => TRUE,
							),
						),
					);
		parent::__construct($cfg);
	}

	public function __toString()
	{
		return $this->nombre;
	}

	public function grabar()
	{
		parent::grabar();
		if ($this->activo)
		{
			$this->db->update($this->get_model_tabla(), array('activo' => 0), 'id<>' . $this->id);
		}
	}

	public function get_id_inventario_activo()
	{
		$this->find('first', array('conditions' => array('activo' => 1)));
		return (int) $this->get_model_id();
	}

	public function get_max_hoja_inventario()
	{
		$rs = $this->db->select('max(hoja) as max_hoja')->get_where('fija_detalle_inventario', array('id_inventario' => $this->id))->row_array();
		return ($rs['max_hoja']);
	}

	public function borrar_detalle_inventario()
	{
		$this->db->delete('fija_detalle_inventario', array('id_inventario' => $this->id));
	}


	/**
	 * Carga a la BD los datos del archivo
	 * @param  string $archivo Nombre del archivo a cargar a la BD (full path)
	 * @return array           Estado de la carga del archivo
	 */
	public function cargar_datos_archivo($archivo = '')
	{
		$count_OK    = 0;
		$count_error = 0;
		$num_linea   = 0;
		$c           = 0;

		$arr_lineas_error = array();
		$arr_bulk_insert  = array();
		$script_carga     = '';

		ini_set("auto_detect_line_endings", TRUE);
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
					// no se procesa esta linea
				}
				else if ($resultado_procesa_linea == 'error')
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
				$msj_termino .= $lin_error . (($key > 0) ? ', ' : '');
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

			if (count($arr_datos) == 9) // igual a 10 en caso de tener HU
			{
				$ubicacion   = trim(str_replace("'", '"', $arr_datos[0]));
				//$hu          = trim(str_replace("'", '"', $arr_datos[1]));
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
					$stock_sap == 'STOCK_SAP' or $hoja     == 'HOJA') // or $hu        == 'HU'
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
							$this->id . ',' .
							$hoja                . ',' .
							'0,' .
							'0,' .
							'\'' . $ubicacion    . '\',' .
							// '\'' . $hu           . '\',' .
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


}

/* End of file inventario.php */
/* Location: ./application/models/inventario.php */
