<?php


namespace Entity;

class ModeloORM {

	public function __construct()
	{
	}

	public function get_valor_field($campo = '')
	{
		$get_campo = 'get_' . $campo;
		return $this->$get_campo();
	}

	public function get_mostrar_lista($campo = '')
	{
		return TRUE;
	}


	public function get_model_id()
	{

	}


	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 *
	 * @param  string $filtro Filtro de los valores del modelo
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas($filtro = '_', $url = '', $total_rows = 0, $per_page = 0, $model_nombre = '', $ci = null)
	{
		$cfg_pagination = array(
					'uri_segment' => 5,
					'num_links'   => 5,

					'full_tag_open'   => '<ul class="pagination">',
					'flil_tag_close'  => '</ul>',

					'first_tag_open'  => '<li>',
					'first_tag_close' => '</li>',
					'last_tag_open'   => '<li>',
					'last_tag_close'  => '</li>',
					'next_tag_open'   => '<li>',
					'next_tag_close'  => '</li>',
					'prev_tag_open'   => '<li>',
					'prev_tag_close'  => '</li>',
					'cur_tag_open'    => '<li class="active"><a href="#">',
					'cur_tag_close'   => '</a></li>',
					'num_tag_open'    => '<li>',
					'num_tag_close'   => '</li>',


					'per_page'    => $per_page,
					'total_rows'  => $total_rows,
					'base_url'    => site_url($url . '/' . $model_nombre . '/' . $filtro . '/'),
					'first_link'  => 'Primero',
					'last_link'   => 'Ultimo (' . (int)($total_rows / $per_page + 1) . ')',
					'prev_link'   => '<span class="glyphicon glyphicon-chevron-left"></span>',
					'next_link'   => '<span class="glyphicon glyphicon-chevron-right"></span>',
				);
		$ci->pagination->initialize($cfg_pagination);
		return $ci->pagination->create_links();
	}
}

/* End of file modeloorm.php */
/* Location: ./application/models/Entity/modeloorm.php */
