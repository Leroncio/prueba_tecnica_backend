## Prueba técnica BackEnd
_Prueba técnica para Grupo avanza presentada por Eduardo Araya proyecto backend._

## Requisitos del proyecto 🔧
* [PHP 8.2](https://www.php.net/releases/8.2/en.php) - Lenguaje utilizado
* [MySql 8.2.13](https://dev.mysql.com/downloads/connector/php-mysqlnd) - Versión de la base de datos


## Instrucciones de ejecución 🚀

- Descargar o clonar el package del proyecto
- Descomprimir, copiar o mover a la carpeta de destino para producción

## Instrucciones de instalación de base de datos

Importaremos el archivo `user_list.sql` en nuestra base de datos y ejecutaremos las sentencias de import, las sentencias de creación se encuentran incluidas en este archivo.
Esto debería de crear la estructura de datos necesaria para el proyecto.
En caso de no disponer de funciones de import puede copiar el archivo completo de base de datos y ejecutarlo dentro del intérprete de mysql.

## Configuración del proyecto

Los parámetros de conexión entre en proyecto y la base de datos se encuentran en la ruta: `{root}/data/Parameters.php` en caso de utilizar datos de conexión diferentes a los por defecto de mysql deben ser modificados en este archivo.

```html
    const HOST = "localhost"; 
    const USER = "root";
    const PASSWORD = "12345678";
    const DATABASE = "user_list";
```

## Detalle adicional ✒️

_El sitio debería ser montado en la carpera `www` de su servidor local y la ruta para el consumo de las request de la api debería quedar algo asi:_

```
http://localhost/{nombre_carpeta_proyecto}/user
```
_En el repositorio se incluye una colección de request en Postman para pruebas de forma directa con la estructura de las mismas_

_El proyecto no cuenta validación de usuarios o sesiones por lo que utiliza un token estático, este sería:_

```
aaaaaaaa-1234-1234-cc12-a1a1a1a1a1a1
```