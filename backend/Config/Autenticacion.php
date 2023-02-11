<?php
namespace Config;

use Controllers\Auth as Auth;

class Autenticacion {

	public static function Autenticar()
	{
		$token = Autenticacion::getAuthorizationToken();
		if ($token) {
			$auth = new Auth();
			$user = $auth->Autenticar($token);
			return $user ?? false;
		} else {
			return false;
		}
	}
   
	public static function getAuthorizationToken()
	{
		return $_GET['token'] ?? null;
	}
   
	public static function getAuthorizationHeader()
	{
		$headers = null;
		if (isset($_GET['token'])) {
			$headers = trim($_GET['token']);
		} else if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER['Authorization']);
		}
		else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx o fast CGI
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} else if (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}

	public static function getBearerToken()
	{
		$headers = Autenticacion::getAuthorizationHeader();
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
}