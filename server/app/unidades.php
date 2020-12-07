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
            $consulta=$GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT UNIDADES_CODIGO, UNIDADES_NOMBRE,(SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = UNIDADES_PERMISIONARIO) AS PERMISIONARIO FROM datos_unidades WHERE UNIDADES_ELIMINADA = 0 ORDER BY UNIDADES_NOMBRE");
            $arr = Array();
            if($consulta){
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("codigo" => $actual['UNIDADES_CODIGO'], "nombre" => $actual["UNIDADES_NOMBRE"], "permisionario" => $actual["PERMISIONARIO"]));
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

    /**
     * Funcion para agregar unidad
     * 
     * @param Object $datos Objeto con los valores de la unidad
     * @return Array Array con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function add_unidad($datos)
    {
        if(!validar_parametros_option($datos, ["token", "nombre","codigo","permisionario"], 4))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($datos->token);
        if($validar)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_unidades (UNIDADES_ANADIDO, UNIDADES_CODIGO, UNIDADES_NOMBRE, UNIDADES_PERMISIONARIO) VALUES ({$validar['id']}, '{$datos->codigo}', '{$datos->nombre}', {$datos->permisionario})");
            if($consulta)
            {
                return Array("mensaje" => "ok");
            }
        }
        return Array("mensaje" => "error interno");
    }
?>