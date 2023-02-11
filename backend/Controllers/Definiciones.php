<?php
namespace Controllers;

use PDO;
use Controllers\BaseController;
use Config\Conexion;
use Models\Definiciones as ModelsDefiniciones;

class Definiciones extends BaseController {

   private $DB;
   private $Definiciones;

	function __construct()
   {
		$this->DB = new Conexion();
      $this->DB = $this->DB->Conectar();
      $this->Definiciones = new ModelsDefiniciones($this->DB);
	}

   function endpointTest()
   {
      http_response_code(200);
   }


   // ----------------------------------------
   // MÉTODOS PARA ROLES
   // ----------------------------------------

   function getRoles()
   {
      try {
         $res = $this->Definiciones->getRoles();
         $roles = $res->fetchAll(PDO::FETCH_OBJ);

         foreach ($roles as &$r) {
            $r->id = (int) $r->codigo;
            unset($r->codigo);
            unset($r->categoria);
            unset($r->abreviatura);
            unset($r->estatus);
         }

         $this->responder([
            'result' => true,
            'data' => $roles
         ]);
         
      } catch (\PDOException $err) {
         $this->responder([
            'result' => false,
            'message' => 'Ocurrió un error al intentar obtener los datos',
            'errorDetails' => $err
         ]);
      }
   }


   // ----------------------------------------
   // MÉTODOS PARA MÉTODOS DE PAGO
   // ----------------------------------------

   function getMetodos()
   {
      try {
         $res = $this->Definiciones->getMetodos();
         $metodos = $res->fetchAll(PDO::FETCH_OBJ);

         foreach ($metodos as &$r) {
            $r->id = (int) $r->codigo;
            unset($r->abreviatura);
            unset($r->codigo);
            unset($r->categoria);
            unset($r->estatus);
         }

         $this->responder([
            'result' => true,
            'data' => $metodos
         ]);
         
      } catch (\PDOException $err) {
         $this->responder([
            'result' => false,
            'message' => 'Ocurrió un error al intentar obtener los datos',
            'errorDetails' => $err
         ]);
      }
   }
}
