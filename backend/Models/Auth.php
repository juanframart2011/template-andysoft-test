<?php
namespace Models;

class Auth {

	private $DB;
	private $origin;

	function __construct($DB, $origin){
		$this->DB = $DB;
		$this->origin = $origin;
	}

	function Autenticar($token)
	{
		$Q = "SELECT *
				FROM usuarios
				WHERE access_token = :token
				AND access_origin = :origin";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':token', $token);
		$Q->bindParam(':origin', $this->origin);
		$Q->execute();
		return $Q;
	}

	function verificar($user, $pass)
	{
		$Q = "SELECT id, nombre, apellido, correo, rol_id, foto
				FROM usuarios
				WHERE (usuario = :user OR correo = :user)
				AND contrasena = :pass";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':user', $user);
		$Q->bindParam(':pass', $pass);
		$Q->execute();
		return $Q;
	}

	function setToken($user, $token)
	{
		$Q = "UPDATE usuarios SET access_token = :token, access_origin = :origin WHERE id = :user";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':user', $user);
		$Q->bindParam(':token', $token);
		$Q->bindParam(':origin', $this->origin);
		$Q->execute();
		return $Q;
	}

	function getUserByToken($token)
	{
		$Q = "SELECT id, id_sucursal, nombre, apellido, correo, rol_id, foto
				FROM usuarios
				WHERE access_token = :token
				AND access_origin = :origin";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':token', $token);
		$Q->bindParam(':origin', $this->origin);
		$Q->execute();
		return $Q;
	}
}