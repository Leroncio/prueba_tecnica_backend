<?php

require 'model/UserTable.php';

//Recurso user (esta en minusculas para ser utilizado en la url de la api)
class user {

    //token de validación para las request
    //utilice un token fijo ya que el sistema no cuenta con un sistema de login usuarios
    //a los cuales asignarles un token especifico por login
    private static string $validationToken = "aaaaaaaa-1234-1234-cc12-a1a1a1a1a1a1";

    //Llamadas de segmento en peticiones GET
    public static function get($urlSegments) {
        if (!isset($urlSegments[0])) {
            throw new ApiException(
                400,
                1011,
                "El recurso está mal referenciado",
                "http://localhost",
                "El recurso $_SERVER[REQUEST_URI] no esta sujeto a resultados"
            );
        }

        //captura de parametros para comprobación del token
        if (!isset($_GET["token"])){ //verificar que el token este incluido en la solicitud
            throw new ApiException(
                401,
                1012,
                "Operación no permitida",
                "http://localhost",
                "El atributo \"token\" esta vacio o no definido"
            );
        }

        $token = $_GET["token"];
        if (strlen($token) == 36 && $token === self::$validationToken){ //verificar que el token este incluido en la solicitud
            $pdo = self::initConnection();
            switch ($urlSegments[0]) {
                case "all"://endpoint de retorno de todos los usuarios
                    $allUser = UserTable::all($pdo);
                    return [
                        "status" => 200,
                        "message" => "success",
                        "total" => count($allUser),
                        "users" => $allUser
                    ];
                    break;
                case "find"://endpoint busqueda de usuario por uuid
                    if (!isset($_GET["search"])){ //verificar que la busqueda por uuid este incluido en la solicitud
                        throw new ApiException(
                            400,
                            1013,
                            "Error de operación",
                            "http://localhost",
                            "El atributo \"search\" esta vacio o no definido"
                        );
                    }
                    $uuid = $_GET["search"];
                    $user = new UserTable(
                        0,
                        $uuid
                    );
                    if($user->get($pdo)){
                        return [
                            "status" => 200,
                            "message" => "success",
                            "search" => $uuid,
                            "data" => [
                                "id"=>$user->getId(),
                                "uuid"=>$user->getId(),
                                "fullname"=>$user->getFullname(),
                                "email"=>$user->getEmail(),
                                "address"=>$user->getAddress(),
                                "birthdate"=>$user->getBirthdate(),
                                "created_at"=>$user->getCreatedAt(),
                                "updated_at"=>$user->getUpdatedAt(),
                            ]
                        ];
                    }else{
                        return [
                            "status" => 203,
                            "message" => "user not found",
                            "search" => $uuid,
                            "data" => null
                        ];
                    }
                    break;
                default:
                    throw new ApiException(
                        404,
                        0,
                        "El recurso al que intentas acceder no existe",
                        "http://localhost", "No se encontró el segmento {".get_class()."/$urlSegments[0]}");
            }
        }else{
            throw new ApiException(
                401,
                1012,
                "Operación no permitida",
                "http://localhost",
                "El atributo \"token\" es invalido"
            );
        }
    }

    //Llamadas de segmento en peticiones POST
    public static function post($urlSegments) {
        if (!isset($urlSegments[0])) {
            throw new ApiException(
                400,
                0,
                "El recurso está mal referenciado",
                "http://localhost",
                "El recurso $_SERVER[REQUEST_URI] no esta sujeto a resultados"
            );
        }
        switch ($urlSegments[0]) {
            default:
                throw new ApiException(
                    404,
                    0,
                    "El recurso al que intentas acceder no existe",
                    "http://localhost", "No se encontró el segmento {".get_class()."/$urlSegments[0]}");
        }
    }

    //Llamadas de segmento en peticiones PUT (Deshabilitadas)
    public static function put($urlSegments) {
        throw new ApiException(
            405,
            1001,
            "Acción no permitida",
            "http://localhost",
            "No se puede aplicar el método $_SERVER[REQUEST_METHOD] sobre el recurso \"$urlSegments\"");
    }

    //Llamadas de segmento en peticiones DELETE (Deshabilitadas)
    public static function delete($urlSegments) {
        throw new ApiException(
            405,
            1001,
            "Acción no permitida",
            "http://localhost",
            "No se puede aplicar el método $_SERVER[REQUEST_METHOD] sobre el recurso \"$urlSegments\"");
    }

    private static function initConnection() : PDO{
        $pdo = MySqlManager::get()->init();
        return $pdo;
    }

}