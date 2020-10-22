<?php

    /**
     * Funcion para validar los datos de incio de sesion
     * @param Object $datos Objeto con los datos de la peticion
     * @return Array Arreglo con la informacion solicitada
     * @author Juanjo Romero
     */
    function validar_login($datos)
    {
        if(!isset($datos->mail) && !isset($datos->password))
        {
            return Array("mensaje" => "Usuario y/o Contraseña no especificado");
        }
        
        $st_mail = $datos->nickname;
        $st_password = $datos->password;
        $ar_data = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_ID, USUARIOS_PASSWORD FROM usuario_usuarios WHERE USUARIOS_NICKNAME= '{$st_mail}' AND USUARIOS_ACTIVO=1");

        if($ar_data)
        {
            if($ar_data['USUARIOS_PASSWORD']==$st_password){
                $token = generar_token($ar_data["USUARIOS_ID"]);
                $fecha_hora = date("Y-m-d H:i:s");
                if($GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET USUARIOS_TOKEN = '{$token}', USUARIOS_ULT_CON = '{$fecha_hora}' WHERE USUARIOS_ID={$ar_data['USUARIOS_ID']}"))
                {
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

        /**
         * Funcion para Cerrar la sesion
         * @param Object $datos Objeto con los datos de la peticion
         * @return Array Arreglo con los datos solicitados
         * @author Juanjo Romero
         */
        function cerrar_sesion($datos)
        {

        }
    }
?>