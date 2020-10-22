<?php

    /**
     * Funcion para agregar boleteras
     * @param Object $data Los datos de la peticion
     * @return Array Arreglo con la información solicitada
     * @author Juanjo Romero
     */
    function add_boletera($data)
    {
        if(validar_token($data))
        {
            if($GLOBALS["DB"]->ejecutar_consulta("INSERT INTO datos_boleteras(BOLETERAS_CODIGO, BOLETERAS_BOL_VEND, BOLETERAS_BOL_REST, BOLETERAS_MONTO, BOLETERAS_ESTADO, BOLETERAS_BOLETOS) VALUES ($data->codigo,0,$data->totalBoletos,0,'NO ASIGNADO',$data->boletos)"))
            {
                $boletera = $GLOBALS["DB"]->ejecutar_consulta("SELECT BOLETERAS_CODIGO, BOLETERAS_BOL_VEND, BOLETERAS_BOL_REST, BOLETERAS_MONTO, BOLETERAS_ESTADO, BOLETERAS_BOLETOS FROM datos_boleteras WHERE BOLETERAS_CODIGO = $data->codigo ");
                return Array("mensaje"=> "ok", "data" => Array("codigo" => $boletera["BOLETERAS_CODIGO"], "boletosVendidos" => $boletera["BOLETERAS_BOL_VEND"], "boletosRestantes" => $boletera["BOLETERAS_BOL_REST"], "monto" => $boletera["BOLETERAS_MONTO"]));
            }
        }
    }
?>