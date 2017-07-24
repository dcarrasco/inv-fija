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
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_catalogos_fotos'),
				'label'        => 'Cat&aacute;logo foto',
				'label_plural' => 'Cat&aacute;logos fotos',
				'order_by'     => 'catalogo',
			],
			'campos' => [
				'catalogo' => [
					'label'          => 'Cat&aacute;logo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'C&oacute;digo del cat&aacute;logo. M&aacute;ximo 20 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'foto' => [
					'label'          => 'Foto del material',
					'tipo'           => 'picture',
					'texto_ayuda'    => 'Foto del material.',
					'es_obligatorio' => TRUE,
				],
			],
		];

		parent::__construct($id_catalogo);
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
