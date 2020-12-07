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
            $codigo=$data->permisionario . $data->boletoInicio;
            $GLOBALS["LOG"]["Usuarios"]->write("El usuario ".$validar["id"] . " esta ingresando estos boletos: Codigo: ".$codigo);
            if($GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_boleteras (BOLETERAS_ANADIDO, BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL, BOLETERAS_ESTADO, BOLETERAS_PERMISIONARIO, BOLETERAS_BOLETOS) VALUES ({$validar['id']},'{$codigo}','{$data->boletoInicio}', '{$data->boletoFinal}', '{$data->totalBoletos}', '{$data->status}',{$data->permisionario}, '{}')"))
            {
                $id = $GLOBALS["DB"]->id();
                $nuevoCodigo= $codigo."-" .$id;
                $actualizar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_CODIGO = '{$nuevoCodigo}' WHERE BOLETERAS_ID = {$id}");
                if($actualizar)
                {
                    $boletera = $GLOBALS["DB"]->ejecutar_consulta("SELECT BOLETERAS_CODIGO, BOLETERAS_ESTADO, BOLETERAS_BOL_INI, (SELECT USUARIOS_NOMBRE  FROM usuario_usuarios WHERE USUARIOS_ID = BOLETERAS_PERMISIONARIO) AS PERMISIONARIO FROM datos_boleteras WHERE BOLETERAS_ID = {$id}");
                    return Array("mensaje"=> "ok", "data" => Array("codigo" => $boletera["BOLETERAS_CODIGO"], "boletoInicial" => $boletera["BOLETERAS_BOL_INI"], "monto" => $boletera["BOLETERAS_MONTO"], "estado" => $boletera["BOLETERAS_ESTADO"], "permisionario" => $boletera["PERMISIONARIO"]));
                }
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
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL, BOLETERAS_ESTADO, USUARIOS_NOMBRE FROM datos_boleteras INNER JOIN usuario_usuarios ON USUARIOS_ID=BOLETERAS_PERMISIONARIO WHERE BOLETERAS_ESTADO!='VENDIDA' AND BOLETERAS_ELIMINADA=0 ORDER BY BOLETERAS_CODIGO");
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
     * Funcion para retornar las boleteras 
     */
    function get_boleteras_asignables($token, $unidad)
    {
        if(!validar_parametros_get([$token, $unidad],2))
        {
            return Array("mensaje" => "error");
        }
        if(validar_token($token))
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("SELECT UNIDADES_PERMISIONARIO FROM datos_unidades WHERE UNIDADES_CODIGO = '{$unidad}'");
            if($consulta)
            {
                $boleteras = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT BOLETERAS_CODIGO, BOLETERAS_BOL_INI, BOLETERAS_BOL_FIN, BOLETERAS_TOTAL_BOL, BOLETERAS_ESTADO, USUARIOS_NOMBRE FROM datos_boleteras INNER JOIN usuario_usuarios ON USUARIOS_ID=BOLETERAS_PERMISIONARIO WHERE BOLETERAS_PERMISIONARIO = {$consulta['UNIDADES_PERMISIONARIO']} AND BOLETERAS_ESTADO='NO ASIGNADA' AND BOLETERAS_ELIMINADA=0 ORDER BY BOLETERAS_CODIGO");
                if($boleteras)
                {
                    $regreso = Array();
                    foreach($boleteras as $actual)
                    {
                        array_push($regreso, Array("codigo" => $actual["BOLETERAS_CODIGO"], "boletoInicial" => $actual["BOLETERAS_BOL_INI"],"boletoFinal" => $actual["BOLETERAS_BOL_FIN"], "totalBoletos" => $actual["BOLETERAS_TOTAL_BOL"],  "estado" => $actual["BOLETERAS_ESTADO"], "monto" => $actual["BOLETERAS_MONTO"], "estado" => $actual["BOLETERAS_ESTADO"], "permisionario" => $actual['USUARIOS_NOMBRE']));
                    }
                    return Array("mensaje" => "ok", "data" => $regreso);
                }
                return Array("mensaje" => "Error interno");
            }
            return Array("mensaje" => "Error en la unidad");
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
            $consulta= $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT USUARIOS_ID, USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_TIPO = 4 ORDER BY USUARIOS_NOMBRE");
            if($consulta)
            {
                $arreglo = Array();
                foreach($consulta as $actual)
                {
                    array_push($arreglo, Array("nombre" => $actual['USUARIOS_NOMBRE'], "clave" => $actual['USUARIOS_ID']));
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
            $turno = $GLOBALS["DB"]->ejecutar_consulta("SELECT TURNOS_ID, TURNOS_VUELTAS FROM datos_turnos WHERE TURNOS_CHOFER = (SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME='{$data->chofer}') AND TURNOS_ESTADO = 'ABIERTO'");
            if($turno)
            {
                $vueltaNumero=$turno["TURNOS_VUELTAS"]+1;
                $turnoId = $turno["TURNOS_ID"];
            }
            else
            {
                $turno = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_turnos (TURNOS_UNIDAD, TURNOS_CHOFER, TURNOS_RUTA, TURNOS_VUELTAS, TURNOS_INICIO, TURNOS_ESTADO) VALUES ((SELECT UNIDADES_ID FROM datos_unidades WHERE UNIDADES_CODIGO='{$data->unidad}'), (SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME = '{$data->chofer}'), (SELECT RUTAS_ID FROM datos_rutas WHERE RUTAS_CODIGO='{$data->ruta}'), 1, '{$data->fechaHora}', 'ABIERTO')");
                if($turno)
                {
                    $turnoId = $GLOBALS["DB"]->id();
                    $vueltaNumero = 1;
                }
                else
                {
                    return Array("mensaje" => "Error al abrir turno");
                }
            }
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_vueltas (VUELTAS_FECHA_HORA_INICIO, VUELTAS_CAJERA, VUELTAS_UNIDAD, VUELTAS_RUTA, VUELTAS_CHOFER, VUELTAS_TURNO, VUELTAS_ESTADO, VUELTAS_NUMERO) VALUES ('{$data->fechaHora}', {$validar['id']},(SELECT UNIDADES_ID FROM datos_unidades WHERE UNIDADES_CODIGO='{$data->unidad}'),(SELECT RUTAS_ID FROM datos_rutas WHERE RUTAS_CODIGO='{$data->ruta}'),(SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME='{$data->chofer}'),{$turnoId},'ABIERTA', {$vueltaNumero})");
            if($consulta)
            {
                $idVuelta = $GLOBALS["DB"]->id();
                if($idVuelta){
                    $asignadas = 0;
                    $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_turnos SET TURNOS_VUELTAS = {$vueltaNumero} WHERE TURNOS_ID = {$turnoId}");
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
            $valores = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_ID, VUELTAS_FECHA_HORA_INICIO AS FECHA_HORA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID=VUELTAS_UNIDAD) AS UNIDAD, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID=VUELTAS_RUTA) AS RUTA , (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID=VUELTAS_CHOFER) AS CHOFER, VUELTAS_TURNO AS TURNO, VUELTAS_NUMERO AS NUMERO, VUELTAS_COMENTARIOS FROM datos_vueltas WHERE VUELTAS_ESTADO='ABIERTA' ORDER BY FECHA_HORA");
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
                    array_push($arreglo, Array("id" => $actual["VUELTAS_ID"], "fechaHora" => $actual["FECHA_HORA"], "unidad" => $actual["UNIDAD"], "ruta" => $actual["RUTA"], "chofer" => $actual["CHOFER"],"turno" => $actual["TURNO"],  "numero" => $actual["NUMERO"] , "comentarios" => $actual["VUELTAS_COMENTARIOS"], "boleteras" => $arr));
                }
                return Array("mensaje" => "ok", "data" => $arreglo);
            }
            return Array("mensaje" => "Sin datos");
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para obtener las vueltas abiertas de una unidad
     * @param String $token El token del usuario
     * @param String $unidad La unidad de la que se quieren obtener las vueltas abiertas
     * @return Array El array con la información solicitada
     * 
     * @author Juanjo Romero
     */
    function get_vuelta_by_unidad($token, $unidad)
    {
        if(!validar_parametros_get([$token, $unidad], 2))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);

        if($validar)
        {
            $valores = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_ID, VUELTAS_FECHA_HORA_INICIO AS FECHA_HORA, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID=VUELTAS_UNIDAD) AS UNIDAD, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID=VUELTAS_RUTA) AS RUTA , (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID=VUELTAS_CHOFER) AS CHOFER, VUELTAS_NUMERO AS NUMERO, VUELTAS_COMENTARIOS FROM datos_vueltas WHERE VUELTAS_ESTADO='ABIERTA' AND VUELTAS_UNIDAD = (SELECT UNIDADES_ID FROM datos_unidades WHERE UNIDADES_CODIGO = '{$unidad}') ORDER BY FECHA_HORA");
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
        if(!validar_parametros_option($data,['token', 'vuelta', 'boleteras', 'bruto', 'gastosTotal', 'monto', 'entregado', 'gastos', 'comentarios'], 9))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($data->token);
        if($validar){
            $exito = 0;
            foreach($data->boleteras as $actual)
            {
                $boletos = json_encode($actual->boletos);
                $GLOBALS["LOG"]["Usuarios"]->write("El usuario ".$validar["id"] . " esta ingresando estos boletos: Codigo: ".$boletos);
                $actualizar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_BOL_FIN= '{$actual->boletoFinal}', BOLETERAS_TOTAL_BOL={$actual->totalBoletos}, BOLETERAS_BOL_SOB={$actual->sobrantes}, BOLETERAS_MONTO={$actual->monto}, BOLETERAS_ESTADO='COBRADA', BOLETERAS_BOLETOS='{$boletos}' WHERE BOLETERAS_CODIGO='{$actual->codigo}'");
                if($actualizar)
                {
                    $exito++;
                }
            }
            if($exito==count($data->boleteras))
            {   
                $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_boleteras SET BOLETERAS_ESTADO='NO ASIGNADA', BOLETERAS_VUELTA = 0 WHERE BOLETERAS_VUELTA = {$data->vuelta} AND BOLETERAS_ESTADO='ASIGNADA'");
                
                if(count($data->gastos))
                {
                    $totalGastos=0;
                    foreach($data->gastos as $este)
                    {
                        $insertGasto = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_gastos_vueltas (GASTOS_VUELTA, GASTOS_MONTO, GASTOS_DESCRIPCION) VALUES ({$data->vuelta}, {$este->monto}, '{$este->descripcion}')");
                        if($insertGasto)
                        {
                            $totalGastos++;
                        }
                    }
                    if($totalGastos!=count($data->gastos))
                    {
                        return Array("mensaje" => "error agregando gastos");
                    }
                }
                $fechaCierre = date("Y-m-d H:i:s");
                $comision = round($data->bruto*0.18);
                $cerrar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_vueltas SET VUELTAS_FECHA_HORA_FIN = '{$fechaCierre}', VUELTAS_ESTADO='CERRADA', VUELTAS_BRUTO = {$data->bruto}, VUELTAS_GASTOS = {$data->gastosTotal}, VUELTAS_MONTO={$data->monto}, VUELTAS_ENTREGADO = {$data->entregado}, VUELTAS_COMISION = {$comision}, VUELTAS_COMENTARIOS = '{$data->comentarios}', VUELTAS_CERRADA={$validar['id']} WHERE VUELTAS_ID={$data->vuelta}");
                if($cerrar)
                {
                    return Array("mensaje" => "ok");
                }
                
            }
            return Array("mensaje" => "Error interno");
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para obtener todos los turnos abiertos
     * @param String $token El token del usuario
     * @return Array Un arreglo con los datos pedidos
     * 
     * @author Juanjo Romero
     */
    function get_all_turnos($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);

        if($validar)
        {
            $valores = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT TURNOS_ID, (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = TURNOS_UNIDAD) AS UNIDAD, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = TURNOS_CHOFER) AS CHOFER, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = TURNOS_RUTA) AS RUTA , TURNOS_VUELTAS, TURNOS_INICIO FROM datos_turnos WHERE TURNOS_ESTADO = 'ABIERTO' ORDER BY TURNOS_INICIO");
            if($valores)
            {
                $arra = Array();
                foreach($valores as $esta)
                {
                    array_push($arra, Array("id" => $esta['TURNOS_ID'], "unidad" => $esta['UNIDAD'], "chofer" => $esta['CHOFER'], "ruta" => $esta['RUTA'], "vueltas" => $esta['TURNOS_VUELTAS'], "inicio" => $esta['TURNOS_INICIO']));
                }
                return Array("mensaje" => "ok", "data" => $arra);
            }
            return Array("mensaje" => "Sin datos");
        }
        return Array("mensaje" => "error");
    }


    /**
     * Funcion para obtener un turno por su id listo para ser liquidado
     * @param String $token El token de acceso del usuario
     * @param String $id El id del turno a solicitar
     * @return Array Un array con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function get_turno_by_id($token, $id)
    {
        if(!validar_parametros_get([$token, $id],2))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($token);
        if($validar)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT VUELTAS_FECHA_HORA_INICIO AS INICIO, VUELTAS_FECHA_HORA_FIN AS FIN, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CAJERA) AS CAJERA_ABRIO, (SELECT RUTAS_RUTA FROM datos_rutas WHERE RUTAS_ID = VUELTAS_RUTA) AS RUTA, VUELTAS_NUMERO, VUELTAS_BRUTO, VUELTAS_GASTOS,  VUELTAS_MONTO, VUELTAS_ENTREGADO,(VUELTAS_MONTO - VUELTAS_ENTREGADO) AS DIFERENCIA, VUELTAS_COMISION, VUELTAS_COMENTARIOS, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = VUELTAS_CERRADA) AS CAJERA_CERRO, (SELECT SUM(BOLETERAS_TOTAL_BOL) FROM datos_boleteras WHERE BOLETERAS_VUELTA=VUELTAS_ID) AS BOLETOS FROM datos_vueltas WHERE VUELTAS_TURNO = {$id}");
            if($consulta)
            {
                $arra = Array();
                foreach($consulta as $actual)
                {
                    array_push($arra, Array("inicio" => $actual["INICIO"], "fin" => $actual["FIN"], "abrio" => $actual["CAJERA_ABRIO"], "ruta" => $actual["RUTA"], "vuelta" => $actual["VUELTAS_NUMERO"], "bruto" => $actual["VUELTAS_BRUTO"], "gastos" => $actual["VUELTAS_GASTOS"],  "monto" => $actual["VUELTAS_MONTO"], "entregado" => $actual["VUELTAS_ENTREGADO"], "diferencia" => $actual["DIFERENCIA"], "comision" => $actual["VUELTAS_COMISION"], "comentarios" => $actual["VUELTAS_COMENTARIOS"], "cerro" => $actual["CAJERA_CERRO"], "boletos" => $actual["BOLETOS"]));
                }
                $turno = $GLOBALS["DB"]->ejecutar_consulta("SELECT (SELECT UNIDADES_NOMBRE FROM datos_unidades WHERE UNIDADES_ID = TURNOS_UNIDAD) AS UNIDAD, (SELECT USUARIOS_NOMBRE FROM usuario_usuarios WHERE USUARIOS_ID = TURNOS_CHOFER) AS CHOFER, TURNOS_VUELTAS, TURNOS_INICIO FROM datos_turnos WHERE TURNOS_ID={$id}");
                if($turno)
                {                    
                    return Array("mensaje" => "ok", "data" => Array("unidad" => $turno["UNIDAD"], "chofer" => $turno["CHOFER"], "totalVueltas" => $turno["TURNOS_VUELTAS"], "inicio" => $turno["TURNOS_INICIO"], "vueltas" => $arra));
                }
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para pagar el turno de un chofer
     * @param Object $data El objeto con todas las propiedades
     * @return Array Arreglo con todos los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function pagar_turno($data)
    {
        if(!    validar_parametros_option($data, ['token', 'id', 'venta', 'recaudado', 'comision', 'boletos', 'totalGastos', 'gastos'],8))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($data->token);
        if($validacion)
        {
            $utilidad = $data->venta  - $data->totalGastos;
            $actualizarVueltas = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_vueltas SET VUELTAS_ESTADO = 'PAGADO' WHERE VUELTAS_TURNO = {$data->id}");
            if($actualizarVueltas)
            {
                $fechaCierre = date("Y-m-d H:i:s");
                $liquidacion = $GLOBALS["DB"]->ejecutar_consulta("UPDATE datos_turnos SET TURNOS_FIN='{$fechaCierre}', TURNOS_BOLETOS = {$data->boletos}, TURNOS_VENTA = {$data->venta}, TURNOS_GASTOS = {$data->totalGastos}, TURNOS_RECAUDADO = {$data->recaudado}, TURNOS_UTILIDAD = {$utilidad}, TURNOS_CAJERA = {$validacion['id']}, TURNOS_ESTADO = 'PAGADO' WHERE TURNOS_ID = {$data->id}");
                if($liquidacion)
                {
                    $confirmar= 0;
                    foreach($data->gastos as $actual)
                    {
                        $gas = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_gastos_turno (GASTOS_TUR_TURNO, GASTOS_TUR_MONTO, GASTOS_TUR_DESCRIPCION) VALUES ({$data->id}, {$actual->monto}, '{$actual->descripcion}')");
                        if($gas)
                        {
                            $confirmar++;
                        }
                    }
                    if($confirmar==count($data->gastos))
                    {
                        $lis = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_liquidaciones (LIQUIDACIONES_TURNO, LIQUIDACIONES_MONTO, LIQUIDACIONES_GASTOS, LIQUIDACIONES_UTILIDADES, LIQUIDACIONES_RECAUDADO, LIQUIDACIONES_UNIDAD, LIQUIDACIONES_CHOFER, LIQUIDACIONES_ESTADO) VALUES ({$data->id}, {$data->venta}, {$data->totalGastos}, {$utilidad}, {$data->recaudado}, (SELECT TURNOS_UNIDAD FROM datos_turnos WHERE TURNOS_ID = {$data->id}), (SELECT TURNOS_CHOFER FROM datos_turnos WHERE TURNOS_ID = {$data->id}), 'RECIBIDO')");
                        if($lis)
                        {
                            $com = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO usuario_comisiones (COMISIONES_CHOFER, COMISIONES_TURNO, COMISIONES_MONTO) VALUES ((SELECT TURNOS_CHOFER FROM datos_turnos WHERE TURNOS_ID={$data->id}),{$data->id},{$data->comision})");
                            if($com)
                            {
                                return Array("mensaje" => "ok");
                            }
                            return Array("mensaje" => "Error interno 5");
                        }
                        return Array("mensaje" => "Error interno 4");
                    }
                    return Array("mensaje" => "Error interno 3");
                }
                return Array("mensaje" => "Error interno 2");
            }
            return Array("mensaje" => "Error interno");
        }
        return Array("mensaje" => "error");
    }

    function obtener_sumas($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }

        $validacion = validar_token($token);
        if($validacion)
        {
            
        }
    }
?>