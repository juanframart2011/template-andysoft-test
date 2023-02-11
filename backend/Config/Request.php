<?php
namespace Config;

use Config\Rutas as Rutas;

class Request {

	private $uri;
	private $ruta;
	private $controlador;
	private $metodo;
	private $auth;
	private $error = false;

	public function __construct()
	{
		$this->uri = substr(filter_var(urldecode($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL), 1);
		$this->uri = strlen($this->uri) != 0 ? $this->uri:false;
		if (!$this->uri) die();
		if (!isset($_GET['route'])) die();

		$this->ruta = strtolower($_GET['route']);
		$r = new Rutas($this->ruta);
		if (!$r->error) {
			$this->controlador = $r->controlador;
			$this->metodo = $r->metodo;
			$this->auth = $r->auth;
		} else {
			$this->error = $r->error;
		}
	}
	
	public function getUri(){
		return strtolower($this->uri);
	}

	public function getPath(){
		return strtolower($this->ruta);
	}

	public function getControlador(){
		return $this->controlador;
	}

	public function getMetodo(){
		return $this->metodo;
	}

	public function getAuth(){
		return $this->auth;
	}

	public function getError(){
		return $this->error;
	}
}