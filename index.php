<?php

//Index principal del api

//importación requerida del controlador para las llamadas, el retorno de vistas y control de excepciones
require 'data/MySqlManager.php';
require 'controllers/user.php';
require 'views/JsonView.php';
require 'utils/ApiException.php';

//inicialización de la vista del JSON
$apiView = new JsonView();

//Fijar excepciones personalizadas (Archivo ApiException.php)
set_exception_handler(
    function (ApiException $exception) use ($apiView) {
        http_response_code($exception->getStatus());
        $apiView->render($exception->toArray());
    }
);

//Cuerpo de excepción
$generalError = new ApiException(
    404,
    1000, 
    "El recurso al que intentas acceder no existe", 
    "http://localhost",
    "No existe un resource definido en: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
);

//Recurso de busqueda de la api
if (isset($_GET['PATH_INFO'])) {
    $urlSegments = explode('/', $_GET['PATH_INFO']);
} else {
    throw $generalError;
}

$resource = array_shift($urlSegments);

//Recursos disponibles para la api
$apiResources = array('user');

//Verificar que el recurso existe
if (!in_array($resource, $apiResources)) {
    throw $generalError;
}

$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
//switch de acción de la api
switch ($httpMethod) {
    //Métodos permitidos en la api
    case 'get':
    case 'post':
        if (method_exists($resource, $httpMethod)) {
            $apiResponse = call_user_func(array($resource, $httpMethod), $urlSegments);
            $apiView->render($apiResponse);
            break;
        }
    case 'put':
    case 'delete':
    default:
        // Método no permitido sobre el recurso desactivados metodos put y delete ya que requieren de configuración adicional
        $methodNotAllowed = new ApiException(
            405,
            1001,
            "Acción no permitida",
            "http://localhost",
            "No se puede aplicar el método $_SERVER[REQUEST_METHOD] sobre el recurso \"$resource\"");
        $apiView->render($methodNotAllowed->toArray());
}