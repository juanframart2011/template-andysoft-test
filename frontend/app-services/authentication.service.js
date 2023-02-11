angular
.module('app')
.factory('AuthenticationService', ($http, $rootScope) => {
	var service = {};

	service.Login = Login;
	service.SetCredentials = SetCredentials;
	service.ClearCredentials = ClearCredentials;
	service.GetUser = GetUser;
	$rootScope.AuthenticationService = service;

	return service;
	
	async function Login(username, password, callback) {		
		const data = {
			username,
			password
		};

		await $http.post(API_ENDPOINT + 'entrar', data).then(handleSuccess, handleError('Error al solicitar los datos'));

		function handleSuccess(res) {
			callback(res.data);
		}

		function handleError(error) {
			return () => {
				return { success: false, message: error };
			};
		}
	}

	async function GetUser(token) {
		await $http.get(API_ENDPOINT + 'usuario')
			.then(data => SetCredentials(data, token))
			.catch(ClearCredentials);
   }

	function SetCredentials(info, token) {
		$rootScope.isLogged = true;
		localStorage.setItem('userToken', JSON.stringify(token));
		localStorage.setItem('userInfo', JSON.stringify(info));
	}

	function ClearCredentials() {
		$rootScope.isLogged = false;
		localStorage.removeItem('userToken');
		localStorage.removeItem('userInfo');
	}
});