<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis_series_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Toma un listado de series y lo convierte en arreglo, de acuerdo a un formato especifico
	 *
	 * @param  string  $series  Listado de series a convertir
	 * @param  string  $tipo    Formato de salida de las series
	 * @return array            Arreglo con las series con formato
	 */
	private function _series_list_to_array($series = string, $tipo = 'SAP')
	{
		$arr_series = array();
		$series = str_replace(" ", "", $series);
		$arr_series = preg_grep('/[\d]+/', explode("\r\n", $series));

		foreach ($arr_series as $k => $v)
		{
			$serie_temp = $v;
			// Modificaciones de formato SAP
			if ($tipo == 'SAP')
			{
				$serie_temp = preg_replace('/^01/', '1', $serie_temp);
				$arr_series[$k] = (strlen($serie_temp) == '19') ? substr($serie_temp, 1, 18) : $serie_temp;
			}
			// Modificaciones de formato SCL
			else if ($tipo == 'trafico')
			{
				$serie_temp = preg_replace('/^1/', '01', $serie_temp);
				$arr_series[$k] = substr($serie_temp, 0, 14) . '0';
			}
			else if ($tipo == 'SCL')
			{
				$serie_temp = preg_replace('/^1/', '01', $serie_temp);
				$arr_series[$k] = $serie_temp;
			}
		}

		return $arr_series;
	}


	// --------------------------------------------------------------------

	/**
	 * Toma una serie y le calcula el DV
	 *
	 * @param  string  $series  Listado de series a convertir
	 * @param  string  $tipo    Formato de salida de las series
	 * @return array            Arreglo con las series con formato
	 */
	private function _get_dv_imei($serie = '')
	{
		$serie15 = '';

		if (strlen($serie) == 14 AND substr($serie, 0, 1) == '1')
		{
			$serie15 = '0' . $serie;
		}

		if (strlen($serie) == 15)
		{
			$serie15 = $serie;
		}

		$serie14 = substr($serie15, 0, 14);

		$sum_dv = 0;
		foreach(str_split($serie14) as $i => $n)
		{
			$sum_dv += ($i %2 !== 0) ? (($n*2>9)?$n*2-9:$n*2): $n;
		}

		return $serie14 . (10 - $sum_dv % 10);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera la historia de un conjunto de series
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_historia($series = string)
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{

			return $this->db->limit(3000)
				->select('mov_hist.*')
				->select('alm1.des_almacen as des_alm, alm2.des_almacen as des_rec, cp_usuarios.nom_usuario')
				->select('cp_cmv.*')
				->select('convert(varchar(20),fec_entrada_doc,120) as fecha_entrada_doc')
				->select('convert(varchar(20),fecha,103) as fec')
				->from('bd_logistica..mov_hist')
				->join('bd_logistica..cp_cmv', 'mov_hist.cmv=cp_cmv.cmv', 'left')
				->join('bd_logistica..cp_almacenes as alm1', "alm1.centro=mov_hist.ce and mov_hist.alm=alm1.cod_almacen", 'left')
				->join('bd_logistica..cp_almacenes as alm2', "alm2.centro=mov_hist.ce and mov_hist.rec=alm2.cod_almacen", 'left')
				->join('bd_logistica..cp_usuarios', 'mov_hist.usuario=cp_usuarios.usuario', 'left')
				->where_in('serie', $arr_series)
				->order_by('serie','asc')
				->order_by('fecha','asc')
				->order_by('fec_entrada_doc','asc')
				->get()
				->result_array();
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los despachos de un conjunto de series
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_despacho($series = string)
	{
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			return $this->db->limit(1000)
				->select('*')
				->select('convert(varchar(20),fecha,120) as fecha_despacho')
				->from('bd_logistica..despachos_sap')
				->where_in('n_serie', $arr_series)
				->get()
				->result_array();
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el stock SAP de un conjunto de series
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_stock_sap($series = string)
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			return $this->db->limit(1000)
				->select('bd_stock_sap.*, cp_usuarios.*, al_articulos.*')
				->select('convert(varchar(20), fecha_stock, 103) as fecha')
				->select('convert(varchar(20), modificado_el, 103) as modif_el')
				->select('cp_almacenes.des_almacen')
				->from('bd_logistica..bd_stock_sap')
				->join('bd_logistica..al_articulos', 'bd_stock_sap.material = al_articulos.cod_articulo', 'left')
				->join('bd_logistica..cp_almacenes', 'bd_stock_sap.almacen=cp_almacenes.cod_almacen and bd_stock_sap.centro=cp_almacenes.centro', 'left')
				->join('bd_logistica..cp_usuarios', 'bd_stock_sap.modificado_por=cp_usuarios.usuario', 'left')
				->where_in('serie', $arr_series)
				->get()
				->result_array();
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el stock SCL de un conjunto de series
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_stock_scl($series = string) {
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			return $this->db->limit(1000)
				->select('bd_stock_scl.*, al_bodegas.*, al_tipos_bodegas.*')
				->select('al_articulos.*, al_tipos_stock.*, al_usos.*, al_estados.*')
				->select('convert(varchar(20), FECHA_STOCK, 103) as FECHA')
				->from('bd_logistica..bd_stock_scl')
				->join('bd_logistica..al_bodegas', 'cast(bd_stock_scl.cod_bodega as varchar(10)) = al_bodegas.cod_bodega', 'left')
				->join('bd_logistica..al_tipos_bodegas', 'cast(bd_stock_scl.tip_bodega as varchar(10)) = al_tipos_bodegas.tip_bodega', 'left')
				->join('bd_logistica..al_articulos', 'cast(bd_stock_scl.cod_articulo as varchar(10)) = al_articulos.cod_articulo', 'left')
				->join('bd_logistica..al_tipos_stock', 'bd_stock_scl.tip_stock = al_tipos_stock.tipo_stock', 'left')
				->join('bd_logistica..al_estados', 'bd_stock_scl.cod_estado = al_estados.cod_estado', 'left')
				->join('bd_logistica..al_usos', 'bd_stock_scl.cod_uso = al_usos.cod_uso', 'left')
				->where_in('serie_sap', $arr_series)
				->get()
				->result_array();
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera ultimo trafico de un conjunto de series
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_trafico($series = "")
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series, 'trafico');

		foreach ($arr_series as $serie)
		{
			$anomes = $this->db->select_max('ano*100+mes', 'anomes')
				->get('bd_controles.dbo.trafico_dias_procesados')
				->row()
				->anomes;

			$ano = intval($anomes/100);
			$mes = $anomes - 100*$ano;

			$this->db->select('t.*, a.*, c.*, b.*')
				->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta')
				->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja')
				->from('bd_logistica..trafico_mes t')
				->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left')
				->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left')
				->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left')
				->where(array('ano' => $ano, 'mes' => $mes, 'imei'=> $serie))
				->order_by('imei, fec_alta');

			array_push($result, $this->db->get()->result_array());
		}

		return $result;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con los meses de trafico
	 *
	 * @param  none
	 * @return array            Arreglo con resultado
	 */
	public function get_meses_trafico()
	{
		$resultado = array();

		$this->db
			->distinct()
			->select('100*ano+mes as mes')
			->from('bd_controles..trafico_dias_procesados')
			->order_by('mes desc');

		foreach($this->db->get()->result_array() as $res)
		{
			$resultado[$res['mes']] = substr($res['mes'],0,4) . "-" . substr($res['mes'],4,2);
		}
		return $resultado;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el trafico de una serie (imei/celular) para un conjunto de meses
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @param  array   $meses   Arreglo de meses a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_trafico_mes($series = "", $meses = array())
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series, 'SCL');

		foreach ($arr_series as $serie)
		{
			$serie_orig = $serie;
			$serie_cero = substr($serie,0,14) . "0";

			foreach($meses as $mes)
			{
				$mes_ano = substr($mes,0,4);
				$mes_mes = substr($mes,4,2);
				$this->db->select('t.*, a.*, c.*, b.*, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
				$this->db->from('bd_logistica..trafico_mes t');
				$this->db->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
				$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
				$this->db->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left');
				//$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'imei'=> $serie, 'cod_situacion<>' => 'BAA'));
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'cod_situacion<>' => 'BAA'));
				$this->db->where_in('imei', array($serie_orig, $serie_cero));
				$this->db->order_by('imei, fec_alta');
				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$serie][$reg['celular']][$mes] = $reg;
				}
			}
		}

		return $result;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el trafico de una serie (imei/celular) para un conjunto de meses
	 *
	 * @param  string  $series  Listado de series a consultar
	 * @param  array   $meses   Arreglo de meses a consultar
	 * @param  string  $tipo    imei o celular a consultar
	 * @return array            Arreglo con resultado
	 */
	public function get_trafico_mes2($series = "", $str_meses = "", $tipo = 'imei')
	{
		$result = array();
		$arr_series = array();

		if ($series != "")
		{
			$arr_series = $this->_series_list_to_array($series, ($tipo == 'imei') ? 'trafico' : 'celular');
		}

		foreach ($arr_series as $serie)
		{
			$meses = array();
			$meses = explode("-", $str_meses);

			foreach($meses as $mes)
			{
				$mes_ano = (int) substr($mes,0,4);
				$mes_mes = (int) substr($mes,4,2);

				// recupera datos de trafico
				$this->db->from('bd_logistica..trafico_mes');
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes));
				$this->db->where($tipo, $serie);

				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$this->_get_dv_imei($reg['imei'])][$reg['celular']][$mes] = ($reg['seg_entrada']+$reg['seg_salida'])/60;
				}
				//dbg($result);
			}
		}

		// recupera datos comerciales
		$result_final = array();

		foreach($result as $imei => $datos_imei)
		{
			foreach($datos_imei as $cel => $datos_cel)
			{
				$num_celular = (string) $cel;
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
	 * @param  string  $imei     IMEI a consultar
	 * @param  array   $celular  Celular a consultar
	 * @return array             Arreglo con resultado
	 */
	private function _get_datos_comerciales($imei = '', $celular = '')
	{
		$maxfecha = $this->db
			->select('max(convert(varchar(20), fec_alta, 112)) as fec_alta')
			->where('num_celular', $celular)
			->get('bd_controles..trafico_abocelamist')
			->row()
			->fec_alta;

		return $this->db
			->select('a.tipo, a.num_celular, a.cod_situacion')
			->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta')
			->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja')
			->select('c.num_ident as rut')
			->select('c.nom_cliente + \' \' + c.ape1_cliente + \' \' + c.ape2_cliente as nombre')
			->from('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left')
			->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left')
			->where(array('a.num_celular' => $celular, 'a.fec_alta' => $maxfecha))
			->get()->row_array();
	}


}
/* End of file analisis_series_model.php */
/* Location: ./application/models/analisis_series_model.php */