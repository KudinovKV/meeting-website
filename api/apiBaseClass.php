<?php
class apiBaseClass {
    
    public $mySQLWorker=null;//Одиночка для работы с базой
    
    //Конструктор с возможными параметрами
    function __construct($dbUser=null,$dbPassword=null) {
        ;
    }
    
    function __destruct() {
        ;
    }
    
    //Создаем дефолтный JSON для ответов
    function createDefaultJson() {
        $retObject = json_decode('{}');
        return $retObject;
    }
    
    //Заполняем JSON объект по ответу из MySQLiWorker
}

?>