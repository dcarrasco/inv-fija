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
 * Clase Modelo Catalogo
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
class Catalogo_foto extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_catalogo Identificador del catalogo
	 * @return void
	 */
	public function __construct($id_catalogo = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_catalogos_fotos'),
				'model_label'        => 'Cat&aacute;logo foto',
				'model_label_plural' => 'Cat&aacute;logos fotos',
				'model_order_by'     => 'catalogo',
			),
			'campos' => array(
				'catalogo' => array(
					'label'          => 'Cat&aacute;logo',
					'tipo'           => 'char',
					'largo'          => 20,
					'texto_ayuda'    => 'C&oacute;digo del cat&aacute;logo. M&aacute;ximo 20 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'foto' => array(
					'label'          => 'Foto del material',
					'tipo'           => 'picture',
					'texto_ayuda'    => 'Foto del material.',
					'es_obligatorio' => TRUE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_catalogo)
		{
			$this->fill($id_catalogo);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Catalogo
	 */
	public function __toString()
	{
		return (string) $this->catalogo;
	}




}
/* End of file catalogo_foto.php */
/* Location: ./application/models/catalogo_foto.php */