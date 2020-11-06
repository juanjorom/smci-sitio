<?php
    /**
     * Clase para el manejo de las bitacoras
     * @author Juanjo Romero
     * 
     */
    class Log
    {
        var $archivo;

        /**
         * @param $archivo La ruta donde estara el archivo
         */
        function __construct($archivo)
        {
            $this->archivo = $archivo;
        }

        /**
         * Funcion para abrir el archivo
         * @return Handle El manejador que contiene el stream al archivo
         * @author Juanjo Romero
         */
        function open()
        {
            return fopen($this->archivo, 'a+');
        }

        /**
         * Funcion para cerrar el archivo
         * @param Handle $archivo El stream que maneja el archivo abierto
         * @author Juanjo Romero
         */
        function close($archivo)
        {
            fclose($archivo);
        }

        /**
         * Funcion para escribir en el archivo
         * @param String $texto El texto a escribir en el archivo
         * @author Juanjo Romero
         */
        function write($texto)
        {
            $file = $this->open();
            fwrite($file,  date("Y-m-d H:i:s") . $texto . PHP_EOL);
            $this->close($file);
        }
    }
?>