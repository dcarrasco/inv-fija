<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config = array(
	'stock_analisis_series/historia' => array(
		array(
			'field' => 'series',
			'label' => 'series',
			'rules' => 'required',
		),
	),
	'stock_sap/mostrar_stock' => array(
		array(
			'field' => 'fechas[]',
			'label' => 'fechas',
			'rules' => 'required',
		),
		array(
			'field' => 'tipo_alm[]',
			'label' => 'tipo_alm',
			'rules' => 'required',
		),
	),
);



/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */