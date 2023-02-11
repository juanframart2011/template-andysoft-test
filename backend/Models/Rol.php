<?php
namespace Models;

class Rol {

	private $DB;

	function __construct($DB)
	{
		$this->DB = $DB;
	}

	function getrols($filtro)
	{
		$Q = "SELECT *
				FROM rols r
				WHERE estatus = 1
            {$filtro->coincidencia}
            {$filtro->paginacion}";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}

	function getRolList()
	{
		$Q = "SELECT *
				FROM rols r
				WHERE estatus = 1";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}

	function getrolsFilter($f)
	{
		$Q = "SELECT COUNT(r.id) AS total
				FROM rols r
				WHERE TRUE
				{$f->coincidencia}";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}

	function addRol($r)
	{
		$Q = "INSERT INTO rols SET
				name = :name,
				description = :description,
				estatus = :estatus";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':name', $r->name);
		$Q->bindParam(':description', $r->description);
		$Q->bindParam(':estatus', $r->estatus);
		$Q->execute();
		return $Q;
	}

	function updateRol($r)
	{
		$Q = "UPDATE rols SET
				name = :name,
				description = :description,
				estatus = :estatus
				WHERE id = :id";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':name', $r->name);
		$Q->bindParam(':description', $r->description);
		$Q->bindParam(':estatus', $r->estatus);
		$Q->bindParam(':id', $r->id);
		$Q->execute();
		return $Q;
	}
}