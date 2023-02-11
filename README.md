# Plantilla de Proyecto Fullstack con AngularJS y PHP


## Para uso local
### Consideraciones:
• Tener instalado XAMPP con PHP 7.2 o superior.  
• Es necesario tener el backend y el frontend en virtual hosts distintos configurados en XAMPP y host de la PC.

### 1. (En Windows) Abrir Bloc de Notas en modo Administrador y buscar el archivo __C:\Windows\System32\drivers\etc\hosts__ y agregar las siguientes líneas al final del archivo.

      ...
        127.0.0.1       template.loc
        127.0.0.1       api.template.loc


### 2. Abrir el archivo __C:\xampp\apache\conf\extra\httpd-vhosts.conf__ y agregar las siguientes líneas al final del archivo.

      <VirtualHost template.loc>
         DocumentRoot "C:\ruta\al\proyecto\angluarApiTemplate\frontend"
         ServerName template.loc
      </VirtualHost>

      <VirtualHost api.template.loc>
         DocumentRoot "C:\ruta\al\proyecto\angluarApiTemplate\backend"
         ServerName api.template.loc
      </VirtualHost>

### 3. Reiniciar el servicio de Apache en el panel de control de XAMPP

### 4. Abrir el navegador e ir a la ruta __http://template.loc__

<br>
<br>


## Para despliegue a producción
### Consideraciones:
• Usar una versión de PHP 7.2 o superior  
• Es necesario tener el backend y el frontend en dominios distintos, ya sea el frontend en el dominio base y el backend en un subdominio del mismo.

<br>

### 1. Migrar la base de datos al hosting.
### 2. Abrir el archivo __./backend/\_InitConf\_.php__

2.1 Definir el entorno de trabajo en PROD

      ...
      define('ENV', 'PROD');	# ENTORNO DE TRABAJO -> DEV | PROD | SB
      ...

2.2 Dentro de la función switch, en el case PROD definir:  
   ✔ __ORIGIN__: El orígen de las peticiones HTTP el cual debe ser la URL base del frontend  
   ✔ __URL__: La URL base del backend  
   ✔ __DB_HOST__: URL hosting de la BD (normalmente __localhost__)  
   ✔ __DB_USER__: Nombre de usuario de la BD  
   ✔ __DB_PASS__: Contraseña de la BD  
   ✔ __DB_NAME__: Nombre de la BD  

      ...
      switch (ENV) {
         ...
         case 'PROD':
            define('ORIGIN', '');
            define('URL', '');
            define('DB_HOST', '');
            define('DB_USER', '');
            define('DB_PASS', '');
            define('DB_NAME', '');
         break;

2.3 En la sección Ubicación de Archivos puede definir la ruta de las carpetas del sistema donde se guardarán los archivos generados por el sistema como PDF's. Estas deben coincidir con la estructura de carpetas dentro de __./backend/docs/__.  

      // UBICACIÓN DE LOS ARCHIVOS
      define('LOCATION_QR', 'docs/qr/');
      define('LOCATION_INVOICE', 'docs/invoice/');
      define('LOCATION_CLOSING', 'docs/closing/');

2.4 En la sección App se definen datos de ubicación, información básica de la empresa, correos asociados al dominio principal e información de Google Captcha si se implementa.

      // APP
      define('ESTADO', '');
      define('MUNICIPIO', '');
      define('NAME', '');
      define('RAZON_SOCIAL', '');
      define('RIF', '');
      define('DIRECCION', '');
      define('TELEFONOS', '');
      define('EMAIL_NOREPLY', 'no-responder@dominio.com');
      define('EMAIL_SOPORTE', 'soporte@dominio.com');
      define('EMAIL_PAGOS', 'pagos@dominio.com');
      define('SITE_KEY', '');
      define('SECRET_KEY', '');

2.5 En la sección Tiempo se define el Huso Horario a implementar en la BD

      // TIEMPO
      define('ZONA_HORARIA', 'America/Monterrey');
      date_default_timezone_set(ZONA_HORARIA);
      define('HOY', date('Y-m-d H:i:s'));
      setlocale(LC_TIME, 'es_ES', 'esp_esp');


### 3. Abrir el archivo ./frontend/vendor/js/definitions.js y modificar el valor de API_ENDPOINT con la URL de la API (paso 2.2 -> URL).

      const API_ENDPOINT = 'http://api.template.loc/';
