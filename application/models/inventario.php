<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_inventarios'),
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
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           => 'boolean',
					'texto_ayuda'    => 'Indica se el inventario est&aacute; activo dentro del sistema.',
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

		$this->config_model($cfg);

		if ($id)
		{
			$this->fill($id);
		}
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->nombre;
	}


	// --------------------------------------------------------------------

	/**
	 * Graba un inventario
	 * Si tiene marca activo, elimina esta marca del resto de los inventarios
	 *
	 * @return none
	 */
	public function grabar()
	{
		parent::grabar();

		if ($this->activo)
		{
			$this->CI->db
				->where('id <>', (int) $this->id)
				->update($this->get_model_tabla(), array('activo' => 0));
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve ID del inventario activo
	 *
	 * @return integer ID del inventario activo
	 */
	public function get_id_inventario_activo()
	{
		$this->find('first', array('conditions' => array('activo' => 1)));

		return (int) $this->get_model_id();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el numero de la mayor hoja del inventario
	 *
	 * @return integer Numero de la hoja mayor
	 */
	public function get_max_hoja_inventario()
	{
		$rs = $this->CI->db
			->select('max(hoja) as max_hoja')
			->get_where($this->CI->config->item('bd_detalle_inventario'), array('id_inventario' => $this->id))
			->row();

		return ($rs->max_hoja);
	}


	// --------------------------------------------------------------------

	/**
	 * Borra registros de detalle de inventario
	 *
	 * @return none
	 */
	public function borrar_detalle_inventario()
	{
		$this->CI->db->delete($this->CI->config->item('bd_detalle_inventario'), array('id_inventario' => $this->id));
	}


	// --------------------------------------------------------------------

	/**
	 * Carga a la BD los datos del archivo
	 *
	 * @param  string $archivo Nombre del archivo a cargar a la BD (full path)
	 * @return array           Estado de la carga del archivo
	 */
	public function cargar_datos_archivo($archivo = '')
	{
		$count_OK    = 0;
		$count_error = 0;
		$num_linea   = 0;

		$arr_lineas_error = array();
		$arr_bulk_insert  = array();
		$script_carga     = '';

		foreach(file($archivo) as $linea)
		{
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
				if (is_array($resultado_procesa_linea))
				{
					$resultado_procesa_linea['count'] = $num_linea;
					$script_carga .= 'subeStock.proc_linea_carga(' . json_encode($resultado_procesa_linea). ");\n";
				}
			}
		}

		$msj_termino = 'Total lineas: ' . ($count_OK + $count_error) . ' (OK: ' . $count_OK . '; Error: ' . $count_error . ')';

		if ($count_error > 0)
		{
			$msj_termino .= '<br>Lineas con errores (' . implode(', ', $arr_lineas_error) . ')';
		}

		return array('script' => $script_carga, 'regs_OK' => $count_OK, 'regs_error' => $count_error, 'msj_termino' => $msj_termino);
	}


	// --------------------------------------------------------------------

	private function _procesa_linea($linea = '')
	{
		$linea = utf8_encode(trim($linea, "\r\n"));
		$linea = str_replace("'", '"', $linea);

		if ($linea != '')
		{
			$arr_datos = explode("\t", $linea);

			if (count($arr_datos) == 9) // igual a 10 en caso de tener HU
			{
				$arr_datos = array_combine(
					array('ubicacion', 'catalogo', 'descripcion', 'lote', 'centro', 'almacen', 'um', 'stock_sap', 'hoja'),
					$arr_datos
				);
				extract($arr_datos);

				if (strtoupper($ubicacion) == 'UBICACION' OR strtoupper($catalogo) == 'CATALOGO' OR
					strtoupper($descripcion) == 'DESCRIPCION' OR
					strtoupper($centro)    == 'CENTRO'    OR strtoupper($almacen)  == 'ALMACEN'  OR
					strtoupper($lote)      == 'LOTE'      OR strtoupper($um)       == 'UM'       OR
					strtoupper($stock_sap) == 'STOCK_SAP' OR strtoupper($hoja)     == 'HOJA') // OR $hu        == 'HU'
				{
					// cabecera del archivo, no se hace nada
					return 'no_procesar';
				}
				else
				{
					if (is_numeric($stock_sap) and is_numeric($hoja))
					{
						return (array(
							$this->CI->security->get_csrf_token_name() => $this->CI->security->get_csrf_hash(),
							'id'                 => 0,
							'id_inventario'      => $this->id,
							'hoja'               => $hoja,
							'ubicacion'          => $ubicacion,
							'hu'                 => '',
							'catalogo'           => $catalogo,
							'descripcion'        => $descripcion,
							'lote'               => $lote,
							'centro'             => $centro,
							'almacen'            => $almacen,
							'um'                 => $um,
							'stock_sap'          => $stock_sap,
							'stock_fisico'       => 0,
							'digitador'          => 0,
							'auditor'            => 0,
							'observacion'        => '',
							'fecha_modificacion' => date('Ymd His'),
							'reg_nuevo'          => '',
							'stock_ajuste'       => 0,
							'glosa_ajuste'       => '',
						));

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
