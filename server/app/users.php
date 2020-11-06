<?php
    /**
     * Funcion para que un suario obtenga sus datos de inicio de sesion
     * Todos los usuarios tienen acceso, una vez que se hayan logeado
     * 
     * @param String $data Los datos obtenidos de la petición
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
    */
    function get_user($data)
    {
        if(!validar_parametros_get([$data]))
        {
            return Array("mensaje" => "error");
        }
        $valores= validar_token($data);
        if($valores)
        {
            $orden = $GLOBALS['DB']->ejecutar_consulta("SELECT USUARIOS_ID, USUARIOS_NOMBRE, USUARIOS_PASSWORD_HISTORIAL, ROL_DESCRIPCION FROM usuario_usuarios INNER JOIN usuario_rol ON ROL_ID=USUARIOS_TIPO WHERE USUARIOS_ID={$valores['id']} AND USUARIOS_ACTIVO=1");
            if($orden){
                return Array("mensaje" => "ok", "data" => Array( "id"=> $orden['USUARIOS_ID'], "nombre" => $orden['USUARIOS_NOMBRE'], "password" => $orden['USUARIOS_PASSWORD_HISTORIAL'] ,"rol" => $orden['ROL_DESCRIPCION']));
            }
            return Array("mensaje" => "error interno");
        }
        return Array("mensaje" => "error");
    }
    

    /**
     * 
     * Funcion para crear un usuario
     * Solo Administradores y Root tienen acceso
     * 
     * @param Object $data Los datos obtenidos de la petición
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */

    function create_user($data)
    {
        if(!validar_parametros_option($data, ["token", "nickname","rol", "nombre", "password"], 5))
        {
            return Array("mensaje" => "error");
        }
        $valores= validar_token($data->token);
        if($valores)
        {
            $permiso = $GLOBALS['DB']->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios  WHERE USUARIOS_ID={$valores['id']} AND USUARIOS_ACTIVO=1");
            if($permiso["USUARIOS_TIPO"]==1 || $permiso["USUARIOS_TIPO"]==2)
            {
                
                $validaNickname = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME = '{$data->nickname}'");
                if($validaNickname)
                {
                    return Array("mensaje" => "Nickname ya existe");
                }
                $rol = $GLOBALS["DB"]->ejecutar_consulta("SELECT ROL_ID FROM usuario_rol WHERE ROL_DESCRIPCION='$data->rol'");
                $bandera= false;
                if($permiso["USUARIOS_TIPO"]==1 && $rol['ROL_ID']==2)
                {
                    $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO usuario_usuarios(USUARIOS_CREADOR, USUARIOS_NICKNAME, USUARIOS_NOMBRE, USUARIOS_PASSWORD, USUARIOS_PASSWORD_HISTORIAL, USUARIOS_ACCESOS, USUARIOS_TIPO)  VALUES({$valores['id']},'$data->nickname', '$data->nombre', '$data->password', 0, '{}', '{$rol['ROL_ID']}')");
                    if($consulta)
                    {
                        $bandera=true;
                    }
                }
                else if($rol['ROL_ID']!=1)
                {
                    $consulta = $GLOBALS["DB"]->ejecutar_consulta("INSERT INTO usuario_usuarios(USUARIOS_CREADOR, USUARIOS_NICKNAME, USUARIOS_NOMBRE, USUARIOS_PASSWORD, USUARIOS_PASSWORD_HISTORIAL, USUARIOS_ACCESOS, USUARIOS_TIPO)  VALUES({$valores['id']},'$data->nickname', '$data->nombre', '$data->password', 0, '{}', '{$rol['ROL_ID']}')");
                    if($consulta)
                    {
                        $bandera=true;
                    }
                }
                if($bandera)
                {
                    $res = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_ID, USUARIOS_NICKNAME, USUARIOS_NOMBRE, USUARIOS_PASSWORD_HISTORIAL, ROL_DESCRIPCION FROM usuario_usuarios INNER JOIN usuario_rol ON ROL_ID=USUARIOS_TIPO WHERE USUARIOS_NICKNAME='{$data->nickname}'");
                    return Array("mensaje" => "ok", "data" => Array("id" => $res["USUARIOS_ID"], "nickname" => $res["USUARIOS_NICKNAME"], "pass" => $res["USUARIOS_PASSWORD_HISTORIAL"], "rol" => $res["ROL_DESCRIPCION"]));
                }
                return Array("mensaje" => "operacion no valida");
            }
            return Array("mensaje" => "Acceso denegado");
            
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para validar el nickname
     * Solo tienen acceso el ADMINISTRADOR y el ROOT
     * 
     * @param Object $data Los datos obtenidos de la petición
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */
    function validar_nickname($data)
    {
        if(!validar_parametros_option($data, ["token", "nickname"], 2))
        {
            return Array("mensaje" => "error");
        }   
        $validacion = validar_token($data->token);
        if($validacion)
        {
            $permiso = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']} AND USUARIOS_ACTIVO=1");
            if($permiso["USUARIOS_TIPO"]<=2)
            {
                $consulta = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_ID FROM usuario_usuarios WHERE USUARIOS_NICKNAME = '{$data->nickname}'");
                if(!$consulta)
                {
                    return Array("mensaje" => "ok");
                }
                return Array("mensaje" => "No disponible");
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para obtener todos los usuarios
     * Solo tienen acceso el Administrador y Root
     * 
     * @param String $token El token de acceso
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */
    function get_all_users($token)
    {
        if(!validar_parametros_get([$token]))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $permiso = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']} AND USUARIOS_ACTIVO = 1");
            if($permiso["USUARIOS_TIPO"]==1 || $permiso["USUARIOS_TIPO"]==2)
            {
                $tipo = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT USUARIOS_ID AS id, USUARIOS_NICKNAME AS nickname, USUARIOS_NOMBRE AS nombre, USUARIOS_PASSWORD_HISTORIAL AS pass, ROL_DESCRIPCION AS tipo FROM usuario_usuarios INNER JOIN usuario_rol ON ROL_ID=USUARIOS_TIPO WHERE USUARIOS_TIPO > 1 AND USUARIOS_ACTIVO = 1");
                $arreglo = Array();
                foreach($tipo as $presente)
                {
                    array_push($arreglo,Array("id" => $presente["id"], "nickname" => $presente["nickname"], "nombre" => $presente["nombre"], "rol" => $presente["tipo"]));
                }
                return Array("mensaje" => "ok", "data"  =>$arreglo);
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para actualizar la contraseña desde el mismo usuario
     * 
     * @param Object $data Los datos de la peticion
     * @return Array Un arreglo con la información solicitada
     * @author Juanjo Romero
     */
    function update_password($data)
    {
        if(!validar_parametros_option($data, ["token", "password"],2))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($data->token);
        if($validacion)
        {
            $actualizar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET USUARIOS_PASSWORD = '{$data->password}', USUARIOS_PASSWORD_HISTORIAL = 1 WHERE USUARIOS_ID = {$validacion['id']}");
            if($actualizar)
            {
                return Array("mensaje" => "ok");
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para modificar un usuario
     * 
     * @param Object $data Los datos recibidos de la petición
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */
    function modify_user($data)
    {
        if(!validar_parametros_option($data, ["token", "id"],2))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($data->token);
        if($validacion)
        {
            $permiso = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']} AND USUARIOS_ACTIVO=1");
            if($permiso["USUARIOS_TIPO"]==1 || $permiso["USUARIOS_TIPO"]==2)
            {
                $actualizar = $GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET ");
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para eliminar un usuario
     * Solo tienen acceso los Administrdores y Root
     * 
     * @param String $token Los datos recibidos de la peticion
     * @param Integer $id Los datos recibidos de la peticion
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */
    function delete_user($token, $id)
    {
        if(!validar_parametros_get([$token, $id],2))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $permiso = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']} AND USUARIOS_ACTIVO=1");
            if($permiso["USUARIOS_TIPO"]==1 || $permiso["USUARIOS_TIPO"]==2)
            {
                $delete = $GLOBALS["DB"]->ejecutar_consulta("UPDATE usuario_usuarios SET USUARIOS_ACTIVO = 0 WHERE USUARIOS_ID = {$id}");
                if($delete)
                {
                    return Array("mensaje" => "ok");
                }
                return Array("mensaje" => "Usuario no existe");
            }
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para obtener los usuarios filtrados por tipo
     * Acceso solo de Administradores y Root
     * 
     * @param String $token El token de acceso
     * @param String $type Tipo de usuarios a filtrar
     * @return Array Un array con los datos solicitados
     * @author Juanjo Romero
     */
    function get_user_by_type($token, $type)
    {
        if(!validar_parametros_get([$token, $type], 2))
        {
            return Array("mensaje" => "error");
        }
        $validacion = validar_token($token);
        if($validacion)
        {
            $permiso = $GLOBALS["DB"]->ejecutar_consulta("SELECT USUARIOS_TIPO FROM usuario_usuarios WHERE USUARIOS_ID = {$validacion['id']} AND USUARIOS_ACTIVO=1");
            if($permiso["USUARIOS_TIPO"]==1 || $permiso["USUARIOS_TIPO"]==2)
            {
                if($type!="ROOT" && $type!="root")
                {
                    $tipo = $GLOBALS["DB"]->ejecutar_consulta_multiple("SELECT USUARIOS_ID AS id, USUARIOS_NICKNAME AS nickname, USUARIOS_NOMBRE AS nombre, USUARIOS_PASSWORD_HISTORIAL AS pass, ROL_DESCRIPCION AS tipo FROM usuario_usuarios INNER JOIN usuario_rol ON ROL_ID=USUARIOS_TIPO WHERE ROL_DESCRIPCION = '{$type}' AND USUARIOS_ACTIVO = 1");
                    $arreglo = Array();
                    foreach($tipo as $presente)
                    {
                        array_push($arreglo,Array("id" => $presente["id"], "nickname" => $presente["nickname"], "nombre" => $presente["nombre"], "rol" => $presente["tipo"]));
                    }
                    return Array("mensaje" => "ok", "data"  =>$arreglo);
                }

            }
        }
        return Array("mensaje" => "error");
    }
?>