<?php

    /**
     * Funcion para pedir todas las unidades
     * @param String $token El token de acceso
     * @return Array Un array con los resultados de la operacion
     * 
     * @author Juanjo Romero
     */
    function get_all_unidades($token){
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);
        if($validar)
        {
            $consulta=$GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT UNIDADES_CODIGO, UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ELIMINADA=0");
            $arr = Array();
            if($consulta){
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("codigo" => $actual['UNIDADES_CODIGO'], "nombre" => $actual["UNIDADES_NOMBRE"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
            return Array("mensaje" => "Sin datos");
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para llamar a todas las rutas
     * @param String $token El token para verificar sus accesos
     * @return Array Un array con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function get_all_rutas($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);
        if($validar)
        {
            $consulta=$GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT RUTAS_CODIGO, RUTAS_RUTA FROM datos_rutas");
            $arr = Array();
            if($consulta){
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("codigo" => $actual["RUTAS_CODIGO"], "ruta" => $actual["RUTAS_RUTA"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
            return Array("mensaje" => "Sin datos");
        }
        return Array("mensaje" => "error");
    }
?>