<?php

//Recurso user (esta en minusculas para ser utilizado en la url de la api)
class user {

    public static function get($urlSegments) {
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
            case "all":
                return [
                    "status" => 200,
                    "total" => 200
                ];
                break;
            default:
                throw new ApiException(
                    404,
                    0,
                    "El recurso al que intentas acceder no existe",
                    "http://localhost", "No se encontró el segmento {".get_class()."/$urlSegments[0]}");
        }
    }

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

    public static function put($urlSegments) {
        throw new ApiException(
            405,
            1001,
            "Acción no permitida",
            "http://localhost",
            "No se puede aplicar el método $_SERVER[REQUEST_METHOD] sobre el recurso \"$urlSegments\"");
    }

    public static function delete($urlSegments) {
        throw new ApiException(
            405,
            1001,
            "Acción no permitida",
            "http://localhost",
            "No se puede aplicar el método $_SERVER[REQUEST_METHOD] sobre el recurso \"$urlSegments\"");
    }

}