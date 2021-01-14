<?php
    /**
     * Funcion para pedir las ventas de todas las unidades
     * 
     * @param String $token El token de verificacion
     * @return Array El array con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function get_ventas_unidades($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT SUM(TURNOS_VENTA) AS VENTA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = TURNOS_UNIDAD) AS UNIDAD FROM datos_turnos GROUP BY TURNOS_UNIDAD");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("valor" => $actual["VENTA"], "unidad" => $actual["UNIDAD"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para pedir las ventas de todas las unidades
     * 
     * @param String $token El token de verificacion
     * @return Array El array con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function get_ventas_fecha($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT SUM(VUELTAS_BRUTO) AS VENTA, DATE_FORMAT(VUELTAS_FECHA_HORA_FIN, '%Y-%m-%d') AS FECHA FROM datos_vueltas WHERE VUELTAS_ESTADO <> 'ABIERTA'  GROUP BY FECHA");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("valor" => $actual["VENTA"], "unidad" => $actual["UNIDAD"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para llamar todas las vueltas cerradas
     * @param String $token El token de acceso del usuario
     * @param String $inicio Fecha de inicio del reporte
     * @param String $inicio Fecha final del reporte
     * @return Array Un arreglo con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function get_vueltas_closed($token, $inicio, $fin)
    {
        if(!validar_parametros_get([$token, $inicio, $fin], 3))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $GLOBALS["LOG"]["DB"]->write("Los parametros son ". $inicio . " y " . $fin);
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_FECHA_HORA_INICIO AS INICIO, VUELTAS_FECHA_HORA_INICIO AS FIN, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CAJERA) AS ABRIO, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = VUELTAS_UNIDAD) AS UNIDAD, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = VUELTAS_RUTA) AS RUTA, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CHOFER) AS CHOFER, VUELTAS_ESTADO AS ESTADO, VUELTAS_NUMERO AS NUMERO, VUELTAS_BRUTO AS BRUTO, VUELTAS_GASTOS AS GASTOS, VUELTAS_MONTO AS MONTO, VUELTAS_ENTREGADO AS ENTREGADO, VUELTAS_COMISION AS COMISION, VUELTAS_COMENTARIOS AS COMENTARIOS, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CERRADA) AS CERRO FROM datos_vueltas WHERE VUELTAS_ESTADO != 'ABIERTA' AND VUELTAS_FECHA_HORA_FIN BETWEEN '{$inicio}' AND '{$fin}'");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("inicio" => $actual["INICIO"], "fin" => $actual["FIN"], "abrio" => $actual["ABRIO"], "unidad" => $actual["UNIDAD"], "ruta" => $actual["RUTA"], "chofer" => $actual["CHOFER"], "estado" => $actual["ESTADO"], "numero" => $actual["NUMERO"], "bruto" => $actual["BRUTO"], "gastos" => $actual["GASTOS"], "monto" => $actual["MONTO"], "entregado" => $actual["ENTREGADO"], "comision" => $actual["COMISION"], "comentarios" => $actual["COMENTARIO"], "cerro" => $actual["CERRO"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
            return Array("mensaje" => "error de query");
        }
        return Array("mensaje" => "error de acceso");
    }
?>