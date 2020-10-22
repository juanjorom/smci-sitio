<?php
    /**
     * Funcion para verificar que la version de la aplicación es la mas nueva
     * 
     * @param String $token Token de aplicacion
     * @return Array Arreglo con la informacin solicitada
     * @author Juanjo Romero
     */
    function check_version($token)
    {
        $consulta = $GLOBALS["DB"]->ejecutar_consulta("SELECT VERSION_RELEASE FROM aplicacion_version WHERE VERSION_TOKEN='{$token}'");
        if($consulta)
        {
            return Array("mensaje" => "ok", "data" => $consulta["VERSION_RELEASE"]);
        }
        return Array("mensaje" => "error");
    }

    /**
     * Funcion para actulizar la version en la base de datos
     * @param Object $data Objeto con la informacion de la peticion
     * @return Array Arreglo con la informacion solicitada
     * @author Juanjo Romero
     */
    function update_version($data)
    {
        $consulta = $GLOBALS["DB"]->ejecutar_consulta("UPDATE aplicacion_version SET VERSION_RELEASE ='{$data->version}' WHERE VERSION_TOKEN ='{$data->token}'");
        if($consulta)
        {
            return Array("mensaje" => "ok");
        }
        return Array("mensaje" => "error");
    }
?>