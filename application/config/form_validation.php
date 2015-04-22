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
			'field' => 'almacenes[]',
			'label' => 'almacenes',
			'rules' => 'required',
		),
	),
	'inventario_analisis/imprime_inventario' => array(
		array(
			'field' => 'pag_desde',
			'label' => 'lang:inventario_print_label_page_from',
			'rules' => 'trim|required|is_natural_no_zero',
		),
		array(
			'field' => 'pag_hasta',
			'label' => 'lang:inventario_print_label_page_to',
			'rules' => 'trim|required|is_natural_no_zero',
		),
		array(
			'field' => 'oculta_stock_sap',
			'label' => 'xxxx',
			'rules' => 'trim',
		),
	),
);



/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */