angular
   .module('app', ['ngRoute', 'ngCookies', 'ngMaterial', 'ngMessages'])
   .config(config)
   .run(run)

config.$inject = ['$routeProvider', '$httpProvider', '$locationProvider'];
function config($routeProvider, $httpProvider, $locationProvider) {
   $routeProvider
      .when('/', {
         controller: 'HomeController',
         templateUrl: 'modulos/home/home.html',
         controllerAs: 'vm'
      })

      .when('/usuarios', {
         controller: 'ABCPersonalController',
         templateUrl: 'modulos/abc-usuarios/abc-usuarios.html',
         controllerAs: 'vm'
      })

      .when('/rol', {
         controller: 'ABCRolController',
         templateUrl: 'modulos/abc-rols/abc-rols.html',
         controllerAs: 'vm'
      })

      .when('/entrar', {
         controller: 'LoginController',
         templateUrl: 'modulos/auth/login/login.html',
         controllerAs: 'vm'
      })

      .otherwise({ redirectTo: '/entrar' });

   // $locationProvider.html5Mode(true);

   $httpProvider.interceptors.push(($q, $location) => {
      return {
         request: config => {
            // Si el endpoint es de la API se le agrega el token de sesión a la petición
            if (config.url.startsWith(API_ENDPOINT)) {
               // Obtiene un arreglo con el [endpoint, params]
               const route = config.url.substr(API_ENDPOINT.length).split('?');
               const oldParams = route.length > 1
                  ? `?${route[1]}&`
                  : '?';

               const newParams = `route=${route[0]}&token=${USER_TOKEN()}`;
               config.url = `${API_ENDPOINT}${oldParams}${newParams}`;
            }
            config.headers['Content-Type'] = 'text/plain';
            return config;
         },
         responseError: res => {
            if(res.status === 401 || res.status === 403) {
               alert('Acceso no autorizado.\nInicie sesión nuevamente');
               $location.path('/entrar');
            }
            return $q.reject(res);
         }
      };
   });
}

run.$inject = ['$rootScope', '$location', '$cookies'];
function run($rootScope, $location) {
   $rootScope.$on('$locationChangeStart', function (event, next, current) {
      // Cerrar el sidenav al cambiar de ruta
      if (angular.isDefined($rootScope.isOpenRight) && $rootScope.isOpenRight())
         $rootScope.toggleRight();

      // Redirecciona a la pagina de login si no está logueado e intenta acceder a una ruta restringida
      const restrictedPage = $.inArray($location.path(), ['/entrar']) < 0;
      const isLogged = USER_TOKEN() || false;
      const loadedData = Object.keys(USER_INFO()).length > 0;
      $rootScope.isLogged = isLogged;

      if (restrictedPage && !isLogged) {
         $rootScope.isLogged = false;
         $location.path('/entrar');
      }

      if (isLogged && !loadedData) $rootScope.AuthenticationService.GetUser(USER_TOKEN());
   });
}