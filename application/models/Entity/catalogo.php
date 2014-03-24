<?php


namespace Entity;

/**
 * @Entity
 * @Table(name="fija_catalogos")
 */
class Catalogo extends ModeloORM {

	/**
	 * @Id
	 * @Column(type="string", length=10)
	 */
	protected $catalogo;

	/**
	 * @Column(type="string", length=50, options={"comment" = "Descripcion del material"})
	 */
	protected $descripcion;

	/**
	 * @Column(type="integer")
	 */
	protected $pmp;

	/**
	 * @Column(type="boolean")
	 */
	protected $es_seriado;


	public function get_order_by()
	{
		return array('catalogo' => 'ASC');
	}


	public function __construct()
	{
		parent::__construct();
	}

/*

	public function get_catalogo()
	{
		return $this->catalogo;
	}

	public function get_descripcion()
	{
		return $this->descripcion;
	}

	public function get_pmp()
	{
		return $this->pmp;
	}

	public function get_es_seriado()
	{
		return $this->es_seriado;
	}

*/

}

/* End of file catalogo.php */
/* Location: ./application/models/Entity/catalogo.php */
