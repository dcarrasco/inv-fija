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
 * Clase Modelo Inventario
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_inventario Identificador del inventario
	 * @return void
	 */
	public function __construct($id_inventario = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_inventarios'),
				'model_label'        => 'Inventario',
				'model_label_plural' => 'Inventarios',
				'model_order_by'     => 'nombre',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => Orm_field::TIPO_ID,
				),
				'nombre' => array(
					'label'          => 'Nombre del inventario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           => Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica se el inventario est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
				),
				'tipo_inventario' => array(
					'tipo'           =>  Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model' => 'tipo_inventario'
					),
					'texto_ayuda'    => 'Seleccione el tipo de inventario.',
					'es_obligatorio' => TRUE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_inventario)
		{
			$this->fill($id_inventario);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Inventario
	 */
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
			$this->db
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
	 * Carga el registro del inventario activo
	 *
	 * @return void
	 */
	public function get_inventario_activo()
	{
		$inventario_activo = $this->get_id_inventario_activo();
		$this->find_id($inventario_activo);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el numero de la mayor hoja del inventario
	 *
	 * @return integer Numero de la hoja mayor
	 */
	public function get_max_hoja_inventario()
	{
		$registro = $this->db
			->select('max(hoja) as max_hoja')
			->get_where($this->config->item('bd_detalle_inventario'), array('id_inventario' => $this->id))
			->row();

		return ($registro->max_hoja);
	}


	// --------------------------------------------------------------------

	/**
	 * Borra registros de detalle de inventario
	 *
	 * @return none
	 */
	public function borrar_detalle_inventario()
	{
		$this->db->delete($this->config->item('bd_detalle_inventario'), array('id_inventario' => $this->id));
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
		$count_ok    = 0;
		$count_error = 0;
		$num_linea   = 0;

		$arr_lineas_error = array();
		$arr_bulk_insert  = array();
		$script_carga     = '';

		foreach(file($archivo) as $linea)
		{
			$num_linea += 1;
			$resultado_procesa_linea = $this->_procesa_linea($linea);

			if ($resultado_procesa_linea === 'no_procesar')
			{
				// no se procesa esta linea
			}
			else if ($resultado_procesa_linea === 'error')
			{
				$count_error += 1;
				array_push($arr_lineas_error, $num_linea);
			}
			else
			{
				$count_ok += 1;
				if (is_array($resultado_procesa_linea))
				{
					$resultado_procesa_linea['count'] = $num_linea;
					$script_carga .= 'subeStock.proc_linea_carga(' . json_encode($resultado_procesa_linea). ");\n";
				}
			}
		}

		$msj_termino = 'Total lineas: ' . ($count_ok + $count_error) . ' (OK: ' . $count_ok . '; Error: ' . $count_error . ')';

		if ($count_error > 0)
		{
			$msj_termino .= '<br>Lineas con errores (' . implode(', ', $arr_lineas_error) . ')';
		}

		return array('script' => $script_carga, 'regs_ok' => $count_ok, 'regs_error' => $count_error, 'msj_termino' => $msj_termino);
	}


	// --------------------------------------------------------------------

	/**
	 * Procesa una linea de texto con datos de inventario y devuelve un string con la informacion
	 *
	 * @param  string $linea Linea de texto con informacion de inventario
	 * @return mixed        Arreglo con información de inventario
	 */
	private function _procesa_linea($linea = '')
	{
		$linea = utf8_encode(trim($linea, "\r\n"));
		$linea = str_replace("'", '"', $linea);

		// no error: linea en blanco
		if ($linea === '')
		{
			return 'no_procesar';
		}

		$arr_datos = explode("\t", $linea);

		// error: linea con cantidad de campos <> 9
		if (count($arr_datos) !== 9) // igual a 10 en caso de tener HU
		{
			return 'error';
		}

		$arr_datos = array_combine(
			array('ubicacion', 'catalogo', 'descripcion', 'lote', 'centro', 'almacen', 'um', 'stock_sap', 'hoja'),
			$arr_datos
		);
		extract($arr_datos);

		if (strtoupper($ubicacion) === 'UBICACION' OR strtoupper($catalogo) === 'CATALOGO' OR
			strtoupper($descripcion) === 'DESCRIPCION' OR
			strtoupper($centro) === 'CENTRO' OR strtoupper($almacen) === 'ALMACEN' OR
			strtoupper($lote) === 'LOTE' OR strtoupper($um) === 'UM' OR
			strtoupper($stock_sap) === 'STOCK_SAP' OR strtoupper($hoja) === 'HOJA') // OR $hu === 'HU'
		{
			// cabecera del archivo, no se hace nada
			return 'no_procesar';
		}
		else
		{
			if (is_numeric($stock_sap) AND is_numeric($hoja))
			{
				return (array(
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
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
					'fecha_modificacion' => date('Y-m-d H:i:s'),
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



}
/* End of file inventario.php */
/* Location: ./application/models/inventario.php */
