<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Detalle
    {
        public $id;
        public $id_producto;
        public $cantidad;
        public $id_pedido;
        public $duracion;
        public $id_sector;

        public function CrearDetalle()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into detalles (id_producto,cantidad,id_pedido,duracion,id_sector) values(:id_producto,:cantidad,:id_pedido,:duracion,:id_sector)");
            $consulta->bindValue(":id_producto",$this->id_producto,PDO::PARAM_INT);
            $consulta->bindValue(":cantidad",$this->cantidad,PDO::PARAM_INT);
            $consulta->bindValue(":id_pedido",$this->id_pedido,PDO::PARAM_INT);
            $consulta->bindValue(":duracion",$this->duracion,PDO::PARAM_INT);
            $consulta->bindValue(":id_sector",$this->id_sector,PDO::PARAM_INT);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerDetalles()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id_producto,cantidad,id_pedido,duracion,id_sector FROM detalles");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        }

    }


?>