<?php
    class DB {
        var $CONNECTION;
        var $HOST;
        var $DATABASE;
        var $USER;
        var $PASSWORD;
        var $PORT;

        /**
         * @param string $HOST servidor donde se aloja la base de datos
         * @param string $USER usuario para la base de datos
         * @param string $PASSWORD contraseña del usuario
         * @param string $DATABASE nombre de la base de datos
         * @param string $PORT puerto de la base de datos, default es el 3306
         * @return boolean true si se pudo conectar, false si no
         */

        function __construct($HOST, $USER, $PASSWORD, $DATABASE,$PORT = '3306')
        {
            $this->HOST = $HOST;
            $this->DATABASE = $DATABASE;
            $this->USER = $USER;
            $this->PASSWORD = $PASSWORD;
            $this->PORT = $PORT;
            
            
            return $this->connect();
        }
        
        private function connect()
        {
            $this->CONNECTION = new  mysqli($this->HOST, $this->USER, $this->PASSWORD, $this->DATABASE, (int) $this->PORT );

            if($this->CONNECTION->connect_error)
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        /**
         * Funcion para ejecutar un query del que se espera mas de una fila
         * 
         * @param String $QUERY Query que se ejecutar en la base de datos
         * @return Array  Un array de dos dimensiones con los datos solicitados
         * 
         */
        function ejecutar_consulta_multiple($QUERY)
        {
            $result = $this->CONNECTION->query($QUERY);
            if($result===true){
                return true;
            }else if($result){
                
                while($retorno = mysqli_fetch_array($result))
                {
                    $final[] = $retorno;
                }
                return $final;
            }
            return false;
        }

        function ejecutar_consulta($QUERY)
        {
            $result = $this->CONNECTION->query($QUERY);
            if($result===true){
                return true;
            }else if($result){
                $retorno = mysqli_fetch_array($result);
                return $retorno;
            }
            return false;
        }

        function id()
        {
            return $this->CONNECTION->insert_id;
        }

        function close_database()
        {
            $this->CONNECTION->close();
        }
    }
?>