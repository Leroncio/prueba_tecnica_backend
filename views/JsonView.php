<?php

//importacion del render
require "View.php";

//Clase de impresion de vista en formato json
class JsonView extends View {

    public function render($body) {

        // Set de estado de le respuesta
        if (isset($body["status"])) {
            http_response_code($body["status"]);
        }

        // Set del contenido de la respuesta
        header('Content-Type: application/json; charset=utf8');

        // Encodificado y configuracion de propiedades del JSON
        $jsonResponse = json_encode(
            $body, 
            JSON_PRETTY_PRINT, 
            JSON_UNESCAPED_UNICODE
        );

        if (json_last_error() != JSON_ERROR_NONE) {
            //Retornar Error
        }

        //imprimir el JSON
        echo $jsonResponse;
        exit;
    }
}
