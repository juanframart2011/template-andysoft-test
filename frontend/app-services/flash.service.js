angular
	.module('app')
	.factory('FlashService', FlashService);

FlashService.$inject = ['$rootScope'];
function FlashService($rootScope) {
	var service = {};

	service.Loading = Loading;
	service.Loaded = Loaded;
	service.Success = Success;
	service.Error = Error;

	initService();

	return service;

	function initService() {
		$rootScope.$on('$locationChangeStart', function () {
			clearFlashMessage();
		});

		function clearFlashMessage() {
			var flash = $rootScope.flash;
			if (flash) {
				if (!flash.keepAfterLocationChange) {
					delete $rootScope.flash;
				} else {
					// only keep for a single location change
					flash.keepAfterLocationChange = false;
				}
			}
		}
	}

	function Loading(message = 'Cargando') {
		$rootScope.cargando = true;
		$rootScope.flash = {
			message: message,
			type: 'loading',
			icon: ''
		};
	}

	function Loaded() {
		$rootScope.cargando = false;
	}

	function Success(message) {
		$rootScope.flash.message = message;
		$rootScope.flash.type = 'success';
		$rootScope.flash.icon = 'check';
	}

	function Error(message) {
		$rootScope.flash.message = message;
		$rootScope.flash.type = 'error';
		$rootScope.flash.icon = 'clear';
	}
}