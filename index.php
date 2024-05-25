<?php

//Index principal del api

//importación requerida del controlador para las llamadas, el retorno de vistas y control de excepciones
require 'controllers/user.php';
require 'views/JsonView.php';
require 'utils/ApiException.php';

//inicialización de la vista del JSON
$apiView = new JsonView();

