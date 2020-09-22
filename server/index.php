<?php
    $GLOBALS['SYSTEM_PATH'] = "/";
    
    require_once("./bin/db.php");
    require_once("./conf/conf.php");
    require_once("./funciones.php");

    $URI = $_SERVER['REQUEST_URI'];
    
    $RESPUESTA = "";

    switch($URI)
    {
        case "login":
            $RESPUESTA = login();
        break;
    }

?>