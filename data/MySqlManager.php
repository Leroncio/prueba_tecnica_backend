<?php

//importe de parametros de archivo de configuración
require_once 'Parameters.php';

class MySqlManager{

    private static $mySqlManager = null;

    private static $pdo; //instancia para PDO

    final private function __construct()
    {
        //constructor de instancia automática del manager
        try{
            self::init();
        }catch(PDOException $exception){
            //retornar errores de conexión
        }
    }

    public static function get(){
        if(self::$mySqlManager === null){
            self::$mySqlManager = new self(); //instancia automática de la clase
        }
        return self::$mySqlManager; //retorno del manager instanciado
    }

    public function init(){
        if(self::$pdo === null){
            //construcción del string de conexión
            $databseString = sprintf('mysql:dbname=%s;host=%s',Parameters::DATABASE,Parameters::HOST);
            $PDOoptions = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
            );
            //instanciar la conexión
            self::$pdo = new PDO(
                $databseString, 
                Parameters::USER, 
                Parameters::PASSWORD, 
                $PDOoptions
            );
        }
        return self::$pdo;
    }

    function _destructor() {
        self::$pdo = null; //destruye la conexión activa
    }


}