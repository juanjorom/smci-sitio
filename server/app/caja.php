<?php

    /**
     * Funcion para agregar boleteras
     * @param Object $data Los datos de la peticion
     * @return Array Arreglo con la información solicitada
     * @author Juanjo Romero
     */
    function add_boletera($data)
    {
        if(!validar_parametros_option($data, ["token", "boletoInicio","boletoFinal","totalBoletos", "status", "permisionario"], 6))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($data->token);
        $GLOBALS['LOG']['Usuarios']->write("El usuario ".$validar["id"] . " tiene derechos en Cajas");
        if($validar)
        {
            $codigo=$data->permisionario . '-' . $data->boletoInicio. '-' . date("Y-m-d");
            $GLOBALS["LOG"]["Usuarios"]->write("El usuario ".$validar["id"] . " esta ingresando estos boletos: Codigo: ".$codigo);
            if($GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_boleteras (BOLETERAS_ANADIDO, BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL, BOLETERAS_ESTADO, BOLETERAS_PERMISIONARIO, BOLETERAS_BOLETOS) VALUES ({$validar['id']},'{$codigo}','{$data->boletoInicio}', '{$data->boletoFinal}', '{$data->totalBoletos}', '{$data->status}', (SELECT PERMISIONARIO_USUARIO FROM usuario_permisionario WHERE PERMISIONARIO_CLAVE ='{$data->permisionario}'), '{}')"))
            {
                $boletera = $GLOBALS["DB"]->ejecutar_consulta("SELECT BOLETERAS_CODIGO, BOLETERAS_ESTADO, BOLETERAS_BOL_INI FROM datos_boleteras WHERE BOLETERAS_CODIGO = '{$codigo}' ");
                return Array("mensaje"=> "ok", "data" => Array("codigo" => $boletera["BOLETERAS_CODIGO"], "boletoInicial" => $boletera["BOLETERAS_BOL_INI"], "monto" => $boletera["BOLETERAS_MONTO"], "estado" => $boletera["BOLETERAS_ESTADO"]));
            }
            return Array("mensaje" => "error al ingresar");
        }
        return Array("mensaje" => "error interno");
    }

    /**
     * Funcion para pedir todas las boleteras
     * @param String $token El token para validar que tiene acceso
     * @return Array Un Array con los datos solicitados
     * @author Juanjo Romero
     */
    function get_all_boleteras($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        if(validar_token($token))
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL, BOLETERAS_ESTADO, USUARIOS_NOMBRE FROM datos_boleteras INNER JOIN usuario_usuarios ON USUARIOS_ID=BOLETERAS_PERMISIONARIO WHERE BOLETERAS_ESTADO!='VENDIDA' AND BOLETERAS_ELIMINADA=0");
            if($consulta)
            {
                $arreglo = Array();
                foreach($consulta as $actual)
                {
                    array_push($arreglo, Array("codigo" => $actual["BOLETERAS_CODIGO"], "boletoInicial" => $actual["BOLETERAS_BOL_INI"],"boletoFinal" => $actual["BOLETERAS_BOL_FIN"], "totalBoletos" => $actual["BOLETERAS_TOTAL_BOL"],  "estado" => $actual["BOLETERAS_ESTADO"], "monto" => $actual["BOLETERAS_MONTO"], "estado" => $actual["BOLETERAS_ESTADO"], "permisionario" => $actual['USUARIOS_NOMBRE']));
                }
                return Array("mensaje" => "ok", "data" => $arreglo);
            }
            return Array("mensaje" => "error");
        }
        return Array("mensaje" => "error");
    }


    /**
     * Funcion para obtener las claves y nombre de los permisionarios
     * @param String $token El token que valida al usuario
     * @return Array Arreglo con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function get_permisionarios($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        if(validar_token($token))
        {
            $consulta= $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT USUARIOS_NOMBRE, PERMISIONARIO_CLAVE FROM usuario_usuarios INNER JOIN usuario_permisionario ON PERMISIONARIO_USUARIO=USUARIOS_ID WHERE USUARIOS_TIPO=4");
            if($consulta)
            {
                $arreglo = Array();
                foreach($consulta as $actual)
                {
                    array_push($arreglo, Array("nombre" => $actual['USUARIOS_NOMBRE'], "clave" => $actual['PERMISIONARIO_CLAVE']));
                }
                return Array("mensaje" => "ok", "data" => $arreglo);
            }
            return Array("mensaje" => "SIN DATOS");
        }
        return Array("mensaje" => "error");
    }


    /**
     * Funcion para eliminar una boletera (deshabilitarla)
     * @param String $token El token del usuario para validar que es el
     * @param String $boletera El codigo de la boletera
     * @return Array Un array con el resultado de la operacion
     * 
     * @author Juanjo Romero
     */
    function delete_boletera($token, $boletera)
    {
        if(!validar_parametros_get([$token,$boletera], 2))
        {
            return Array("mensaje" => "error");
        }
        $validacion= validar_token($token);
        if($validacion)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_ELIMINADA = {$validacion['id']} WHERE BOLETERAS_CODIGO='{$boletera}'");
            if($consulta)
            {
                return Array("mensaje" => "ok");
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para abrir una vuelta
     * @param Object $data El objeto con todos los datos de la peticion
     * @return Array El arreglo con la informacion solicitada
     * 
     * @author Juanjo Romero
     */
    function abrir_vuelta($data)
    {
        if(!validar_parametros_option($data,['token', 'boleteras', 'chofer','ruta','unidad','fechaHora'], 6))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($data->token);
        if($validar)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_vueltas (VUELTAS_FECHA_HORA_INICIO, VUELTAS_CAJERA, VUELTAS_UNIDAD, VUELTAS_RUTA, VUELTAS_CHOFER, VUELTAS_ESTADO) VALUES('{$data->fechaHora}', {$validar['id']},(SELECT UNIDADES_ID FROM datos_unidades WHERE UNIDADES_CODIGO='{$data->unidad}'),(SELECT RUTAS_ID FROM datos_rutas WHERE RUTAS_CODIGO='{$data->ruta}'),(SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME='{$data->chofer}'), 'ABIERTA')");
            if($consulta)
            {
                $idVuelta = $GLOBALS["DB"]->id();
                if($idVuelta){
                    $asignadas = 0;
                    foreach($data->boleteras as $actual)
                    {
                        $asignarBoleteras = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_ESTADO='ASIGNADA', BOLETERAS_VUELTA={$idVuelta} WHERE BOLETERAS_CODIGO='{$actual}'");
                        if($asignarBoleteras)
                        {
                            $asignadas++;
                        }
                    }
                    if($asignadas== count($data->boleteras)){
                        return Array("mensaje" => "ok");
                    }
                }
            }
            return Array("mensaje" => "Error interno");
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para obtener todas las vueltas abiertas
     * @param String $token El token para validar los accesos
     * @return Array Un array con toda la información solicitada
     * 
     * @author Juanjo Romero
     */
    function get_all_vueltas_abiertas($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);

        if($validar)
        {
            $valores = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_ID, VUELTAS_FECHA_HORA_INICIO AS FECHA_HORA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID=VUELTAS_UNIDAD) AS UNIDAD, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID=VUELTAS_RUTA) AS RUTA , (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID=VUELTAS_CHOFER) AS CHOFER, VUELTAS_NUMERO AS NUMERO, VUELTAS_COMENTARIOS FROM datos_vueltas WHERE VUELTAS_ESTADO='ABIERTA'");
            if($valores)
            {
                $arreglo = Array();
                foreach($valores as $actual)
                {
                    $boleteras = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL FROM datos_boleteras WHERE BOLETERAS_VUELTA={$actual['VUELTAS_ID']} AND BOLETERAS_ELIMINADA=0");
                    if($boleteras)
                    {
                        $arr= Array();
                        foreach($boleteras as $esta)
                        {
                            array_push($arr, Array("codigo" => $esta["BOLETERAS_CODIGO"], "boletoInicial" => $esta["BOLETERAS_BOL_INI"], "boletoFinal" => $esta["BOLETERAS_BOL_FIN"], "totalBoletos" => $esta["BOLETERAS_TOTAL_BOL"]));
                        }
                    }
                    array_push($arreglo, Array("id" => $actual["VUELTAS_ID"], "fechaHora" => $actual["FECHA_HORA"], "unidad" => $actual["UNIDAD"], "ruta" => $actual["RUTA"], "chofer" => $actual["CHOFER"], "numero" => $actual["NUMERO"] , "comentarios" => $actual["VUELTAS_COMENTARIOS"], "boleteras" => $arr));
                }
                return Array("mensaje" => "ok", "data" => $arreglo);
            }
            return Array("mensaje" => "Sin datos");
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para cerrar una vuelta
     * @param Object $data Los datos de la peticion
     * @return Array Un array con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function close_lap($data)
    {
        if(!validar_parametros_option($data,['token', 'vuelta', 'boleteras', 'monto'], 4))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($data->token);
        if($validar){
            foreach($data->boleteras as $actual){
                $exito = 0;
                $boletos = json_encode($actual->boletos);
                $GLOBALS["LOG"]["Usuarios"]->write("El usuario ".$validar["id"] . " esta ingresando estos boletos: Codigo: ".$boletos);
                $actualizar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_BOL_FIN= '{$actual->boletoFinal}', BOLETERAS_TOTAL_BOL={$actual->totalBoletos}, BOLETERAS_MONTO={$actual->monto}, BOLETERAS_ESTADO='COBRADA', BOLETERAS_BOLETOS='{$boletos}' WHERE BOLETERAS_CODIGO='{$actual->codigo}'");
                if($actualizar)
                {
                    $exito++;
                }
            }
            if($exito==count($data->boleteras))
            {
                $cerrar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_vueltas SET VUELTAS_ESTADO='CERRADA', VUELTAS_MONTO={$data->monto}, VUELTAS_CERRADA={$validar['id']} WHERE VUELTAS_ID={$data->vuelta}");
                if($cerrar)
                {
                    return Array("mensaje" => "ok");
                }
            }
            return Array("mensaje" => "Error interno");
        }
        return Array("mensaje" => "error");
    }
?>