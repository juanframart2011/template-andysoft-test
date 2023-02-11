<?php

ignore_user_abort(true);			#	IGNORA EL ABORTO DE LA PETICIÓN POR PARTE DEL USUARIO

// LOGS
// error_reporting(0);				#  EVITAR MOSTRAR ERRORES
ini_set('display_errors', '1');	#  PERMITIR MOSTRAR LOS ERRORES
error_reporting(E_ALL);				#  MUESTRA TODOS LOS TIPOS DE ERRORES

// ENTORNO
define('DS', DIRECTORY_SEPARATOR);	# SEPARADOR DE DIRECTORIO '/'
define('ROOT', realpath(dirname(__FILE__)) . DS);	# RUTA ACTUAL DE ARCHIVO INDEX EN EL SERVIDOR
define('ENV', 'DEV');	# ENTORNO DE TRABAJO -> DEV | PROD | SB

switch (ENV) {
	case 'DEV':
		# DEVELOPMENT
		define('ORIGIN', 'http://andysoft-template-frontend.test');
		define('URL', 'http://andysoft-template-backend.test');
		define('DB_HOST', 'localhost');
		define('DB_USER', 'root');
		define('DB_PASS', 'mysql');
		define('DB_NAME', 'andysoft-template');
		break;

	case 'PROD':
		# PRODUCCIÓN
		define('ORIGIN', '');
		define('URL', '');
		define('DB_HOST', '');
		define('DB_USER', '');
		define('DB_PASS', '');
		define('DB_NAME', '');
		break;

	case 'SB':
		# SANDBOX
		define('ORIGIN', 'http://localhost');
		define('URL', '');
		define('DB_HOST', 'localhost');
		define('DB_USER', '');
		define('DB_PASS', '');
		define('DB_NAME', '');
		break;
	
	default:
		exit('Entorno no definido');
		break;
}

$origen = $_SERVER['HTTP_ORIGIN'] ?? '*';

if ($origen == ORIGIN || $origen === '*'){  
	header('Access-Control-Allow-Origin: '.$origen);
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
	header('Access-Control-Allow-Credentials: true');
	header('Content-Type: application/json; charset=utf-8');

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		header('HTTP/1.1 200 OK');
		exit();
	}
} else {
	exit();
}

// UBICACIÓN DE LOS ARCHIVOS
define('LOCATION_QR', 'docs/qr/');
define('LOCATION_INVOICE', 'docs/invoice/');
define('LOCATION_CLOSING', 'docs/closing/');

// APP
define('ESTADO', 'Estado aquí');
define('MUNICIPIO', 'Municipio aquí');
define('NAME', 'Nombre de la empresa aquí');
define('RAZON_SOCIAL', 'Razón social aquí');
define('RIF', 'Número de Identificación Aquí');
define('DIRECCION', 'Dirección aquí');
define('TELEFONOS', 'Teléfonos aquí');
define('EMAIL_NOREPLY', 'no-responder@dominio.com');
define('EMAIL_SOPORTE', 'soporte@dominio.com');
define('EMAIL_PAGOS', 'pagos@dominio.com');
define('SITE_KEY', 'Google Captcha');
define('SECRET_KEY', 'Google Captcha');

// TIEMPO
define('ZONA_HORARIA', 'America/Monterrey');
date_default_timezone_set(ZONA_HORARIA);
define('HOY', date('Y-m-d H:i:s'));
setlocale(LC_TIME, 'es_ES', 'esp_esp');