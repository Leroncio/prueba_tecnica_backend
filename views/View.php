<?php

//Renderizado de retorno para las respuestas
abstract class View {
    public abstract function render($body);
}