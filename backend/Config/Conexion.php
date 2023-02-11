<?php
namespace Config;

class Conexion {

	public static function Conectar(){
		// ESTEBLECE LA CONEXIÓN A LA BASE DE DATOS		
		$DB = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
		$DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		// ESTABLECE LA ZONA HORARIA EN LA CONEXIÓN DE LA BBDD
		$tz = (new \DateTime('now', new \DateTimeZone(ZONA_HORARIA)))->format('P');
		$DB->exec("SET time_zone = '$tz'");
		$DB->exec("SET lc_time_names = 'es_ES'");

		return $DB;
	}
}