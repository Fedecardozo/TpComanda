<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Encuesta
    {

        public const TIPO_BUENA = "Buena";
        public const TIPO_MALA = "Mala";

        public $id;
        //putuacion
        public $mesa;
        public $restaurante;
        public $mozo;
        public $cocinero;
        public $estrellas;
        public $tipo; //buena o mala
        public $texto;
        public $codigo_pedido;
        public $codigo_mesa;

        public function CrearEncuesta()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO encuestas (mesa,restaurante,mozo,cocinero,estrellas,tipo,texto,codigo_pedido,codigo_mesa) VALUES(:mesa,:restaurante,:mozo,:cocinero,:estrellas,:tipo,:texto,:codigo_pedido,:codigo_mesa);");
            $consulta->bindValue(":mesa",$this->mesa,PDO::PARAM_INT);
            $consulta->bindValue(":restaurante",$this->restaurante,PDO::PARAM_INT);
            $consulta->bindValue(":mozo",$this->mozo,PDO::PARAM_INT);
            $consulta->bindValue(":cocinero",$this->cocinero,PDO::PARAM_INT);
            $consulta->bindValue(":estrellas",$this->estrellas,PDO::PARAM_INT);
            $consulta->bindValue(":tipo",$this->tipo,PDO::PARAM_STR);
            $consulta->bindValue(":texto",$this->texto,PDO::PARAM_STR);
            $consulta->bindValue(":codigo_pedido",$this->codigo_pedido,PDO::PARAM_STR);
            $consulta->bindValue(":codigo_mesa",$this->codigo_mesa,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerUnaEncuesta($codigo_pedido)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,mesa,restaurante,mozo,cocinero,estrellas,tipo,texto,codigo_pedido,codigo_mesa FROM encuestas WHERE codigo_pedido = '$codigo_pedido'");
            $consulta->execute();
            return $consulta->fetchObject("Encuesta");
        }

        public static function TraerEncuestas($tipo,$estrellas)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,mesa,restaurante,mozo,cocinero,estrellas,tipo,texto,codigo_pedido,codigo_mesa FROM encuestas WHERE tipo = '$tipo' AND estrellas <= '$estrellas' ");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
        }

    }


?>
