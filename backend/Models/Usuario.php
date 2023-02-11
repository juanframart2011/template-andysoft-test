<?php
namespace Models;

class Usuario {

	private $DB;

	function __construct($DB)
	{
		$this->DB = $DB;
	}

	function getUsuarios($filtro)
	{
		$Q = "SELECT *
				FROM usuarios u
				WHERE estatus = 1
            {$filtro->rol}
            {$filtro->coincidencia}
            {$filtro->paginacion}";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}

	function getUsuariosFilter($f)
	{
		$Q = "SELECT COUNT(u.id) AS total
				FROM usuarios u
				WHERE TRUE
				{$f->rol}
				{$f->coincidencia}";
		$Q = $this->DB->prepare($Q);
		$Q->execute();
		return $Q;
	}

	function addUsuario($u)
	{
		$Q = "INSERT INTO usuarios SET
				nombre = :nombre,
				apellido = :apellido,
				foto = :foto,
				rol_id = :rol,
				correo = :correo,
				contrasena = :contrasena,
				telefono = :telefono,
				usuario = :usuario,
				estatus = :estatus";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':nombre', $u->nombre);
		$Q->bindParam(':apellido', $u->apellido);
		$Q->bindParam(':foto', $u->foto);
		$Q->bindParam(':rol', $u->rol);
		$Q->bindParam(':correo', $u->correo);
		$Q->bindParam(':contrasena', $u->contrasena);
		$Q->bindParam(':telefono', $u->telefono);
		$Q->bindParam(':usuario', $u->usuario);
		$Q->bindParam(':estatus', $u->estatus);
		$Q->execute();
		return $Q;
	}

	function updateUsuario($u)
	{
		$Q = "UPDATE usuarios SET
				nombre = :nombre,
				apellido = :apellido,
				foto = :foto,
				rol_id = :rol,
				correo = :correo,
				telefono = :telefono,
				usuario = :usuario,
				estatus = :estatus
				WHERE id = :id";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':nombre', $u->nombre);
		$Q->bindParam(':apellido', $u->apellido);
		$Q->bindParam(':foto', $u->foto);
		$Q->bindParam(':rol', $u->rol);
		$Q->bindParam(':correo', $u->correo);
		$Q->bindParam(':telefono', $u->telefono);
		$Q->bindParam(':usuario', $u->usuario);
		$Q->bindParam(':estatus', $u->estatus);
		$Q->bindParam(':id', $u->id);
		$Q->execute();
		return $Q;
	}

	function updatePassword($id, $pass)
	{
		$Q = "UPDATE usuarios SET
				contrasena = :contrasena
				WHERE id = :id";
		$Q = $this->DB->prepare($Q);
		$Q->bindParam(':contrasena', $pass);
		$Q->bindParam(':id', $id);
		$Q->execute();
		return $Q;
	}
}