<?php
    require_once("./bin/db.php");
    include_once("./bin/log.php");
    $GLOBALS['LOG']['CRON'] = new Log("./log/CRON-".date("Y-m-d").".txt" );
    $fecha = date("Y-m-d H:i:s");
    $GLOBALS["LOG"]["CRON"]->write("INICIANDO TAREA CRONOMETRADA");
    $consultas = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT ");
?>