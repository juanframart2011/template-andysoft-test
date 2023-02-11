<?php
namespace Controllers;

use PDO;
use Controllers\BaseController;
use Config\Conexion;
use Models\Usuario as ModelsUsuario;

class Usuario extends BaseController {

   private $DB;
   private $Usuario;

	function __construct()
   {
		$this->DB = new Conexion();
      $this->DB = $this->DB->Conectar();
      $this->Usuario = new ModelsUsuario($this->DB);
	}

   function getUsuarios()
   {
      try {
         $filtro = $this->getUsuariosFilters((object) $_GET);

         $res = $this->Usuario->getUsuarios($filtro);
         $usuarios = $res->fetchAll(PDO::FETCH_OBJ);

         foreach ($usuarios as &$u) {
            $u->id = (int) $u->id;
            $u->rol = (int) $u->rol_id;
            $u->estatus = (int) $u->estatus;
            unset($u->contrasena);
            unset($u->access_token);
            unset($u->access_origin);
         }

         $respuesta = [
            'result' => true,
            'data' => $usuarios
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

   function getUsuariosFilters($req) {
      if (isset($req->filtrar) && !$req->filtrar) {
         return (object) [
            'filtrar' => false,
            'paginar' => false,
            'rol' => null,
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
         : "AND u.estatus = $req->estatus";
   
      $f['rol'] =
         !isset($req->rol) || empty($req->rol) || $req->rol === 'x'
         ? null
         : "AND u.rol_id = $req->rol";
      
      $f['coincidencia'] = (!isset($req->coincidencia) || empty($req->coincidencia))
         ? null
         : "AND (
            u.nombre LIKE '%$req->coincidencia%' OR
            u.apellido LIKE '%$req->coincidencia%' OR
            u.usuario LIKE '%$req->coincidencia%' OR
            u.correo LIKE '%$req->coincidencia%' OR
            u.telefono LIKE '%$req->coincidencia%')";

      $f['pagina'] = ($f['cursor'] - 1) * $f['items'];

      $f['paginacion'] =
         $f['paginar']
         ? "ORDER BY u.id LIMIT {$f['pagina']}, {$f['items']}"
         : null;

      $res = $this->Usuario->getUsuariosFilter((object) $f);
      $res = $res->fetch(PDO::FETCH_OBJ);
      $f['total_items'] = (int) $res->total;

      return (object) $f;
   }

   function saveUsuario()
   {
      $this->validar($this->request(), [
         'nuevo' => 'present|boolean',
         'id' => 'present',
         'nombre' => 'required',
         'apellido' => 'required',
         'foto' => 'present',
         'rol' => 'required',
         'correo' => 'required',
         'telefono' => 'present',
         'usuario' => 'required',
         'contrasena' => 'present'
      ]);

      $req = $this->request();
      $req->estatus = 1;
      $this->DB->beginTransaction();

      try {
         if ($req->nuevo) {
            $req->contrasena = hash('sha256', $req->contrasena);
            $this->Usuario->addUsuario($req);
         } else {
            $this->Usuario->updateUsuario($req);
            if (!empty($req->contrasena)) $this->updatePassword($req->id, $req->contrasena);
         }

         $accion = $req->nuevo ? 'registrado':'editado';
         $this->DB->commit();

         $this->responder([
            'result' => true,
            'message' => "Usuario $accion con exito"
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

   function updatePassword($id, $pass)
   {
      $this->Usuario->updatePassword($id, hash('sha256', $pass));
   }
}
