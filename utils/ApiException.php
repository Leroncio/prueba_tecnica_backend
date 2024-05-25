<?php

class ApiException extends Exception {

    //Parametros estandarizados a devolver
    private int $status;
    private int $apiCode;
    private string $userMessage;
    private string $moreInfo;
    private string $developerMessage;

    public function __construct($status, $code, $message, $moreInfo, $developerMessage) {
        $this->status = $status;
        $this->apiCode = $code;
        $this->userMessage = $message;
        $this->moreInfo = $moreInfo;
        $this->developerMessage = $developerMessage;
    }

    //getters de estado
    public function getStatus() : int
    {
        return $this->status;
    }

    public function getApiCode()  : int
    {
        return $this->apiCode;
    }

    public function getUserMessage() : string {
        return $this->userMessage;
    }

    public function getMoreInfo() : string {
        return $this->moreInfo;
    }

    public function getDeveloperMessage() : string {
        return $this->developerMessage;
    }

    //retorno de la excepción personalizada
    public function toArray() : array {
        $errorBody = array(
            "status" => $this->status,
            "code" => $this->apiCode,
            "message" => $this->userMessage,
            "moreInfo" => $this->moreInfo,
            "developerMessage" => $this->developerMessage
        );
        return $errorBody;
    }
}