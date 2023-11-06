<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Mesa
    {
        public const ESTADO_ESPERANDO = "Cliente esperando pedido";
        public const ESTADO_COMIENDO = "Cliente comiendo";
        public const ESTADO_PAGANDO = "Cliente pagando";
        public const ESTADO_CERRADA = "Cerrada";

        public $id;
        public $codigo;
        public $estado;

        public function CrearMesa()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into mesas (codigo,estado) values(:codigo,:estado)");
            $consulta->bindValue(":codigo",$this->codigo,PDO::PARAM_INT);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerMesas()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,codigo,estado FROM mesas");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
        }


    }

?>