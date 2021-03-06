<?php
    /**
     * Funcion para generar un nuevo token
     * Todos los usurios tienen acceso una vez que se logean
     * 
     * @param Int|String $id El id del usuario con el que se generar el token 
     * @return String El token generado
     * @author Juanjo Romero
     * 
    */
    function generar_token($id)
    {
        $file = fopen("pruebas.txt", 'a+');
        $fecha = date("Y-m-d H:i:s");
        $header = base64_encode("{\"alg\" : \"HS256\", \"typ\" : \"JWT\" }");
        $payload = base64_encode("{\"id\": \"" .$id . "\", \"timestamp\": \"" . $fecha ."\"}");
        $signature = base64_encode(hash_hmac("sha256", $header .".". $payload, "s1m2c3i4"));
        fwrite($file, $fecha . PHP_EOL );
        fwrite($file, $header . PHP_EOL);
        fwrite($file, $payload . PHP_EOL);
        fwrite($file, $signature .PHP_EOL);
        fwrite($file, $id .PHP_EOL);
        fclose($file);

        return  $payload.'.'. $signature;

    }

    /**
     * Funcion para validar que el token que se envia es correcto y para determinar a quien le pertenece
     * Todos los usuarios tienen acceso
     * 
     * @param String $token El token a analizar
     * @return Array|Bool Si el token es valido retorna Array, en caso contrario False
     * @author Juanjo Romero
     */
    function validar_token($token)
    {
        $header = base64_encode("{\"alg\" : \"HS256\", \"typ\" : \"JWT\" }");
        $partes = explode(".", $token);
        $compare = base64_encode(hash_hmac("sha256", $header ."." . $partes[0], "s1m2c3i4"));

        if($compare==$partes[1]){
            $jason = json_decode(base64_decode($partes[0]), true);
            if(validar_token_db(Array("token" => $token, "id" => $jason['id'])))
            {
                return $jason;
            }
        }
        return false;
    }

    /**
     * Funcion Auxiliar para validar que el token que se envio se encuentra registrado en la base de datos y le pertenece al usuario mencionado
     * Usada por la funcion de validar_token()
     * 
     * @param Array $datos Los datos para verificar el token
     * @return Bool Devuelve True si los datos son v??lidos, False en caso contrario
     */
    function validar_token_db($datos)
    {
        $usuario = $GLOBALS['DB']->ejecutar_consulta("SELECT USUARIOS_TOKEN, USUARIOS_ULT_CON FROM usuario_usuarios WHERE USUARIOS_ID = {$datos['id']}");
        if($usuario["USUARIOS_TOKEN"]==$datos['token'])
        {
            $fecha = date_create($usuario["USUARIO_ULT_CON"]);
            $current = date_create(date("Y-m-d H:i:s"));
            $interval = date_diff($fecha, $current);
            $difer = intval($interval->format("%a"));
            if($difer==0){
                return true;
            }
        }
        return false;
    }

    /**
     * Funcion auxiliar para validar que los datos enviados por POST, PUT y DELETE hayan sido declarados y no sean NULL
     * Usada por las funciones de peticiones POST, PUT y DELETE
     * 
     * @param Object $objeto Objeto con los datos recibidos de la peticion
     * @param Array $valores Arreglo con los valores que deber??a de traer el objeto
     * @param Int $count Total de valores que deber??a traer el objeto, por defecto es 1
     * @return Bool Si el total de valores definidos coincide con el total deseado, devuelve True; en caso contrario devuelve False
     * @author Juanjo Romero
     */
    function validar_parametros_option($objeto, $valores, $count=1)
    {
        $total=0;
        foreach($valores as $actual)
        {
            if(isset($objeto->{$actual}))
            {
                $total++;
            }
        }
        if($total==$count)
        {
            return true;
        }
        return false;
    }

    /**
     * Funcion Auxiliar para validar que los parametros enviados por GET hayan sido declarados y no sean NULL
     * Usada solo por las funciones de peticiones GET
     * 
     * @param Arreglo $parametros Arreglo con los valores que deberian estar definidos
     * @param Int $count Total de valores definidos esperados
     * @return Bool Devuelve True si los valores definidos coincides con los valores definidos esperados, en caso contrario devuelve False
     * @author Juanjo Romero
     */
    function validar_parametros_get($parametros, $count=1)
    {
        $total=0;
        foreach($parametros as $actual)
        {
            if(isset($actual))
            {
                $total++;
            }
        }
        if($total==$count)
        {
            return true;
        }
        return false;
    }

    /**
     * Funcion para comparar las contrase??as
     * @param String $passwordUsuario La contrase??a ingresada por el usuario
     * @param String $passwordDb La contrase??a guardada en la base de datos
     * @return Bool Devuelve true si las contrase??as coinciden; en caso contrario, devuelve false
     * 
     * @author Juanjo Romero
     */
    function compare_passwords($passwordUsuario, $passwordDb)
    {
        if(!isset($passwordUsuario) || !isset($passwordDb)){
            return false;
        }
        $compaar = base64_encode(hash_hmac("sha256", $passwordUsuario, "s1m2c3i4"));
        $GLOBALS["LOG"]["DB"]->write("Password Actual " . $passwordUsuario);
        $GLOBALS["LOG"]["DB"]->write("Password Generado " . $compaar);
        $GLOBALS["LOG"]["DB"]->write("Password DB " . $passwordDb);
        if($passwordDb == $compaar){
            return true;
        }
        return false;
    }
    //ZDg4YzM1OGM5N2IzNDU2NjIwMmRlODM1NWI0NTE0ZTM1ZmYzYmIzZGMzNDhjYmFmMjYzYmMzZTZmODkyNDRjMw==
    //ZDg4YzM1OGM5N2IzNDU2NjIwMmRlODM1NWI0NTE0ZTM1ZmYzYmIzZGMzNDhjYmFmMjYzYmMzZTZmODkyNDRjMw==
    /**
     * Funcion para encriptar la contrase??a
     * @param String $password La cadena que sera convertida
     * @return String La cadena encriptada
     * 
     * @author Juanjo Romero
     */
    function create_password($password)
    {
        $encript = base64_encode(hash_hmac("sha256", $password, "s1m2c3i4"));
        $GLOBALS["LOG"]["DB"]->write("Password Generado " . $encript);
        return $encript;
    }
?>
