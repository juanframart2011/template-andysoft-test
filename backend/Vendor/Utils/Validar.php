<?php
namespace Vendor\Utils;

class Validar {

	public $request = [];
	public $subject;

	function __construct($request, $x)
	{
		$this->r = is_array($request) ? $request : json_decode(json_encode($request), true);
		// Se iteran los items a validar
		foreach ($x as $itemk => $itemv) {
			$this->s = $itemk;
			// Se separan e iteran los validadores
			$validadores = explode('|', $itemv);
			// Se verifica si es nullable
			if (in_array('nullable', $validadores)) {
				// Se verifica si el campo está vacío, si lo retá no se realizan las demás validaciones
				$validar = !$this->nullable(); // Si devuelve true (está vacío) se invierte el valor para que no se valide el campo.
				unset($validadores[array_search('nullable', $validadores)]);
			} else {
				$validar = true;
			}
			if ($validar) {
				foreach ($validadores as $fieldk => $fieldv) {
					// Se separan los parámetros de los validadores
					$validador = explode(':', $fieldv);
					if (count($validador) > 1) $this->{$validador[0]}($validador[1]);
					else $this->{$validador[0]}();
				}
			}
		}
	}

   public function fail($r)
	{
		http_response_code(422);
		echo json_encode(['result' => false, 'message' => $r], JSON_UNESCAPED_UNICODE);
		die();
	}

	/** After (Date) */
	public function after($x)
	{
		# Subject debe ser un valor posterior a una fecha determinada. Las fechas se pasarán a la strtotimefunción PHP para convertirlas en una DateTimeinstancia válida:
		# 'start_date' => 'required|date|after:tomorrow'
	}

	/** After Or Equal (Date) */
	public function after_or_equal($x)
	{
		# 'start_date' => 'required|date|after:tomorrow'
	}

	/** Array */
	public function array()
	{
		# Subject debe ser PHP array.
		if (!is_array($this->r[$this->s])) $this->fail("$this->s: debe ser un array");
	}

	/** Before (Date) */
	public function before($x)
	{
		# code...
	}

	/** Before Or Equal (Date) */
	public function before_or_equal($x)
	{
		# code...
	}

	/** Between */
	public function between($x)
	{
		# Subject debe tener un tamaño entre el mínimo y el máximo dados. Las cadenas, números, matrices y archivos se evalúan de la misma forma que la size regla.
	}

	/** Boolean */
	public function boolean()
	{
		# Subject debe poder convertirse en booleano. De entrada aceptados son true, false, 1, 0, "1", y "0".
		if (!is_bool($this->r[$this->s])) $this->fail("$this->s: debe ser booleano");
	}
	
	/** Contains */
	public function contains($x)
	{
		# El campo bajo de validación debe contener en los valores de X.
		# contains:anotherfield.*
		foreach (explode(',', $x) as $v) {
			$v = trim($v);
			
			switch (gettype($this->r[$this->s])) {
				case 'array':
					if (!array_key_exists($v, $this->r[$this->s])) $this->fail("{$this->s}: debe contener $v");
					break;
					
				case 'object':
					if (!isset($this->r[$this->s]->{$v})) $this->fail("{$this->s}: debe contener $v");
					break;
				
				default:
					$this->fail("{$this->s}: contains - tipo de dato no definido");
					break;
			}
		}
	}

	/** Date */
	public function date($x)
	{
		# Subject debe ser una fecha válida y no relativa según la función strtotime.
		# 'start_date' => 'required|date|after:tomorrow'
	}

	/** Date Equals */
	public function date_equals($x)
	{
		# Subject debe ser igual a la fecha indicada. Las fechas se pasarán a la función strtotime para convertirse en una instancia DateTime válida.
	}

	/** Date Format */
	public function date_format($x)
	{
		# Subject debe coincidir con el formato dado. Debe usar uno date o date_formatal validar un campo, no ambos.
	}

	/** Different */
	public function different($x)
	{
		# Subject debe tener un valor diferente a Y.
	}

	/** Digits */
	public function digits($x)
	{
		# Subject debe ser numérico y debe tener una longitud exacta de valor .
	}

	/** Digits Between */
	public function digits_between($x)
	{
		# Subject debe ser numérico y debe tener una longitud entre el mínimo y el máximo dados .
	}

	/** Dimensions (Image Files) */
	public function dimensions($x)
	{
		# El archivo bajo validación debe ser una imagen que cumpla con las restricciones de dimensión especificadas por los parámetros de la regla:
		# Las restricciones disponibles son: min_width, max_width, min_height, max_height, width, height, ratio.
		# 'avatar' => 'dimensions:min_width=100,min_height=200'
		
		# Una restricción de relación (ratio) debe representarse como ancho dividido por alto. Esto se puede especificar mediante una fracción como 3/2 o un flotante como 1.5:
		# 'avatar' => 'dimensions:ratio=3/2'
	}

	/** Distinct */
	public function distinct($x)
	{
		# Al validar matrices, el campo bajo validación no debe tener valores duplicados:
		# 'foo.*.id' => 'distinct'
	}

	/** Email */
	public function email()
	{
		# code...
	}

	/** Ends With */
	public function ends_with($x)
	{
		# Subject debe terminar con uno de los valores dados.
		# ends_with:foo,bar,...
	}

	/** Exclude If */
	public function exclude_if(&$x)
	{
		# Subject se excluirá de los datos de solicitud devueltos por los métodos validatey validatedsi el campo otro campo es igual a valor.
		# exclude_if:anotherfield,value
	}

	/** Exists (Database) */
	public function exists($x)
	{
		# Subject debe existir en una tabla de base de datos determinada.
		# exists:table,column
	}

	/** File */
	public function file()
	{
		# Subject debe ser un archivo cargado correctamente.
	}

	/** Filled */
	public function filled()
	{
		# Subject no debe estar vacío cuando está presente.
		if (empty($this->r[$this->s])) $this->fail("$this->s: no debe estar vacío");
	}

	/** For Cicle Filled */
	public function for_filled($x)
	{
		# Subject no debe estar vacío cuando está presente en cada objeto del arreglo.
		foreach ($this->r[$this->s] as $k => $i) {
			foreach (explode(',', $x) as $field) {
				$field = trim($field);
				if (empty($i[$field])) $this->fail("for_filled: {$this->s}[$k]{$field}: no debe estar vacío");
			}
		}
	}
	
	/** For Cicle Present */
	public function for_present($x)
	{
		# Subject debe estar presente en los datos de entrada pero puede estar vacío.
		foreach ($this->r[$this->s] as $k => $i) {
			foreach (explode(',', $x) as $v) {
				$v = trim($v);
			
				switch (gettype($this->r[$this->s][$k])) {
					case 'array':
						if (!array_key_exists($v, $this->r[$this->s][$k])) $this->fail("for_present: {$this->s}[$k]: debe contener $v");
						break;

					case 'object':
						if (!isset($this->r[$this->s][$k]->{$v})) $this->fail("for_present: {$this->s}[$k]: debe contener $v");
						break;

					default:
						$this->fail(gettype($this->r[$this->s]) . " {$this->s}[$k]: for_present - tipo de dato no definido");
						break;
				}
			}
		}
	}
	
	/** Greater Than */
	public function gt($x)
	{
		# Subject debe ser mayor que el campo dado.
		# Los dos campos deben ser del mismo tipo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la regla size.
		# gt:value

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) <= $x) $this->fail("$this->s: debe ser mayor a $x");
				break;

			case is_int($this->r[$this->s]):
				if ($this->r[$this->s] <= $x) $this->fail("$this->s: debe ser mayor a $x");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) <= $x) $this->fail("$this->s: debe ser mayor a $x");
				break;
			
			default:
				$this->fail("$this->s: gt - datatype no definido");
				break;
		}
	}
	
	/** Greater Than Or Equal */
	public function gte($x)
	{
		# Subject debe ser mayor o igual que el campo dado.
		# Los dos campos deben ser del mismo tipo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la sizeregla.
		# gte:value

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) < $x) $this->fail("$this->s: debe ser mayor o igual a $x");
				break;

			case is_int($this->r[$this->s]):
				if ($this->r[$this->s] < $x) $this->fail("$this->s: debe ser mayor o igual a $x");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) < $x) $this->fail("$this->s: debe ser mayor o igual a $x");
				break;
			
			default:
				$this->fail("$this->s: gte - datatype no definido");
				break;
		}
	}
	
	/** In */
	public function in($x)
	{
		# Subject debe incluirse en la lista de valores dada.
		# in:foo,bar,...
	}
	
	/** In Array */
	public function in_array($x)
	{
		# El campo bajo de validación debe existir en los valores de X.
		# in_array:anotherfield.*
		foreach (explode(',', $x) as $k => $v) {
			$v = trim($v);
			if (is_bool(array_search($v, $this->r[$this->s]))) $this->fail("{$this->s}[$k]: debe contener $v");
		}
	}
	
	/** In Array */
	public function in_object($x)
	{
		# El campo bajo de validación debe existir en los valores de X.
		# in_array:anotherfield.*
		foreach (explode(',', $x) as $v) {
			$v = trim($v);
			if (!isset($this->r[$this->s]->{$v})) $this->fail("{$this->s}->{$this->s}: debe contener $v");
		}
	}
	
	/** Integer */
	public function integer()
	{
		# Subject debe ser un número entero.
		if (!is_int($this->r[$this->s])) $this->fail("$this->s: no es un número entero");
	}
	
	/** Less Than */
	public function lt($x)
	{
		# Subject debe ser menor que el campo dado.
		# Los dos campos deben ser del mismo tipo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la regla size.
		# lt:field

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) >= $x) $this->fail("$this->s: debe ser menor a $x");
				break;

			case is_int($this->r[$this->s]):
				if ($this->r[$this->s] >= $x) $this->fail("$this->s: debe ser menor a $x");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) >= $x) $this->fail("$this->s: debe ser menor a $x");
				break;
			
			default:
				$this->fail("$this->s: lt - datatype no definido");
				break;
		}
	}
	
	/** Less Than Or Equal */
	public function lte($x)
	{
		# Subject debe ser menor o igual al campo dado.
		# Los dos campos deben ser del mismo tipo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la regla size.
		# lte:field

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) > $x) $this->fail("$this->s: debe ser menor o igual a $x");
				break;

			case is_int($this->r[$this->s]):
				if ($this->r[$this->s] > $x) $this->fail("$this->s: debe ser menor o igual a $x");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) > $x) $this->fail("$this->s: debe ser menor o igual a $x");
				break;
			
			default:
				$this->fail("$this->s: lte - datatype no definido");
				break;
		}
	}
	
	/** Max */
	public function max($x)
	{
		# Subject debe ser menor o igual a un valor máximo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la regla size.
		# max:value
		# Nota: Los caracteres especiales los cuenta x2: var_dump("Título") -> string(7) "Título"

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) > $x) $this->fail("$this->s: supera el máximo permitido");
				break;

			case is_numeric($this->r[$this->s]):
				if ($this->r[$this->s] > $x) $this->fail("$this->s: supera el máximo permitido");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) > $x) $this->fail("$this->s: supera el máximo permitido");
				break;
			
			default:
				$this->fail("$this->s: max - datatype no definido");
				break;
		}
	}
	
	/** Min */
	public function min($x)
	{
		# Subject debe tener un valor mínimo.
		# Las cadenas, números, matrices y archivos se evalúan utilizando las mismas convenciones que la regla size.
		# min:value

		switch (true) {
			case is_string($this->r[$this->s]):
				if (strlen($this->r[$this->s]) < $x) $this->fail("$this->s: no supera el mínimo permitido");
				break;

			case is_numeric($this->r[$this->s]):
				if ($this->r[$this->s] < $x) $this->fail("$this->s: no supera el mínimo permitido");
				break;

			case is_countable($this->r[$this->s]):
				if (count($this->r[$this->s]) < $x) $this->fail("$this->s: no supera el mínimo permitido");
				break;
			
			default:
				$this->fail("$this->s: min - datatype no definido");
				break;
		}
	}
	
	/** Multiple Of */
	public function multiple_of($x)
	{
		# Subject debe ser un múltiplo de X.
		# multiple_of:value
		if (($this->r[$this->s] % $x) !== 0) $this->fail("$this->s: no es múltiplo de $x");
	}
	
	/** Not In */
	public function not_in($x)
	{
		# Subject no debe incluirse en la lista de valores dada.
		# not_in:foo,bar,...
	}
	
	/** Not Regex */
	public function not_regex($x)
	{
		# Subject no debe coincidir con la expresión regular dada.
		# Internamente, esta regla usa la función preg_match.
		# El patrón especificado debe obedecer al mismo formato requerido por preg_match y, por lo tanto, también debe incluir delimitadores válidos.
		# Por ejemplo: 'email' => 'not_regex:/^.+$/i'.
		# not_regex:pattern
	}
	
	/** Nullable */
	public function nullable()
	{
		# Subject puede ser null.
		return empty($this->r[$this->s]);
	}
	
	/** Numeric */
	public function numeric()
	{
		# Subject debe ser numérico.
		if (!is_numeric($this->r[$this->s])) $this->fail("$this->s: no es numérico");
	}

	/** Object */
	public function object()
	{
		# Subject debe ser PHP object.
		if (!is_object($this->r[$this->s])) $this->fail("$this->s: debe ser un objeto");
	}
	
	/** Regular Expression */
	public function regex($x)
	{
		# Subject debe coincidir con la expresión regular dada.
		# Internamente, esta regla usa la función preg_match.
		# El patrón especificado debe obedecer al mismo formato requerido por preg_matchy, por lo tanto, también debe incluir delimitadores válidos.
		# Por ejemplo: 'email' => 'regex:/^.+@.+$/i'.
		# regex:pattern
	}
	
	/** Present */
	public function present()
	{
		# Subject debe estar presente en los datos de entrada pero puede estar vacío.
		if (!array_key_exists($this->s, $this->r)) $this->fail("$this->s: no existe");
	}
	
	/** Required */
	public function required()
	{
		/*
			Subject debe estar presente en los datos de entrada y no estar vacío.
			Un campo se considera "vacío" si se cumple una de las siguientes condiciones:
			* El valor es null.
			* El valor es una cadena vacía.
			* El valor es una matriz u objeto contable vacío.
		*/
		if (!array_key_exists($this->s, $this->r)) $this->fail("$this->s: es obligatorio");
		if ($this->r[$this->s] === null) $this->fail("$this->s: es obligatorio");
		if ($this->r[$this->s] === '') $this->fail("$this->s: es obligatorio");
		if (is_countable($this->r[$this->s]) && count($this->r[$this->s]) === 0) $this->fail("$this->s: es obligatorio");
		$this->filled();
	}
	
	/** Required If */
	public function required_if($x)
	{
		# Subject debe estar presente y no vacío si el campo de anotherfield es igual a value.
		# required_if:anotherfield,value
	}
	
	/** Required With */
	public function required_with($x)
	{
		# Subject debe estar presente y no vacío solo si alguno de los otros campos especificados está presente.
		# required_with:foo,bar,...
	}
	
	/** Required With All */
	public function required_with_all($x)
	{
		# Subject debe estar presente y no vacío solo si están presentes todos los demás campos especificados.
		# required_with_all:foo,bar,...
	}
	
	/** Required Without */
	public function required_without($x)
	{
		# Subject debe estar presente y no vacío solo cuando alguno de los otros campos especificados no está presente.
		# required_with:foo,bar,...
	}
	
	/** Required Without All */
	public function required_without_all($x)
	{
		# Subject debe estar presente y no vacío solo cuando todos los demás campos especificados no están presentes.
		# required_with_all:foo,bar,...
	}

	/** Same */
	public function same($x)
	{
		# El campo dado debe coincidir con el campo bajo validación.
		# same:field
		if ($this->r[$this->s] === $x) $this->fail("$this->s: no es igual a $x");
	}
	
	/** Size */
	public function size($x)
	{
		/*
			X debe tener un tamaño que coincida con el valor dado . Para datos de cadena, el valor corresponde al número de caracteres.
			Para datos numéricos, el valor corresponde a un valor entero dado (el atributo también debe tener la regla numerico integer).
			Para una matriz, el tamaño corresponde al countde la matriz. Para los archivos, el tamaño corresponde al tamaño del archivo en kilobytes. Veamos algunos ejemplos:
		*/

		# Validate that a string is exactly 12 characters long...
		# 'title' => 'size:12';

		# Validate that a provided integer equals 10...
		# 'seats' => 'integer|size:10';

		# Validate that an array has exactly 5 elements...
		# 'tags' => 'array|size:5';

		# Validate that an uploaded file is exactly 512 kilobytes...
		# 'image' => 'file|size:512';
	}
	
	/** Starts With */
	public function starts_with($x)
	{
		# Subject debe comenzar con uno de los valores dados.
		# starts_with:foo,bar,...
	}
	
	/** String */
	public function string()
	{
		# Subject debe ser una cadena. Si desea permitir que el campo también lo esté null, debe asignar la nullableregla al campo.
		if (!is_string($this->r[$this->s])) $this->fail("$this->s: no es una cadena de texto");
	}
}