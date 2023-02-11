<?php
namespace Controllers;

use PDO;
use Models\Auth as AuthModel;
use Config\Autenticacion as Autenticacion;
use Config\Conexion as Conexion;

class Auth extends BaseController {

	private $Auth;
	private $DB;
	private $origin;

	function __construct()
	{
		$this->DB = Conexion::Conectar();
		$this->origin = $this->get_client_ip();
		$this->Auth = new AuthModel($this->DB, $this->origin);
	}

	function Autenticar($token)
	{
		$u = $this->Auth->Autenticar($token)->fetch(PDO::FETCH_OBJ);
		return $u ?? false;
	}

	function verificar()
	{
		$r['error'] = true;
		$r['success'] = false;
		// Se reciben los datos que fueron enviados en formato JSON
		$d = $this->request();
		$user = $d->username ?? false;
		$pass = $d->password ?? false;
		
		if (!$user || !$pass) {
			$r['message'] = 'Se requiere usuario, contraseña y tipo de autenticación.';
			$this->responder($r);
		}

		$ver = $this->Auth->verificar($user, hash('sha256', $pass));	//	Verificar Usuario
		if (!$ver->rowCount()) {
			$r['error'] = false;
			$r['message'] = 'Credenciales inválidas.';
			$this->responder($r);
		}

		$user = $ver->fetch(PDO::FETCH_OBJ);
		$token = hash('sha256', $user->id.HOY);
		$this->Auth->setToken($user->id, $token);

		// Se devuelve los datos del usuario
		$r['data']['nombre'] = $user->nombre;
		$r['data']['apellido'] = $user->apellido;
		$r['data']['correo'] = $user->correo;
		$r['data']['rol'] = (int) $user->rol_id;
		$r['data']['access_token'] = $token;
		// Se devuelve el estatus de inicio de sesión
		$r['success'] = true;
		$r['error'] = false;
		$this->responder($r);
	}

	function getUserByToken()
	{
		$token = Autenticacion::getAuthorizationToken();
		if ($token) {
			$user = $this->Auth->getUserByToken($token);
			if ($user->rowCount()) {
				$data = $user->fetch(PDO::FETCH_OBJ);

				$r['id'] = (int) $data->id;
				$r['id_sucursal'] = (int) $data->id_sucursal;
				$r['nombre'] = $data->nombre;
				$r['apellido'] = $data->apellido;
				$r['correo'] = $data->correo;
				$r['rol'] = (int) $data->rol_id;
				$r['foto'] = $data->foto;

				$this->responder($r);
			}
			else $this->responder([], 401);
		}
	}

	function get_client_ip()
	{
		$ip = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ip = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ip = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ip = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ip = getenv('REMOTE_ADDR');
		else
			return null;
		
		return filter_var($ip, FILTER_VALIDATE_IP);
	}
}