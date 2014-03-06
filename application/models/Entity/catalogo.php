<?php


namespace Entity;

/**
 * @Entity
 * @Table(name="fija_catalogos")
 */
class Catalogo  {

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


}

/* End of file catalogo.php */
/* Location: ./application/models/Entity/catalogo.php */
