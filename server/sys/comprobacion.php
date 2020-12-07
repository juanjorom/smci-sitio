<?php
    // Incluir los archivos de configuracion
    require_once("./../bin/db.php");
    include_once("./../bin/log.php");
    require_once("cronometer_funcion.php");
    // Iniciar la bitacora
    $GLOBALS['LOG']['CRON'] = new Log("./log/CRON-".date("Y-m-d").".txt" );
    $inicio = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));
    $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "INICIANDO TAREA CRONOMETRADA");
    $consultas = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_ID AS ID, VUELTAS_NUMERO AS NUMERO, VUELTAS_BRUTO AS BRUTO, (SELECT SUM(BOLETERAS_TOTAL_BOL) FROM datos_boleteras WHERE BOLETERAS_VUELTA = VUELTA_ID) AS BOLETOS  WHERE   FROM datos_vueltas WHERE VUELTAS_CERRADA > 0 AND VUELTAS_FECHA_HORA_FIN BETWEEN '{$inicio}' AND  NOW()");
    $salida = Array();
    if($consultas)
    {
        $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "Consultas encontradas: " .count($consultas));
        foreach($consultas as $actual)
        {
            $motivo = revision_vuelta($actual);
            if($motivo)
            {
                $insertar = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO cron_revision (REVISION_VUELTA, REVISION_ESTADO, REVISION_MOTIVO) VALUES ({$actual['ID']}, 'NOTIFICADO', '{$motivo}')");
                if($insertar)
                {
                    $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "Vuelta agregada: " . $actual["ID"]);
                    array_push( $salida,date("Y-m-d H:i:s") . "Vuelta agregada: " . $actual["ID"]);
                }
            }
            else
            {
                $boleteras = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT BOLETERAS_ID AS ID, BOLETERAS_TOTAL_BOL AS TOTAL, BLETERAS_MONTO AS MONTO, BOLETERAS_BOLETOS AS BOLETOS FROM datos_boleteras WHERE BOLETERAS_VUELTA = {$actual['ID']}");
                if($boleteras)
                {
                    $sumas = Array();
                    foreach($boleteras as $bol)
                    {
                        $revisar = revision_boletera($bol);
                        foreach(array_keys($revisar) as $esta)
                        {
                            $sumas[$esta]+= $revisar[$esta];
                        }
                    }
                    $estadistica = Array();
                    $keys = array_keys($sumas);
                    sort($keys);
                    foreach($keys as $sumita)
                    {
                        $estadistica[$sumita] = ($sumas[$sumita]*100)/$actual["BOLETOS"];
                        $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "Porcentaje de boletos de ". $sumita ." " . $estadistica[$sumita]);
                    }

                    $determinar = revision_fondo($estadistica);
                    if($determinar)
                    {
                        $insertar =$GLOBALS["DB"]->ejecutar_consulta("INSERT INTO cron_revision (REVISION_VUELTA, REVISION_ESTADO, REVISION_MOTIVO) VALUES ({$actual['ID']}, 'NOTIFICADO', '{$determinar}')");
                        if($insertar)
                        {
                            $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "Vuelta agregada: " . $actual["ID"]);
                            array_push( $salida,date("Y-m-d H:i:s") . "Vuelta agregada: " . $actual["ID"]);
                        }
                    }
                }
            }
            
        }
    }
    $GLOBALS["LOG"]["CRON"]->write(date("Y-m-d H:i:s") . "Algoritmo Terminado");
    if(count($salida))
    {
        print_r($salida);
    }
?>