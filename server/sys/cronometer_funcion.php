<?php
    /**
     * Funcion para revision rapida de una vuelta
     * @param Array $vuelta La vuelta que sera revisada de manera rápida
     * @return String|Boolean Si se debe de revisar retornara un string con el motivo, sino retornara False
     * 
     * @author Juanjo Romero
     */
    function revision_vuelta($vuelta)
    {
        if($vuelta["BOLETOS"])
        {
            switch($vuelta["NUMERO"])
            {
                case 1:
                    if($vuelta["BOLETOS"]<60)
                    {
                        return "BOLETAJE BAJO";
                    }
                    return false;
                break;
                case 2:
                    if($vuelta["BOLETOS"]<30)
                    {
                        return "BOLETAJE BAJO";
                    }
                    return false;
                break;
                case 3:
                    if($vuelta["BOLETOS"]<50)
                    {
                        return "BOLETAJE BAJO";
                    }
                    return false;
                break;
                default:
                    return false;
                break;
            }
        }
        return false;
    }

    /**
     * Funcion para revisar boleto por boleto
     * @param Array $boletera  Un arreglo con los valores de la boletera
     * @return Array Un array con las estadisticas de boletos
     * 
     * @author Juanjo Romero
     */
    function revision_boletera($boletera)
    {
        $sumas = Array();
        $boletosJson = json_decode($boletera["BOLETOS"]);
        foreach($boletosJson as $boletoActual)
        {
            $sumas[$boletoActual]++;
        }
        return $sumas;
    }

    /**
     * Funcion para determinar si la vuelta se debe de revisar despues de determinar la estadistica de la cantidad de boletos
     * 
     * @param Array $estadistica Arreglo con el porcentaje de frecuencia de cada uno de los montos de boletos
     * @return String|Boolean Si la funcion determina que debe ser revisada se devolvera el motivo, sino se devuelve false
     * 
     * @author Juanjo Romero
     */
    function revision_fondo($estadistica)
    {
        $suma = 0;
        if(count($estadistica)>1)
        {
            $medio = buscar_limite($estadistica, 0, 14);           
            for($i = 0; $i<$medio; $i++)
            {
                $suma+= $estadistica[$i];
            }
        }
        if($suma>40)
        {
            return "Porcentaje de boletos menores de 14 de " . $suma;
        }
        return false;
    }


    function buscar_limite($arr, $ini, $limite)
    {
        $keys = array_keys($arr);
        $medio = intdiv(($ini-count($arr)), 2);
        if($keys[$medio]>$limite)
        {
            return $medio;
        }
        else
        {
            return buscar_limite($arr, $medio, $limite);
        }
    }
?>