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
        public $estado;

        public function CrearDetalle()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into detalles (id_producto,cantidad,id_pedido,id_sector) values(:id_producto,:cantidad,:id_pedido,:id_sector);");
            $consulta->bindValue(":id_producto",$this->id_producto,PDO::PARAM_INT);
            $consulta->bindValue(":cantidad",$this->cantidad,PDO::PARAM_INT);
            $consulta->bindValue(":id_pedido",$this->id_pedido,PDO::PARAM_INT);
            $consulta->bindValue(":id_sector",$this->id_sector,PDO::PARAM_INT);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerDetalles()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_producto,cantidad,id_pedido,duracion,id_sector FROM detalles;");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        }

        public static function TraerDetalle_Id_sector($id,$id_sector)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_producto,cantidad,id_pedido,duracion,id_sector FROM detalles WHERE id = '$id' AND id_sector = '$id_sector';");
            $consulta->execute();
            return $consulta->fetchObject("Detalle");
        }

        public static function IssetDuracion($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_producto,cantidad,id_pedido,duracion,id_sector FROM detalles WHERE id = '$id' AND duracion IS NOT NULL;");
            $consulta->execute();
            return $consulta->fetchObject("Detalle");
        }

        public static function ModificarEstado($id, $estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE detalles SET estado = :estado WHERE id = :id;");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
        }

        public static function AddDuracion($id, $duracion,$estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE detalles SET  duracion = :duracion , estado = :estado WHERE id = :id AND duracion IS NULL");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':duracion', $duracion, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
        }

    }


?>