angular
.module('app')
.controller('AuthController', function ($rootScope, $scope, $mdSidenav, $location) {
	let vm = $scope;

	vm.rutas = [];
	vm.toggleRight = () => $mdSidenav('right').toggle();
	vm.isOpenRight = () => $mdSidenav('right').isOpen();

	$rootScope.toggleRight = vm.toggleRight;
	$rootScope.isOpenRight = vm.isOpenRight;

	const { rol } = USER_INFO();

	(rutas = () => {
		switch (rol) {
			case 1:
				$scope.rutas = [
					{to: '/usuarios', name: 'Usuarios'},
					{to: '/rol', name: 'Roles'},
				];
				break;

			case 2:
				$scope.rutas = [
					{to: '/usuarios', name: 'Usuarios'},
					{to: '/rol', name: 'Roles'},
				];
				break;

			case 3:
				$scope.rutas = [
					{to: '/usuarios', name: 'Usuarios'},
					{to: '/rol', name: 'Roles'},
				];
				break;
		
			default:
				$location.path('/entrar');
				break;
		}
	})();
})
.controller('RightCtrl', (vm, $mdSidenav) => {
	vm.close = () => $mdSidenav('right').close();
});