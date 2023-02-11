<?php
namespace Models;

class Definiciones {

	private $DB;

	function __construct($DB)
	{
		$this->DB = $DB;
	}


   // ----------------------------------------
   // MÉTODOS PARA ROLES
   // ----------------------------------------

	function getRoles()
	{
		$Q = "SELECT *
				FROM definiciones
				WHERE categoria = 'Rol'
				AND estatus = 1";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}


   // ----------------------------------------
   // MÉTODOS PARA MÉTODOS DE PAGO
   // ----------------------------------------

	function getMetodos()
	{
		$Q = "SELECT *
				FROM definiciones
				WHERE categoria = 'Metodo-Pago'
				AND estatus = 1";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}
}