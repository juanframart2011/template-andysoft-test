<?php
namespace Controllers;

use Vendor\Utils\Validar;

class BaseController {

   public function responder($r, $code = 200)
	{
		http_response_code($code);
		echo json_encode($r, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function request()
	{
		return json_decode(file_get_contents('php://input'));
	}

	/**
	 * @param array|object $subject
	 * @param array $x
	 * @return void exit with http code 422
	 */
	public function validar($subject, $x)
	{
		$val = new Validar($subject, $x);
	}

	/**
	 * @param object $filtro
	 * @param int $items
	 * @return array pagination
	 */
	public function paginacion($filtro, $items)
	{
		$p = [
         'pagina' => $filtro->cursor,
         'paginas' => 0,
         'rango' => [],
         'total' => $items,
      ];

		if ($filtro->paginar) {
         if ($p['total']) {
            $p['paginas'] = ceil($p['total'] / $filtro->items);
            if ($p['paginas'] > 7) {
               if ($p['pagina'] > $p['paginas'] - 4) {
                  $p['rango'] = range($p['paginas'] - 7, $p['paginas']);
               } elseif ($p['pagina'] > 3) {
                  $p['rango'] = range($p['pagina'] - 3, $p['pagina'] + 3);
               } else {
                  $p['rango'] = range(1, 7);
               }
            } else {
               $p['rango'] = range(1, $p['paginas']);
            }
         }
      }

		return $p;
	}
}