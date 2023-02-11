<?php
namespace Controllers;

use PDO;
use Controllers\BaseController;
use Config\Conexion;
use Models\Rol as ModelsRol;

class Rol extends BaseController {

   private $DB;
   private $Rol;

	function __construct()
   {
		$this->DB = new Conexion();
      $this->DB = $this->DB->Conectar();
      $this->Rol = new ModelsRol($this->DB);
	}

   function getList()
   {
      try {
         
         $res = $this->Rol->getRolList();
         $rols = $res->fetchAll(PDO::FETCH_OBJ);

         foreach ($rols as &$r) {
            $r->id = (int) $r->id;
            $r->name = (string) $r->name;
            $r->description = (string) $r->description;
            $r->estatus = (int) $r->estatus;
         }

         $respuesta = [
            'result' => true,
            'data' => $rols
         ];

         $this->responder($respuesta);
         
      } catch (\PDOException $err) {
         $this->responder([
            'result' => false,
            'message' => $err
         ]);
      }
   }

   function getRols()
   {
      try {
         $filtro = $this->getRolsFilters((object) $_GET);

         $res = $this->Rol->getRols($filtro);
         $rols = $res->fetchAll(PDO::FETCH_OBJ);

         foreach ($rols as &$r) {
            $r->id = (int) $r->id;
            $r->name = (string) $r->name;
            $r->description = (string) $r->description;
            $r->estatus = (int) $r->estatus;
         }

         $respuesta = [
            'result' => true,
            'data' => $rols
         ];

         if ($filtro->paginar) {
            $respuesta['paginacion'] = $this->paginacion($filtro, $filtro->total_items);
         }

         $this->responder($respuesta);
         
      } catch (\PDOException $err) {
         $this->responder([
            'result' => false,
            'message' => $err
         ]);
      }
   }

   function getRolsFilters($req) {
      if (isset($req->filtrar) && !$req->filtrar) {
         return (object) [
            'filtrar' => false,
            'paginar' => false,
            'coincidencia' => null,
            'estatus' => 1,
            'paginacion' => null,
         ];
      }

      $f['filtrar'] = true;

      $f['paginar'] =
         !isset($req->paginar) || $req->paginar === '' || $req->paginar === null || !is_bool($req->paginar)
         ? true
         :(bool) $req->paginar;

      $f['cursor'] =
         !isset($req->pagina) || empty($req->pagina) || !is_numeric($req->pagina) || $req->pagina < 1
         ? 1
         :(int) $req->pagina;

      $f['items'] =
         !isset($req->items) || empty($req->items) || !is_numeric($req->items) || $req->items < 1
         ? 25
         :(int) $req->items;

      $f['estatus'] =
         !isset($req->estatus) || $req->estatus === '' || $req->estatus === null || $req->estatus === 'x' || !is_numeric($req->estatus)
         ? 1
         : "AND r.estatus = $req->estatus";
      
      $f['coincidencia'] = (!isset($req->coincidencia) || empty($req->coincidencia))
         ? null
         : "AND (
            r.name LIKE '%$req->coincidencia%' OR
            r.description LIKE '%$req->coincidencia%')";

      $f['pagina'] = ($f['cursor'] - 1) * $f['items'];

      $f['paginacion'] =
         $f['paginar']
         ? "ORDER BY r.id LIMIT {$f['pagina']}, {$f['items']}"
         : null;

      $res = $this->Rol->getRolsFilter((object) $f);
      $res = $res->fetch(PDO::FETCH_OBJ);
      $f['total_items'] = (int) $res->total;

      return (object) $f;
   }

   function saveRol()
   {
      $this->validar($this->request(), [
         'nuevo' => 'present|boolean',
         'id' => 'present',
         'name' => 'required',
         'description' => 'required'
      ]);

      $req = $this->request();
      $req->estatus = 1;
      $this->DB->beginTransaction();

      try {
         if ($req->nuevo) {
            $this->Rol->addRol($req);
         } else {
            $this->Rol->updateRol($req);
         }

         $accion = $req->nuevo ? 'registrado':'editado';
         $this->DB->commit();

         $this->responder([
            'result' => true,
            'message' => "Rol $accion con exito"
         ]);
         
      } catch (\PDOException $err) {
         $this->DB->rollBack();
         $this->responder([
            'result' => false,
            'message' => 'Ocurrió un error al intentar guardar los datos',
            'errorDetails' => $err
         ]);
      }
   }
}