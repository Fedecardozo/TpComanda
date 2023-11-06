<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Producto
    {

        public $id;
        public $nombre;
        public $tipo;
        public $precio;
        public $stock;

        public function CrearProducto()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre,tipo,precio,stock) values(:nombre,:tipo,:precio,:stock)");
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":tipo",$this->tipo,PDO::PARAM_STR);
            $consulta->bindValue(":precio",$this->precio);
            $consulta->bindValue(":stock",$this->stock,PDO::PARAM_INT);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerProductos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,tipo,precio,stock FROM productos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
        }


    }

?>