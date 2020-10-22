<?php
    $GLOBALS['SYSTEM_PATH'] = "/";
    
    require_once("./bin/db.php");
    require_once("./conf/conf.php");
    require_once("./funciones.php");
    include_once("./bin/tools.php");

    $ruta = $_GET["token"];
    
    $recurso= $_GET['recurso'];
    $metodo = $_SERVER['REQUEST_METHOD'];
    $datos = json_decode(file_get_contents("php://input"));

    $file = fopen("pruebas.txt", 'a+');
    fwrite($file, date("Y-m-d H:i:s") . PHP_EOL );
    fwrite($file, $metodo . PHP_EOL );
    fwrite($file, $recurso . PHP_EOL);
    fwrite($file, $datos->token . PHP_EOL);
    fwrite($file, $_GET["token"] .PHP_EOL);
    fwrite($file, $_SERVER["QUERY_STRING"] .PHP_EOL);

    fclose($file);   

    switch ($metodo)
    {
        case "POST":

            switch($recurso)
            {
                case "login":
                    $RESPUESTA = validar_login($datos);
                break;
                case "addUser":
                    $RESPUESTA = create_user($datos);
                break;
                case "validarNickname":
                    $RESPUESTA = validar_nickname($datos);
                break;
                default:
                    $RESPUESTA= Array("mensaje" => "Recurso no existe");
                break;
            }
        break;

        case "GET":
            switch($recurso)
            {
                case "getUser":
                    $RESPUESTA = get_user($_GET['token']);
                break;
                case "getAllUsers":
                    $RESPUESTA = get_all_users($_GET['token']);
                break;
                case "getUsersByRole":
                    $RESPUESTA = get_user_by_type($_GET['token'],$_GET['rol']);
                break;
                case "getVersion":
                    $RESPUESTA = check_version($_GET['token']);
                break;
                default:
                    $RESPUESTA= Array("mensaje" => "Recurso no existe");
                break;
            }
        break;
        
        case "PUT":
            switch($recurso)
            {
                case "editUSer":
                    $RESPUESTA = modify_user($datos);
                break;
                case "updateVersion":
                    $RESPUESTA = update_version($datos);
                break;
                default:
                    $RESPUESTA= Array("mensaje" => "Recurso no existe");
                break;
            }
        break;

        case "DELETE":
            switch ($recurso)
            {
                case "deleteUSer":
                    $RESPUESTA = delete_user($datos);
                break;
                default:
                    $RESPUESTA= Array("mensaje" => "Recurso no existe");
                break;
            }
        break;
    }

    if(isset($_SERVER['HTTP_ORIGIN'])){
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header("Access-Control-Allow-Credencials: true");
        header("Access-Control-Max-Age: 86400");
    }
    if($_SERVER['REQUEST_METHOD']=="OPTIONS"){
        if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        }
        if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }
    

    echo json_encode($RESPUESTA);
    
?>