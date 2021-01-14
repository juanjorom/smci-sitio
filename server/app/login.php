<?php

    /**
     * Funcion para validar los datos de incio de sesion
     * @param Object $datos Objeto con los datos de la peticion
     * @return Array Arreglo con la informacion solicitada
     * @author Juanjo Romero
     */
    function validar_login($datos)
    {
        if(validar_parametros_option($datos, ["mail", "password"], 2))
        {
            return Array("mensaje" => "Usuario y/o Contraseña no especificado");
        }
        
        $st_mail = $datos->nickname;
        $st_password = $datos->password;
        $ar_data = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_ID, USUARIOS_PASSWORD FROM usuario_usuarios WHERE USUARIOS_NICKNAME = '{$st_mail}' AND USUARIOS_ACTIVO=1");

        if($ar_data)
        {
            if(compare_passwords($st_password,$ar_data['USUARIOS_PASSWORD']))
            {
                $token = generar_token($ar_data["USUARIOS_ID"]);
                $fecha_hora = date("Y-m-d H:i:s");
                if($GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET USUARIOS_TOKEN = '{$token}', USUARIOS_ULT_CON = '{$fecha_hora}' WHERE USUARIOS_ID={$ar_data['USUARIOS_ID']}"))
                {
                    $GLOBALS["LOG"]["Sesion"]->write("Sesion del usuario ".$ar_data["USUARIOS_ID"]);
                    return Array("mensaje" => "ok", "token" => $token);
                }
                else
                {
                    return Array("mensaje" => "Error en la conexion");
                }
            }
            return Array("mensaje" => "Datos incorrectos");
        }
        return Array("mensaje" => "Usuario no existe", "usuario" => $st_mail );
    }
    
    /**
     * Funcion para Cerrar la sesion
     * @param Object $datos Objeto con los datos de la peticion
     * @return Array Arreglo con los datos solicitados
     * @author Juanjo Romero
     */
    function cerrar_sesion($datos)
    {
        if(!validar_parametros_option($datos, ["token"]))
        {
            return Array("mensaje" => "Usuario y/o Contraseña no especificado");
        }
        $validar = validar_token($datos->token);
        if($validar)
        {
            $close = $GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET USUARIOS_TOKEN = '' WHERE USUARIOS_ID={$validar['id']}");
            if($close)
            {
                $GLOBALS["LOG"]["Sesion"]->write("Sesion del usuario ".$validar["id"]);
                return Array("mensaje" => "ok");
            }
        }
        return Array("mensaje" => "error");
    }
    
    /**
     * Funcion para validar al usuario cada que haga un movimiento delicado
     * @param Object $datos El objeto con los datos de la peticion
     * @return Array El arreglo con los datos solicitados
     * 
     * @author Juanjo Romero
     */
    function verificar_password($datos)
    {
        if(!validar_parametros_option($datos,["token", "password"],2))
        {
            return Array("mensaje" => "error");
        }
        $validar = validar_token($datos->token);
        if($validar)
        {
            $consulta = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_PASSWORD FROM usuario_usuarios WHERE USUARIOS_ID={$validar['id']}");
            if($consulta)
            {
                if(compare_passwords($datos->password, $consulta["USUARIOS_PASSWORD"]))
                {
                    return Array("mensaje" => "ok");
                }
                return Array("mensaje" => "error");
            }
        }
        return Array("mensaje" => "error");
    }
?>