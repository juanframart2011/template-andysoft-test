<?php
namespace Config;

class Rutas {
	
	public $ruta;
	public $controlador;
	public $metodo;
	public $auth;
	public $error = false;

	function __construct($ruta)
	{
		$this->ruta = strtolower($ruta);
		$this->verificar();
	}
	
	private function verificar()
	{
		$r = $this->listado();
		if (isset($r[$this->ruta])) {
			$this->controlador = $r[$this->ruta][0];
			$this->metodo = $r[$this->ruta][1];
			$this->auth = in_array('noauth', $r[$this->ruta]) ? false:true;
		} else {
			$this->error = true;
		}
	}

	private function listado()
	{
		// $r['Ruta'] = ['Controlador', 'Metodo' [, otros parámetros]];
		// noauth: La ruta no requiere autenticación
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET':

				$r['endpoint-test'] = 						['Definiciones', 'endpointTest', 'noauth'];
				$r['usuario'] =								['Auth', 'getUserByToken'];
				$r['rol'] =									['Rol', 'getRols'];
				$r['roles'] =									['Rol', 'getList'];
				$r['metodos-pago'] =							['Definiciones', 'getMetodos'];
				$r['usuarios'] =								['Usuario', 'getUsuarios'];

				break;
				
			case 'POST':
				
				$r['entrar'] = 								['Auth', 'verificar', 'noauth'];
				$r['usuario'] = 								['Usuario', 'saveUsuario'];
				$r['rol'] = 								['Rol', 'saveRol'];

				break;
			
			default:
				return false;
				break;
		}
		return $r;
	}
}