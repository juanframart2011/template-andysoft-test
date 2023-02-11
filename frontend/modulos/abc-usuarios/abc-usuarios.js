angular
.module('app')
.controller('ABCPersonalController', function ($scope, $http, $mdDialog, $mdToast, $timeout, FlashService) {
	let vm = $scope;

	vm.filtro = {
		coincidencia: '',
		estatus: 'x',
		sucursal: 'x',
		rol: 'x',
		pagina: 1,
		items: 25
	};

	vm.ultimoFiltro = {
		coincidencia: '',
		estatus: 'x',
		sucursal: 'x',
		rol: 'x',
		pagina: 1,
		items: 25
	};

	vm.usuarios = [];
	vm.paginacion = {
		pagina: 1,
		paginas: 0,
		rango: [],
		total: 0
	};

	vm.selects = {
		sucursales: [
			{id: 1, nombre: 'Sucursal 1'},
			{id: 2, nombre: 'Sucursal 2'},
		],
		roles: []
	};

	// Filtros
	vm.$watch('filtro.items', (newValue, oldValue) => {
		if (newValue != oldValue) {
			vm.ultimoFiltro.items = vm.filtro.items = Number(newValue);
			if (vm.filtro.pagina != 1) vm.filtro.pagina = 1;
			else BuscarUsuarios();
		}
	});
	
	vm.$watch('filtro.pagina', (newValue, oldValue) => {
		if (newValue != oldValue) {
			vm.ultimoFiltro.pagina = vm.filtro.pagina = newValue;
			BuscarUsuarios();
		}
	});

	vm.aplicarFiltros = () => {
		Object.assign(vm.ultimoFiltro, vm.filtro);
		vm.ultimoFiltro.pagina = 1;
		BuscarUsuarios();
	}

	(init = async () => {
		await BuscarRoles();
		await BuscarUsuarios();
	})();

	async function BuscarUsuarios() {
		FlashService.Loading();
		await $http.get(`${API_ENDPOINT}usuarios${toParams(vm.ultimoFiltro)}`)
			.then(res => {
				if (!res.data.result) return mensaje('Ocurrió un error al solicitar los datos')
				if (!res.data.data.length) {
					vm.usuarios = [];
					vm.paginacion = res.data.paginacion;
					mensaje('No existen usuarios con los criterios de búsqueda')
				}
				vm.usuarios = res.data.data;
				vm.paginacion = res.data.paginacion;
			})
			.catch(handleCatch)
			.finally(() => {
				FlashService.Loaded();
				$timeout(() => $scope.$apply(), 10);
			});
	}

	async function BuscarRoles() {
		FlashService.Loading();
		await $http.get(`${API_ENDPOINT}roles`)
			.then(res => {
				if (!res.data.result) return mensaje('Ocurrió un error al solicitar los datos')
				if (!res.data.data.length) {
					vm.selects.roles = [];
					vm.paginacion = res.data.paginacion;
					return mensaje('No existen roles con los criterios de búsqueda')
				}
				vm.selects.roles = res.data.data;
				vm.paginacion = res.data.paginacion;
			})
			.catch(handleCatch)
			.finally(() => {
				FlashService.Loaded();
				$timeout(() => $scope.$apply(), 10);
			});
	}

	// Dialogo
	vm.DetallesUsuario = (nuevo, item = {}) => {
		vm.editarItem = { ...item };
		vm.editarItem.nuevo = nuevo; // editar = false, nuevo = true
		$mdDialog.show({
			controller: DialogoDetallesUsuarioController,
			templateUrl: 'modulos/abc-usuarios/dialogo-detalles-usuario.html',
			parent: angular.element(document.body),
			clickOutsideToClose: true
		})
		.then((res) => {
			if (res) BuscarUsuarios();
		}, () => {});
	}

	function DialogoDetallesUsuarioController($scope, $mdDialog, FlashService) {
		$scope.usuario = {
			nuevo: true,
			id: null,
			nombre: '',
			apellido: '',
			foto: '',
			rol: '',
			telefono: '',
			correo: '',
			usuario: '',
			contrasena: '',
			estatus: ''
		};

		$scope.selects = {
			sucursales: vm.selects.sucursales,
			roles: vm.selects.roles
		};
		
		Object.assign($scope.usuario, vm.editarItem);

		$scope.GuardarUsuario = () => {
			if ($scope.usuario.nuevo) Object.assign($scope.usuario, vm.editarItem);
			FlashService.Loading();

			let usuario = { ...$scope.usuario };

			$http.post(`${API_ENDPOINT}usuario`, usuario)
				.then(res => {
					mensaje(res.data.message);
					if (!res.data.result) return;
					$mdDialog.hide(true);
				})
				.catch(handleCatch)
				.finally(() => {
					FlashService.Loaded();
					$timeout(() => $scope.$apply(), 10);
				});
		}

		$scope.respuesta = (res) => $mdDialog.hide(res);
	}

	vm.getSucursal = id => {
		const s = vm.selects.sucursales.find(x => x.id === id);
		return s
			? s.nombre
			: 'No definido'
	}

	vm.getRol = id => {
		const r = vm.selects.roles.find(x => x.id === id);
		return r
			? r.descripcion
			: 'No definido'
	}

	function mensaje(msg, time = 2500) {
		$mdToast.show(
			$mdToast.simple()
			.textContent(msg)
			.hideDelay(time)
		);
	}

	function toParams(obj) {
		let str = '';
		for (let key in obj) {
			if (str != '') str += '&';
			str += key + '=' + obj[key];
		}
		return '?' + str;
	}

	function handleCatch(err) {
		const msg = err.data.hasOwnProperty('message')
			? err.data.message
			: 'Ocurrió un error al establecer la conexión'
		mensaje(msg);
		console.error(err);
	}
});