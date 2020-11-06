<?php

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
?>