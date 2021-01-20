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
     * @param String $inicio Fecha de inicio
     * @param String $fin La fecha final
     * @param String $filtro Parametro por el que se buscara
     * @return Array El array con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function get_ventas_fecha($token, $inicio, $fin, $filtro)
    {
        if(!validar_parametros_get([$token, $inicio, $fin, $filtro], 4))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            //$consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT DATE_FORMAT(VUELTAS_FECHA_HORA_FIN, '%Y-%m-%d') AS FECHA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = VUELTAS_UNIDAD) AS UNIDAD,  (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = VUELTAS_RUTA) AS RUTA, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CHOFER) AS CHOFER, COUNT(VUELTAS_NUMERO) AS VUELTAS, SUM(VUELTAS_BOLETOS) AS BOLETOS, SUM(VUELTAS_BRUTO) AS VENTA, SUM(VUELTAS_GASTOS) AS GASTOS, SUM(VUELTAS_MONTO) AS MONTO, SUM(VUELTAS_COMISION) AS COMISION, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios INNER JOIN datos_unidades ON UNIDADES_PERMISIONARIO = USUARIOS_ID WHERE UNIDADES_ID = VUELTAS_UNIDAD) AS PERMISIONARIO FROM datos_vueltas WHERE VUELTAS_ESTADO <> 'ABIERTA' AND VUELTAS_FECHA_HORA_FIN BETWEEN '{$inicio}' AND '{$fin}' GROUP BY {$filtro}");
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT DATE_FORMAT(TURNOS_FIN, '%Y-%m-%d') AS FECHA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = TURNOS_UNIDAD) AS UNIDAD,  (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = TURNOS_RUTA) AS RUTA, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = TURNOS_CHOFER) AS CHOFER, COUNT(TURNOS_VUELTAS) AS VUELTAS, SUM(TURNOS_BOLETOS) AS BOLETOS, SUM(TURNOS_VENTA) AS VENTA, SUM(TURNOS_GASTOS) AS GASTOS, SUM(TURNOS_RECAUDADO) AS MONTO, SUM(TURNOS_UTILIDAD) AS UTILIDAD, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios INNER JOIN datos_unidades ON UNIDADES_PERMISIONARIO = USUARIOS_ID WHERE UNIDADES_ID = TURNOS_UNIDAD) AS PERMISIONARIO FROM datos_turnos WHERE TURNOS_ESTADO <> 'ABIERTA' AND TURNOS_FIN BETWEEN '{$inicio}' AND '{$fin}' GROUP BY {$filtro}");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("valor" => $actual["VENTA"], "fecha" => $actual["FECHA"], "vueltas" => $actual["VUELTAS"], "gastos" => $actual["GASTOS"], "recaudado" => $actual["MONTO"], "utilidad" => $actual["UTILIDAD"], "unidad" => $actual["UNIDAD"], "ruta" => $actual["RUTA"], "chofer" => $actual["CHOFER"], "permisionario" => $actual["PERMISIONARIO"], "boletos" => $actual["BOLETOS"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para llamar todas las vueltas cerradas
     * 
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
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_FECHA_HORA_INICIO AS INICIO, VUELTAS_FECHA_HORA_INICIO AS FIN, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CAJERA) AS ABRIO, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = VUELTAS_UNIDAD) AS UNIDAD, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = VUELTAS_RUTA) AS RUTA, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CHOFER) AS CHOFER, VUELTAS_ESTADO AS ESTADO, VUELTAS_NUMERO AS NUMERO, VUELTAS_BOLETOS AS BOLETOS, VUELTAS_BRUTO AS BRUTO, VUELTAS_GASTOS AS GASTOS, VUELTAS_MONTO AS MONTO, VUELTAS_ENTREGADO AS ENTREGADO, VUELTAS_COMISION AS COMISION, VUELTAS_COMENTARIOS AS COMENTARIOS, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CERRADA) AS CERRO FROM datos_vueltas WHERE VUELTAS_ESTADO != 'ABIERTA' AND VUELTAS_FECHA_HORA_FIN BETWEEN '{$inicio}' AND '{$fin}'");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("inicio" => $actual["INICIO"], "fin" => $actual["FIN"], "abrio" => $actual["ABRIO"], "unidad" => $actual["UNIDAD"], "ruta" => $actual["RUTA"], "chofer" => $actual["CHOFER"], "estado" => $actual["ESTADO"], "numero" => $actual["NUMERO"], "boletos" => $actual["BOLETOS"], "bruto" => $actual["BRUTO"], "gastos" => $actual["GASTOS"], "monto" => $actual["MONTO"], "entregado" => $actual["ENTREGADO"], "comision" => $actual["COMISION"], "comentarios" => $actual["COMENTARIO"], "cerro" => $actual["CERRO"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
            return Array("mensaje" => "error de query");
        }
        return Array("mensaje" => "error de acceso");
    }

    /**
     * Funcion para pasarle a cada uno de los usuarios los reportes a los que tiene acceso
     * 
     * @param String $token El token de acceso
     * @return Array Un arreglo con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function get_reportes($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT REPORTES_NOMBRE, REPORTES_ICON, REPORTES_RUTA, REPORTES_CONFIG FROM datos_reportes INNER JOIN rol_has_reportes ON HAS_REPORTES_REPORTE = REPORTES_ID WHERE HAS_REPORTES_ROL = (SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']})");
            if($consulta)
            {
                $arr = Array();
                foreach($consulta as $actual)
                {
                    array_push($arr, Array("reporte" => $actual["REPORTES_NOMBRE"], "icon" => $actual["REPORTES_ICON"], "ruta" => $actual["REPORTES_RUTA"], "config" => $actual["REPORTES_CONFIG"]));
                }
                return Array("mensaje" => "ok", "data" => $arr);
            }
        }
        return Array("mensaje" => "error");
    }
?>