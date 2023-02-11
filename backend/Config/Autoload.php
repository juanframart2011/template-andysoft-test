<?php
namespace Config;

class Autoload {
	public static function run(){
		spl_autoload_register(function($class){
			require_once str_replace('\\', '/', $class).'.php';
		});
	}
}