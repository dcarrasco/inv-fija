<?php

namespace Model;

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
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
trait Model_has_pagination {

	/**
	 * Cantidad de registros por pagina
	 *
	 * @var integer
	 */
	protected $page_results = 12;

	/**
	 * Nombre del registro del numero de pagina
	 *
	 * @var string
	 */
	protected $page_name = 'page';

	// --------------------------------------------------------------------

	/**
	 * Recupera la cantidad de registros que componen una página
	 *
	 * @return integer Cantidad de registros en una página
	 */
	public function get_page_results()
	{
		return $this->page_results;
	}

	// --------------------------------------------------------------------

	/**
	 * Crea links de paginación para desplegar un listado del modelo
	 *
	 * @return string         Links de paginación
	 */
	public function crea_links_paginas()
	{
		$this->load->library('pagination');

		$total_rows = $this->filter_by($this->filtro)->count();

		$cfg_pagination = [
			'uri_segment'     => 5,
			'num_links'       => 5,
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

			'per_page'    => $this->page_results,
			'total_rows'  => $total_rows,
			'base_url'    => site_url(
				$this->router->class . '/' .
				($this->uri->segment(2) ? $this->uri->segment(2) : 'listado') . '/' .
				$this->get_model_nombre(FALSE)
			),
			'first_link'  => lang('orm_pag_first'),
			'last_link'   => lang('orm_pag_last') . ' (' . (int)($total_rows / $this->page_results + 1) . ')',
			'prev_link'   => '<span class="fa fa-chevron-left"></span>',
			'next_link'   => '<span class="fa fa-chevron-right"></span>',

			'reuse_query_string'   => TRUE,
			'page_query_string'    => TRUE,
			'query_string_segment' => $this->page_name,
			'use_page_numbers'     => TRUE,
		];

		$this->pagination->initialize($cfg_pagination);

		return $this->pagination->create_links();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera listado paginado
	 *
	 * @param  integer $page Numero de página a recuperar
	 * @return void
	 */
	public function paginate($page = NULL)
	{
		$page = is_null($page) ? request($this->page_name, 1) : $page;

		return collect($this
			->limit($this->page_results, ($page - 1) * $this->page_results)
			->filter_by($this->filtro)
			->order_by($this->order_by)
			->get()
		);
	}


}
/* End of file model_has_pagination.php */
/* Location: ./application/libraries/model_has_pagination.php */
