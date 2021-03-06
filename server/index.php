<?php
    $GLOBALS['SYSTEM_PATH'] = "/";
   
    require_once("./bin/db.php");
    require_once("./conf/conf.php");
    require_once("./funciones.php");
    include_once("./bin/tools.php");
    include_once("./bin/log.php");
    $GLOBALS['LOG']['DB'] = new Log("./log/DB-".date("Y-m-d").".txt" );
    $GLOBALS['LOG']['Sesion'] = new Log("./log/Sesion-".date("Y-m-d").".txt");
    $GLOBALS['LOG']['Usuarios'] = new Log("./log/Usuarios-".date("Y-m-d").".txt");
    $GLOBALS['LOG']['Peticiones'] = new Log("./log/Peticiones-".date("Y-m-d").".txt");
    
    $ruta = $_GET["token"];
    
    $recurso= $_GET['recurso'];
    $metodo = $_SERVER['REQUEST_METHOD'];
    $datos = json_decode(file_get_contents("php://input"));

    /*$file = fopen("pruebas.txt", 'a+');
    fwrite($file, date("Y-m-d H:i:s") . PHP_EOL );
    fwrite($file, $metodo . PHP_EOL );
    fwrite($file, $recurso . PHP_EOL);
    fwrite($file, $datos->token . PHP_EOL);
    fwrite($file, $_GET["token"] .PHP_EOL);
    fwrite($file, $_SERVER["QUERY_STRING"] .PHP_EOL);

    fclose($file);*/   

    $GLOBALS['LOG']['Peticiones']->write("Peticion por metodo " .$metodo ." al recurso ". $recurso);
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
                case "addBoletera":
                    $RESPUESTA = add_boletera($datos);
                break;
                case "verify":
                    $RESPUESTA = verificar_password($datos);
                break;
                case "openLap":
                    $RESPUESTA = abrir_vuelta($datos);
                break;
                case "addModulo":
                    $RESPUESTA = add_modulo($datos);
                break;
                case "addUnidad":
                    $RESPUESTA = add_unidad($datos);
                break;
                case "ingresarPago":
                    $RESPUESTA = realizar_pago($datos);
                break;
                case "realizarRetiro":
                    $RESPUESTA = realizar_retiro($datos);
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
                case "getAllBoleteras":
                    $RESPUESTA = get_all_boleteras($_GET['token']);
                break;
                case "getAllPermisionarios":
                    $RESPUESTA = get_permisionarios($_GET['token']);
                break;
                case "getAllUnidades":
                    $RESPUESTA = get_all_unidades($_GET['token']);
                break;
                case "getAllRutas":
                    $RESPUESTA = get_all_rutas($_GET['token']);
                break;
                case "getAllOpenLaps":
                    $RESPUESTA = get_all_vueltas_abiertas($_GET['token']);
                break;
                case "getLapsbyUnidad":
                    $RESPUESTA = get_vuelta_by_unidad($_GET['token'], $_GET['unidad']);
                break;
                case "getAllTurnos":
                    $RESPUESTA = get_all_turnos($_GET['token']);
                break;
                case "getTurnoById":
                    $RESPUESTA = get_turno_by_id($_GET["token"], $_GET["turno"]);
                break;
                case "getBoleterasAsignar":
                    $RESPUESTA = get_boleteras_asignables($_GET["token"], $_GET["unidad"]);
                break;
                case "getPermisos":
                    $RESPUESTA = get_permisos_usuario($_GET["token"]);
                break;
                case "getRoles":
                    $RESPUESTA = get_roles($_GET["token"]);
                break;
                case "getMovimientos":
                    $RESPUESTA = get_movimientos($_GET["token"]);
                break;
                case "getLiquidaciones":
                    $RESPUESTA = get_liquidaciones($_GET["token"]); 
                break;
                case "getRecaudacion":
                    $RESPUESTA = get_recaudado($_GET["token"]);
                break;
                case "getVueltas":
                    $RESPUESTA = get_vueltas_closed($_GET["token"], $_GET["inicio"], $_GET["fin"]);
                break;
                case "getReportes":
                    $RESPUESTA = get_reportes($_GET["token"]);
                break;
                case "getVentas":
                    $RESPUESTA = get_ventas_fecha($_GET["token"],$_GET["inicio"],$_GET["fin"], $_GET["filtro"]);
                break;
                default;
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
                case "updatePassword":
                    $RESPUESTA = update_password($datos);
                break;
                case "closeSesion":
                    $RESPUESTA = cerrar_sesion($datos);
                break;
                case "closeLap":
                    $RESPUESTA = close_lap($datos);
                break;
                case "pagarTurno":
                    $RESPUESTA = pagar_turno($datos);
                break;
                case "pagarPermisionario":
                    $RESPUESTA = pagar_permisionario($datos);
                break;
                case "changeUnidad":
                    $RESPUESTA = change_unidad($datos);
                break;
                case "changePassword":
                    $RESPUESTA = update_password_admin($datos);
                break;
                default:
                    $RESPUESTA= Array("mensaje" => "Recurso no existe");
                break;
            }
        break;

        case "DELETE":
            switch ($recurso)
            {
                case "deleteUser":
                    $RESPUESTA = delete_user($_GET['token'],$_GET['id']);
                break;
                case "deleteBoletera":
                    $RESPUESTA = delete_boletera($_GET['token'],$_GET['codigo']);
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