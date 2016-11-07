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
 * Clase Modelo Analisis de series
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Analisis_series_model extends CI_Model {

	/**
	 * Arreglo de validacion módulo analisis de series
	 *
	 * @var array
	 */
	public $validation_analisis = array(
		array('field' => 'series', 'label' => 'Series', 'rules' => 'trim|required'),
		array('field' => 'show_mov', 'label' => 'Mostrar Movimientos', 'rules' => ''),
		array('field' => 'ult_mov', 'label' => 'Ultimo movimiento', 'rules' => ''),
		array('field' => 'show_despachos', 'label' => 'Mostrar despachos', 'rules' => ''),
		array('field' => 'show_stock_sap', 'label' => 'Mostrar stock SAP', 'rules' => ''),
		array('field' => 'show_stock_scl', 'label' => 'Mostrar stock SCL', 'rules' => ''),
		array('field' => 'show_trafico', 'label' => 'Mostrar trafico', 'rules' => ''),
		array('field' => 'show_gdth', 'label' => 'Mostrar gestor DTH', 'rules' => ''),
	);



	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('table');
		$this->table->set_template(array(
			'table_open' => '<table class="table table-bordered table-striped table-hover table-condensed reporte" style="white-space:nowrap;">',
		));

	}


	// --------------------------------------------------------------------

	/**
	 * Toma un listado de series y lo convierte en arreglo, de acuerdo a un formato especifico
	 *
	 * @param  string $series Listado de series a convertir
	 * @param  string $tipo   Formato de salida de las series
	 * @return array           Arreglo con las series con formato
	 */
	private function _series_list_to_array($series = string, $tipo = 'SAP')
	{
		$arr_series = array();
		$series = str_replace(' ', '', $series);
		$arr_series = preg_grep('/[\d]+/', explode("\r\n", $series));

		foreach ($arr_series as $llave => $valor)
		{
			$serie_temp = $valor;
			// Modificaciones de formato SAP
			if ($tipo === 'SAP')
			{
				$serie_temp = preg_replace('/^01/', '1', $serie_temp);
				$arr_series[$llave] = (strlen($serie_temp) === '19') ? substr($serie_temp, 1, 18) : $serie_temp;
			}
			// Modificaciones de formato SCL
			else if ($tipo === 'trafico')
			{
				$serie_temp = preg_replace('/^1/', '01', $serie_temp);
				$arr_series[$llave] = substr($serie_temp, 0, 14) . '0';
			}
			else if ($tipo === 'SCL')
			{
				$serie_temp = preg_replace('/^1/', '01', $serie_temp);
				$arr_series[$llave] = $serie_temp;
			}
		}

		return $arr_series;
	}


	// --------------------------------------------------------------------

	/**
	 * Toma una serie y le calcula el DV
	 *
	 * @param  string $serie Serie a calcular el DV
	 * @return string        Serie con DV
	 */
	private function _get_dv_imei($serie = '')
	{
		$serie15 = '';

		if (strlen($serie) === 14 AND substr($serie, 0, 1) === '1')
		{
			$serie15 = '0' . $serie;
		}

		if (strlen($serie) === 15)
		{
			$serie15 = $serie;
		}

		$serie14 = substr($serie15, 0, 14);

		$sum_dv = 0;
		foreach(str_split($serie14) as $indice => $numero)
		{
			$sum_dv += ($indice %2 !== 0) ? (($numero*2>9) ? $numero*2-9 : $numero*2) : $numero;
		}

		return $serie14 . (10 - $sum_dv % 10);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la historia de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_historia($series = '')
	{
		$reporte = '';

		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			foreach($arr_series as $serie)
			{
				$reporte .= $this->table->generate($this->_get_historia_serie($serie));
			}
		}

		return $reporte;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la historia de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	private function _get_historia_serie($serie = '')
	{
		return $this->db
			->limit(3000)
			->select('m.serie')
			->select('convert(varchar(20),fec_entrada_doc,120) as fecha_entrada_doc', FALSE)
			->select('m.ce as centro')
			->select('m.alm')
			->select('a1.des_almacen as desc_almacen')
			->select('m.rec')
			->select('a2.des_almacen as desc_rec')
			->select('m.cmv')
			->select('c.des_cmv as desc_cmv')
			->select('m.codigo_sap')
			->select('m.texto_breve_material')
			->select('m.lote')
			->select('m.n_doc')
			->select('m.referencia')
			->select('m.usuario')
			->select('u.nom_usuario')
			->from($this->config->item('bd_movimientos_sap').' m')
			->join($this->config->item('bd_cmv_sap').' c', 'm.cmv=c.cmv', 'left')
			->join($this->config->item('bd_almacenes_sap').' a1', 'a1.centro=m.ce and m.alm=a1.cod_almacen', 'left')
			->join($this->config->item('bd_almacenes_sap').' a2', 'a2.centro=m.ce and m.rec=a2.cod_almacen', 'left')
			->join($this->config->item('bd_usuarios_sap').' u', 'm.usuario=u.usuario', 'left')
			->where('serie', $serie)
			->order_by('serie', 'asc')
			->order_by('fecha', 'asc')
			->order_by('fec_entrada_doc', 'asc')
			->get();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los despachos de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_despacho($series = string)
	{
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			return $this->table->generate(
				$this->db
					->limit(1000)
					->select('serie')
					->select('cod_sap as codigo_sap')
					->select('texto_breve_material')
					->select('lote')
					->select('operador')
					->select('convert(varchar(20),fecha,120) as fecha_despacho', FALSE)
					->select('cmv')
					->select('alm')
					->select('rec')
					->select('des_bodega')
					->select('rut')
					->select('tipo_servicio')
					->select('icc')
					->select('abonado')
					->select('n_doc')
					->select('referencia')
					->from($this->config->item('bd_despachos_sap'))
					->where_in('n_serie', $arr_series)
					->get()
			);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el stock SAP de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_stock_sap($series = string)
	{
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			return $this->table->generate(
				$this->db
					->limit(1000)
					->select('convert(varchar(20), s.fecha_stock, 103) as fecha_stock', FALSE)
					->select('s.serie')
					->select('s.material')
					->select('m.des_articulo as desc_material')
					->select('s.centro')
					->select('s.almacen')
					->select('a.des_almacen as desc_almacen')
					->select('s.lote')
					->select('s.status_sistema')
					->select('s.estado_stock')
					->select('convert(varchar(20), modificado_el, 103) as modif_el', FALSE)
					->select('s.modificado_por')
					->select('u.nom_usuario')
					->from($this->config->item('bd_stock_seriado_sap') . ' s')
					->join($this->config->item('bd_materiales_sap') . ' m', 's.material = m.cod_articulo', 'left')
					->join($this->config->item('bd_almacenes_sap') . ' a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
					->join($this->config->item('bd_usuarios_sap') . ' u', 's.modificado_por=u.usuario', 'left')
					->where_in('serie', $arr_series)
					->get()
			);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el stock SCL de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_stock_scl($series = string)
	{
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			$query = $this->db
				->limit(1000)
				->select('convert(varchar(20), s.fecha_stock, 103) as fecha', FALSE)
				->select('s.serie_sap')
				->select('s.cod_bodega')
				->select('b.des_bodega')
				->select('s.tip_bodega')
				->select('t.des_tipbodega')
				->select('s.cod_articulo')
				->select('a.des_articulo')
				->select('s.tip_stock')
				->select('ts.desc_stock')
				->select('s.cod_uso')
				->select('u.desc_uso')
				->select('s.cod_estado')
				->select('e.des_estado')
				->from($this->config->item('bd_stock_scl') . ' s')
				->join($this->config->item('bd_al_bodegas') . ' b', 'cast(s.cod_bodega as varchar(10)) = b.cod_bodega', 'left')
				->join($this->config->item('bd_al_tipos_bodegas') . ' t', 'cast(s.tip_bodega as varchar(10)) = t.tip_bodega', 'left')
				->join($this->config->item('bd_materiales_sap') . ' a', 'cast(s.cod_articulo as varchar(10)) = a.cod_articulo', 'left')
				->join($this->config->item('bd_al_tipos_stock') . ' ts', 's.tip_stock = ts.tipo_stock', 'left')
				->join($this->config->item('bd_al_estados') . ' e', 's.cod_estado = e.cod_estado', 'left')
				->join($this->config->item('bd_al_usos') . ' u', 's.cod_uso = u.cod_uso', 'left')
				->where_in('serie_sap', $arr_series)
				->get_compiled_select();

			$query = str_replace('"', '', $query);

			return $this->table->generate($this->db->query($query));
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera ultimo trafico de un conjunto de series
	 *
	 * @param  string $series Listado de series a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_trafico($series = '')
	{
		$arr_series = $this->_series_list_to_array($series, 'trafico');

		if (count($arr_series) > 0)
		{
			$anomes = $this->db
				->select_max('(ano*100+mes)', 'anomes')
				->get($this->config->item('bd_trafico_dias_proc'))
				->row()
				->anomes;

			$ano = intval($anomes/100);
			$mes = $anomes - 100*$ano;

			return $this->table->generate(
				$this->db
					->select('t.ano')
					->select('t.mes')
					->select('t.imei')
					->select('t.celular')
					->select('t.seg_entrada')
					->select('t.seg_salida')
					->select('a.tipo')
					->select('a.cod_cliente')
					->select('c.num_ident')
					->select("c.nom_cliente+' '+c.ape1_cliente+' '+c.ape2_cliente as nombre_cliente")
					->select('a.cod_situacion')
					->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta', FALSE)
					->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja', FALSE)
					->select('b.des_causabaja')
					->from($this->config->item('bd_trafico_mes') . ' t')
					->join($this->config->item('bd_trafico_abocelamist') . ' a', 't.celular = a.num_celular', 'left')
					->join($this->config->item('bd_trafico_clientes') . ' c', 'a.cod_cliente = c.cod_cliente', 'left')
					->join($this->config->item('bd_trafico_causabaja') . ' b', 'a.cod_causabaja = b.cod_causabaja', 'left')
					->where(array('ano' => $ano, 'mes' => $mes))
					->where_in('imei', $arr_series)
					->order_by('imei, fec_alta')
					->get()
			);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con los meses de trafico
	 *
	 * @return array            Arreglo con resultado
	 */
	public function get_meses_trafico()
	{
		$resultado = array();

		$this->db
			->distinct()
			->select('(100*ano+mes) as mes', FALSE)
			->from($this->config->item('bd_trafico_dias_proc'))
			->order_by('mes desc');

		foreach($this->db->get()->result_array() as $registro)
		{
			$resultado[$registro['mes']] = substr($registro['mes'], 0, 4) . '-' . substr($registro['mes'], 4, 2);
		}

		return $resultado;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el trafico de una serie (imei/celular) para un conjunto de meses
	 *
	 * @param  string $series Listado de series a consultar
	 * @param  array  $meses  Arreglo de meses a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_trafico_mes($series = '', $meses = array())
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series, 'SCL');

		foreach ($arr_series as $serie)
		{
			$serie_orig = $serie;
			$serie_cero = substr($serie, 0, 14) . '0';

			foreach($meses as $mes)
			{
				$mes_ano = substr($mes, 0, 4);
				$mes_mes = substr($mes, 4, 2);

				$this->db->select('t.*, a.*, c.*, b.*')
					->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta', FALSE)
					->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja', FALSE)
					->from($this->config->item('bd_trafico_mes') . ' t')
					->join($this->config->item('bd_trafico_abocelamist') . ' a', 't.celular = a.num_celular', 'left')
					->join($this->config->item('bd_trafico_clientes') . ' c', 'a.cod_cliente = c.cod_cliente', 'left')
					->join($this->config->item('bd_trafico_causabaja') . ' b', 'a.cod_causabaja = b.cod_causabaja', 'left')
					//->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'imei'=> $serie, 'cod_situacion<>' => 'BAA'));
					->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'cod_situacion<>' => 'BAA'))
					->where_in('imei', array($serie_orig, $serie_cero))
					->order_by('imei, fec_alta');

				foreach($this->db->get()->result_array() as $registro)
				{
					$result[$serie][$registro['celular']][$mes] = $registro;
				}
			}
		}

		return $result;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el trafico de una serie (imei/celular) para un conjunto de meses
	 *
	 * @param  string $series Listado de series a consultar
	 * @param  array  $meses  Arreglo de meses a consultar
	 * @param  string $tipo   imei o celular a consultar
	 * @return array          Arreglo con resultado
	 */
	public function get_trafico_mes2($series = '', $str_meses = '', $tipo = 'imei')
	{
		$result = array();
		$arr_series = array();

		if ($series !== '')
		{
			$arr_series = $this->_series_list_to_array($series, ($tipo === 'imei') ? 'trafico' : 'celular');
		}

		foreach ($arr_series as $serie)
		{
			$meses = array();
			$meses = explode('-', $str_meses);

			foreach($meses as $mes)
			{
				$mes_ano = (int) substr($mes, 0, 4);
				$mes_mes = (int) substr($mes, 4, 2);

				// recupera datos de trafico
				$this->db->from($this->config->item('bd_trafico_mes'));
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes));
				$this->db->where($tipo, $serie);

				foreach($this->db->get()->result_array() as $registro)
				{
					$result[$this->_get_dv_imei($registro['imei'])][$registro['celular']][$mes] = ($registro['seg_entrada']+$registro['seg_salida'])/60;
				}
			}
		}

		// recupera datos comerciales
		$result_final = array();

		foreach($result as $imei => $datos_imei)
		{
			foreach($datos_imei as $celular => $datos_cel)
			{
				$num_celular = (string) $celular;
				$result_temp = $this->_get_datos_comerciales($imei, $num_celular);
				$result_temp['celular'] = $num_celular;
				$result_temp['imei'] = $imei;

				foreach($datos_cel as $mes => $trafico)
				{
					$result_temp[$mes] = fmt_cantidad($trafico, 1);
				}

				array_push($result_final, $result_temp);
			}
		}

		return $result_final;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los datos comerciales de un par imei/celular
	 *
	 * @param  string $imei    IMEI a consultar
	 * @param  array  $celular Celular a consultar
	 * @return array           Arreglo con resultado
	 */
	private function _get_datos_comerciales($imei = '', $celular = '')
	{
		$maxfecha = $this->db
			->select('max(convert(varchar(20), fec_alta, 112)) as fec_alta', FALSE)
			->where('num_celular', $celular)
			->get($this->config->item('bd_trafico_abocelamist'))
			->row()
			->fec_alta;

		return $this->db
			->select('a.tipo, a.num_celular, a.cod_situacion')
			->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta', FALSE)
			->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja', FALSE)
			->select('c.num_ident as rut')
			->select("c.nom_cliente + ' ' + c.ape1_cliente + ' ' + c.ape2_cliente as nombre")
			->from($this->config->item('bd_trafico_abocelamist') . ' a', 't.celular = a.num_celular', 'left')
			->join($this->config->item('bd_trafico_clientes') . ' c', 'a.cod_cliente = c.cod_cliente', 'left')
			->where(array('a.num_celular' => $celular, 'a.fec_alta' => $maxfecha))
			->get()->row_array();
	}


}
/* End of file analisis_series_model.php */
/* Location: ./application/models/analisis_series_model.php */