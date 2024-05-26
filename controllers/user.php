<?php

require 'model/UserTable.php';
require 'utils/Utilities.php';

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
                                "uuid"=>$user->getUuid(),
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

        $parameters = file_get_contents('php://input');
        $decodedParameters = json_decode($parameters, true);

        //tomar el token del cuerpo de la petición en formato json
        if (json_last_error() != JSON_ERROR_NONE) {
            $internalServerError = new ApiException(
                500,
                0,
                "Error al decodificar el JSON de solicitud",
                "http://localhost",
                "Error de parsing JSON. Causa: " . json_last_error_msg());
            throw $internalServerError;
        }

        if (!isset($decodedParameters["token"])) {
            throw new ApiException(
                401,
                1012,
                "Operación no permitida",
                "http://localhost",
                "El atributo \"token\" esta vacio o no definido"
            );
        }

        $token = $decodedParameters["token"];

        
        if (strlen($token) == 36 && $token === self::$validationToken){ //verificar que el token este incluido en la solicitud
            $pdo = self::initConnection();
            switch ($urlSegments[0]) {
                case "create":
                    $email = $decodedParameters["email"];
                    if(trim($email) == ""){ //Prevedir que el email este en blanco
                        throw new ApiException(
                            400,
                            1016,
                            "Error de operación",
                            "http://localhost",
                            "El email es obligatorio"
                        );
                    }
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Prevenir que se ingrese un correo invalido
                        throw new ApiException(
                            400,
                            1016,
                            "Error de operación",
                            "http://localhost",
                            "El email ingresado no corresponde a un correo electronico valido"
                        );
                    }
                    //campos opcionales
                    $fullname = $decodedParameters["fullname"];
                    $address = $decodedParameters["address"];
                    $birthdate = $decodedParameters["birthdate"];
                    $uuid = Utilities::generateUuid(); //generar clave uuid
                    $newUser = new UserTable(
                        0,
                        $uuid,
                        $fullname,
                        $email,
                        $address,
                        $birthdate
                    );
                    if($newUser->validateUser($pdo)){ //corroborar que no se repita el email
                        if($newUser->create($pdo)){ //crear usuario
                            $newUser->get($pdo);
                            return [
                                "status" => 200,
                                "message" => "success",
                                "data" => [
                                    "id"=>$newUser->getId(),
                                    "uuid"=>$newUser->getUuid(),
                                    "fullname"=>$newUser->getFullname(),
                                    "email"=>$newUser->getEmail(),
                                    "address"=>$newUser->getAddress(),
                                    "birthdate"=>$newUser->getBirthdate(),
                                    "created_at"=>$newUser->getCreatedAt(),
                                    "updated_at"=>$newUser->getUpdatedAt(),
                                ]
                            ];
                        }else{
                            throw new ApiException(
                                500,
                                1012,
                                "Error en la operación",
                                "http://localhost",
                                "No se pudo completar la solicitud"
                            );
                        }
                    }else{
                        throw new ApiException(
                            400,
                            1016,
                            "Error de operación",
                            "http://localhost",
                            "El email se encuentra registrado por otro usuario"
                        );
                    }
                    break;
                case "update":
                    if (!isset($decodedParameters["uuid"])){ //verificar que el uuid este incluido en la solicitud
                        throw new ApiException(
                            400,
                            1015,
                            "Error de operación",
                            "http://localhost",
                            "El atributo \"uuid\" esta vacio o no definido"
                        );
                    }
                    $uuid = $decodedParameters["uuid"];
                    $user = new UserTable(
                        0,
                        $uuid
                    );
                    if($user->get($pdo)){
                        //actualizar datos del usuario

                        $email = $decodedParameters["email"];
                        if(trim($email) == ""){ //Prevedir que el email este en blanco
                            throw new ApiException(
                                400,
                                1016,
                                "Error de operación",
                                "http://localhost",
                                "El email es obligatorio"
                            );
                        }
                        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Prevenir que se ingrese un correo invalido
                            throw new ApiException(
                                400,
                                1016,
                                "Error de operación",
                                "http://localhost",
                                "El email ingresado no corresponde a un correo electronico valido"
                            );
                        }

                        $currentEmail = $user->getEmail();
                        if(trim($email) == "") $email = $currentEmail; //Prevedir que el email quede en blanco en caso de modificar
                        $fullname = $decodedParameters["fullname"];
                        $address = $decodedParameters["address"];
                        $birthdate = $decodedParameters["birthdate"];

                        $updateEmail = ($currentEmail != $email); //corroborar si se va a modificar el correo
                        $user->setEmail($email);

                        if($updateEmail){
                            if(!$user->validateUser($pdo)){ //corroborar que no se repita el email
                                throw new ApiException(
                                    400,
                                    1016,
                                    "Error de operación",
                                    "http://localhost",
                                    "El email se encuentra registrado"
                                );
                            }
                        }
                        $user->setFullname($fullname);
                        $user->setAddress($address);
                        $user->setBirthdate($birthdate);

                        if($user->update($pdo, $updateEmail)){ //actualizar usuario
                            return [
                                "status" => 200,
                                "message" => "success",
                                "uuid" => $uuid,
                                "data" => null
                            ];
                        }else{
                            throw new ApiException(
                                500,
                                1012,
                                "Error en la operación",
                                "http://localhost",
                                "No se pudo completar la solicitud"
                            );
                        }
                    }else{
                        return [
                            "status" => 203,
                            "message" => "user not found",
                            "uuid" => $uuid,
                            "data" => null
                        ];
                    }
                    break;
                case "delete":
                    if (!isset($decodedParameters["uuid"])){ //verificar que el uuid este incluido en la solicitud
                        throw new ApiException(
                            400,
                            1014,
                            "Error de operación",
                            "http://localhost",
                            "El atributo \"uuid\" esta vacio o no definido"
                        );
                    }
                    $uuid = $decodedParameters["uuid"];
                    $user = new UserTable(
                        0,
                        $uuid
                    );
                    if($user->get($pdo)){
                        if($user->delete($pdo)){ //borrar usuario identificado
                            return [
                                "status" => 200,
                                "message" => "success",
                                "uuid" => $uuid,
                                "data" => null
                            ];
                        }else{
                            throw new ApiException(
                                500,
                                1012,
                                "Error en la operación",
                                "http://localhost",
                                "No se pudo completar la solicitud"
                            );
                        }
                    }else{
                        return [
                            "status" => 203,
                            "message" => "user not found",
                            "uuid" => $uuid,
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