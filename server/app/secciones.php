<?php
    /**
     * Funcion que retorna los accesos de un usuario
     * @param String $token El token de usuario
     * @return Array Un array con todos los accesos del usuario
     * 
     * @author Juanjo Romero
     */
    function get_permisos_usuario($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }

        $permiso = validar_token($token);
        if($permiso)
        {
            $tipo = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$permiso['id']}");
            if($tipo)
            {
                $accesos = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT PERMISOS_MODULO, PERMISOS_DESCRIPCION, PERMISOS_RUTA, PERMISOS_VALOR FROM rol_has_permisos INNER JOIN usuario_permisos ON PERMISOS_ID = HAS_PERMISOS_PERMISOS_ID WHERE HAS_PERMISOS_ROL_ID = {$tipo['USUARIOS_TIPO']}");
                if($accesos)
                {
                    $arr = Array();
                    foreach($accesos as $actual)
                    {
                        array_push($arr, Array("modulo" => $actual["PERMISOS_MODULO"], "descripcion" => $actual["PERMISOS_DESCRIPCION"], "ruta" => $actual["PERMISOS_RUTA"], "valor" => $actual["PERMISOS_VALOR"]));
                    }
                    return Array("mensaje" => "ok", "data" => $arr);
                }
            }
        }
        return Array("mensaje" => "error interno");
    }

    /**
     * Funcion para Agregar un nuevo modulo
     * @param Object $datos Los datos del nuevo modulo
     * @return Array Un array con la respuesta del servidor
     * 
     * @author Juanjo Romero
     */
    function add_modulo($datos)
    {
        if(!validar_parametros_option($datos, ['token', 'modulo','ruta', 'roles'],4))
        {
            return Array("mensaje" => "error de autenticacion");
        }
        if(validar_token($datos->token))
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO usuario_permisos (PERMISOS_MODULO, PERMISOS_RUTA) VALUES ('{$datos->modulo}', '{$datos->ruta}')");
            if($consulta)
            {
                $exito= 0;
                $id = $GLOBALS["DB"]->id();
                foreach($datos->roles as $actual)
                {
                    $roles = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO rol_has_permisos VALUES ({$actual}, {$id})");
                    if($roles)
                    {
                        $exito++;
                    }
                }
                if($exito == count($datos->roles)){
                    return Array("mensaje" => "ok");
                }
            }
        }
        return Array("mensaje" => "error interno");
    }

    /*
    function validar_seccion($token, $seccion)
    {
        if(validar_parametros_get([$token, $seccion], 2))
        {
            return Array("mensaje" => "error");
        }
        
        $permiso = validar_token($token);
        if($permiso)
        {
            
        }
    }
    */
?>