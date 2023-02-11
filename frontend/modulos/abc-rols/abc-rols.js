angular
.module('app')
.controller('ABCRolController', function ($scope, $http, $mdDialog, $mdToast, $timeout, FlashService) {
	let vm = $scope;

	vm.filtro = {
		coincidencia: '',
		estatus: 'x',
		pagina: 1,
		items: 25
	};

	vm.ultimoFiltro = {
		coincidencia: '',
		estatus: 'x',
		pagina: 1,
		items: 25
	};

	vm.rols = [];
	vm.paginacion = {
		pagina: 1,
		paginas: 0,
		rango: [],
		total: 0
	};

	// Filtros
	vm.$watch('filtro.items', (newValue, oldValue) => {
		if (newValue != oldValue) {
			vm.ultimoFiltro.items = vm.filtro.items = Number(newValue);
			if (vm.filtro.pagina != 1) vm.filtro.pagina = 1;
			else BuscarRol();
		}
	});
	
	vm.$watch('filtro.pagina', (newValue, oldValue) => {
		if (newValue != oldValue) {
			vm.ultimoFiltro.pagina = vm.filtro.pagina = newValue;
			BuscarRol();
		}
	});

	vm.aplicarFiltros = () => {
		Object.assign(vm.ultimoFiltro, vm.filtro);
		vm.ultimoFiltro.pagina = 1;
		BuscarRols();
	}

	(init = async () => {
		await BuscarRols();
	})();

	async function BuscarRols() {
		FlashService.Loading();
		await $http.get(`${API_ENDPOINT}rol${toParams(vm.ultimoFiltro)}`)
			.then(res => {
				if (!res.data.result) return mensaje('Ocurrió un error al solicitar los datos')
				if (!res.data.data.length) {
					vm.rols = [];
					vm.paginacion = res.data.paginacion;
					mensaje('No existen rol con los criterios de búsqueda')
				}
				vm.rols = res.data.data;
				vm.paginacion = res.data.paginacion;
			})
			.catch(handleCatch)
			.finally(() => {
				FlashService.Loaded();
				$timeout(() => $scope.$apply(), 10);
			});
	}

	// Dialogo
	vm.DetallesRol = (nuevo, item = {}) => {
		vm.editarItem = { ...item };
		vm.editarItem.nuevo = nuevo; // editar = false, nuevo = true
		$mdDialog.show({
			controller: DialogoDetallesRolController,
			templateUrl: 'modulos/abc-rols/dialogo-detalles-rol.html',
			parent: angular.element(document.body),
			clickOutsideToClose: true
		})
		.then((res) => {
			if (res) BuscarRol();
		}, () => {});
	}

	function DialogoDetallesRolController($scope, $mdDialog, FlashService) {
		$scope.rol = {
			nuevo: true,
			id: null,
			nombre: '',
			description: '',
			estatus: ''
		};
		
		Object.assign($scope.rol, vm.editarItem);

		$scope.GuardarRol = () => {
			if ($scope.rol.nuevo) Object.assign($scope.rol, vm.editarItem);
			FlashService.Loading();

			let rol = { ...$scope.rol };

			$http.post(`${API_ENDPOINT}rol`, rol)
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