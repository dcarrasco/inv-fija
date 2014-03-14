<?php


namespace Entity;

/**
 * @Entity
 * @Table(name="fija_auditores")
 */
class Auditor extends ModeloORM {

	/**
	 * @Id
	 * @Column(type="integer", length=10)
	 */
	protected $id;

	/**
	 * @Column(type="string", length=50, options={"comment" = "Nombre del auditor"})
	 */
	protected $nombre;

	/**
	 * @Column(type="boolean")
	 */
	protected $activo;


	public function __construct()
	{
		parent::__construct();
	}

	public function get_order_by()
	{
		return array('nombre' => 'ASC');
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_nombre()
	{
		return $this->nombre;
	}

	public function get_activo()
	{
		return $this->activo;
	}

}

/* End of file auditor.php */
/* Location: ./application/models/Entity/auditor.php */
