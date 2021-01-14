<?php
    /**
     * Funcion para registrar un nuevo horario
     * @param Object $datos Los datos del nuevo horario
     * @return Array Un arreglo con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function registra_horario($datos)
    {
        if(!validar_parametros_option($datos, ['token', 'nombre', 'inicio', 'fin','ruta','unidades', 'corridas'], 7))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($datos->token);
        if($validacion)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO organizar_horarios (HORARIOS_INICIO, HORARIOS_FIN, HORARIOS_NOMBRE, HORARIOS_RUTA, HORARIOS_UNIDADES) VALUES ('{$datos->inicio}', '{$datos->fin}', '{$datos->nombre}', {$datos->ruta}, {$datos->unidades})");
            if($consulta)
            {
                $id = $GLOBALS["DB"]->id();
                $hecho = 0;
                foreach($datos->corridas as $actual)
                {
                    $subr = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO organizar_corridas SET CORRIDAS_INICIO_HORA = '{$actual->horaInicio}', CORRIDAS_INICIO_PARADA = '{$actual->paradaInicio}', CORRIDAS_FIN_HORA = '{$actual->horaFin}', CORRIDAS_FIN_PARADA = '{$actual->paradaFin}', CORIDAS_POSICION = {$actual->posicion}, CORRIDAS_DURACION = {$actual->duracion}, CORRIDAS_HORARIO = {$id}, CORRIDAS_ORIENTACION = {$actual->orientacion}");
                    if($subr)
                    {
                        $hecho++;
                    }
                }
                if(count($datos->corridas) == $hecho)
                {
                    return Array("mensaje" => "ok");
                }
            }
        }
        return Array("mensaje" => "error interno");
    }
?>