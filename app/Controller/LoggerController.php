<?php
    include_once "./BaseDatos/accesoDatos.php";

    class LoggerController
    {
        public function TraerTodos( $request,  $response)
        {

            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT  idUsuario,fecha,metodo,accion FROM logs");
            $consulta->execute();
            $logs = $consulta->fetchAll(PDO::FETCH_ASSOC);

            $payload = $logs ? json_encode($logs) : "No hay logs para mostrar"; 

            $response->getBody()->write($payload);
            return $response;


        }
    }


?>