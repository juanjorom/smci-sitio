<?php

    function validar_login()
    {
        if(!isset($_POST['USER']) && !isset($_POST['PASSWORD']))
        {
            return Array("mensaje" => "Usuario y Contraseña no especificado");
        }
        
        $st_mail = $_POST['mail'];
        $st_password = $_POST['password'];

        $ar_data = $GLOBALS["DB"]->ejecutar_consulta("SELECT USERS_ID FROM users WHERE USERS_MAIL={'$st_mail'} AND USERS_PASSWORD = $st_password ;");

        if($ar_data)
        {
            return Array("mensaje" => "ok", "datos" => $ar_data);
        }
        return Array("mensaje" => "Usuario no existe");
    }
?>