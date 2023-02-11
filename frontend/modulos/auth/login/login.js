angular
.module('app')
.controller('LoginController', function (AuthenticationService, $mdToast, $location) {
   var vm = this;

   vm.login = login;

   vm.username = 'admin';
   vm.password = 'admin';

   (function initController() {
      AuthenticationService.ClearCredentials();
   })();

   function login() {
      vm.dataLoading = true;
      AuthenticationService.Login(vm.username, vm.password, res => {
         if (res.success) {
            const token = res.data.access_token;
            let info = res.data;
            delete info.access_token;
            AuthenticationService.SetCredentials(info, token);
            $location.path('/');
         } else {
            vm.password = '';
            vm.dataLoading = false;
            mensaje(res.message);
         }
      });
   };

	function mensaje(msg, time = 2500) {
		$mdToast.show(
			$mdToast.simple()
			.textContent(msg)
			.hideDelay(time)
		);
	}
})