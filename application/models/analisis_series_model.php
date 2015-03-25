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
			$sum_dv += ($i %2 !== 0) ? (($n*2>9) ? $n*2-9 : $n*2) : $n;
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
			return $this->db
				->limit(3000)
				->select('m.*')
				->select('a1.des_almacen as des_alm, a2.des_almacen as des_rec, u.nom_usuario')
				->select('c.*')
				->select('convert(varchar(20),fec_entrada_doc,120) as fecha_entrada_doc', FALSE)
				->select('convert(varchar(20),fecha,103) as fec', FALSE)
				->from($this->config->item('bd_movimientos_sap') . ' m')
				->join($this->config->item('bd_cmv_sap') . ' c', 'm.cmv=c.cmv', 'left')
				->join($this->config->item('bd_almacenes_sap') . ' a1', "a1.centro=m.ce and m.alm=a1.cod_almacen", 'left')
				->join($this->config->item('bd_almacenes_sap') . ' a2', "a2.centro=m.ce and m.rec=a2.cod_almacen", 'left')
				->join($this->config->item('bd_usuarios_sap')  . ' u', 'm.usuario=u.usuario', 'left')
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
			return $this->db
				->limit(1000)
				->select('*')
				->select('convert(varchar(20),fecha,120) as fecha_despacho', FALSE)
				->from($this->config->item('bd_despachos_sap'))
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
			return $this->db
				->limit(1000)
				->select('s.*, u.*, m.*')
				->select('convert(varchar(20), fecha_stock, 103) as fecha', FALSE)
				->select('convert(varchar(20), modificado_el, 103) as modif_el', FALSE)
				->select('a.des_almacen')
				->from($this->config->item('bd_stock_seriado_sap') . ' s')
				->join($this->config->item('bd_materiales_sap') . ' m', 's.material = m.cod_articulo', 'left')
				->join($this->config->item('bd_almacenes_sap') . ' a', 's.almacen=a.cod_almacen and s.centro=a.centro', 'left')
				->join($this->config->item('bd_usuarios_sap') . ' u', 's.modificado_por=u.usuario', 'left')
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
			return $this->db
				->limit(1000)
				->select('s.*, b.*, t.*, a.*, ts.*, u.*, e.*')
				->select('convert(varchar(20), fecha_stock, 103) as fecha', FALSE)
				->from($this->config->item('bd_stock_scl') . ' s')
				->join($this->config->item('bd_al_bodegas') . ' b', 'cast(s.cod_bodega as varchar(10)) = b.cod_bodega', 'left')
				->join($this->config->item('bd_al_tipos_bodegas') . ' t', '(cast(s.tip_bodega as varchar(10)) = t.tip_bodega)', 'left')
				->join($this->config->item('bd_materiales_sap') . ' a', '(cast(s.cod_articulo as varchar(10)) = a.cod_articulo)', 'left')
				->join($this->config->item('bd_al_tipos_stock') . ' ts', 's.tip_stock = ts.tipo_stock', 'left')
				->join($this->config->item('bd_al_estados') . ' e', 's.cod_estado = e.cod_estado', 'left')
				->join($this->config->item('bd_al_usos') . ' u', 's.cod_uso = u.cod_uso', 'left')
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
			$anomes = $this->db
				->select_max('(ano*100+mes)', 'anomes')
				->get($this->config->item('bd_trafico_dias_proc'))
				->row()
				->anomes;

			$ano = intval($anomes/100);
			$mes = $anomes - 100*$ano;

			$this->db
				->select('t.*, a.*, c.*, b.*')
				->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta', FALSE)
				->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja', FALSE)
				->from($this->config->item('bd_trafico_mes') . ' t')
				->join($this->config->item('bd_trafico_abocelamist') . ' a', 't.celular = a.num_celular', 'left')
				->join($this->config->item('bd_trafico_clientes') . ' c', 'a.cod_cliente = c.cod_cliente', 'left')
				->join($this->config->item('bd_trafico_causabaja') . ' b', 'a.cod_causabaja = b.cod_causabaja', 'left')
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
			->select('(100*ano+mes) as mes', FALSE)
			->from($this->config->item('bd_trafico_dias_proc'))
			->order_by('mes desc');

		foreach($this->db->get()->result_array() as $res)
		{
			$resultado[$res['mes']] = substr($res['mes'], 0, 4) . "-" . substr($res['mes'], 4, 2);
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
			$serie_cero = substr($serie, 0, 14) . "0";

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
				$mes_ano = (int) substr($mes, 0, 4);
				$mes_mes = (int) substr($mes, 4, 2);

				// recupera datos de trafico
				$this->db->from($this->config->item('bd_trafico_mes'));
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes));
				$this->db->where($tipo, $serie);

				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$this->_get_dv_imei($reg['imei'])][$reg['celular']][$mes] = ($reg['seg_entrada']+$reg['seg_salida'])/60;
				}
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