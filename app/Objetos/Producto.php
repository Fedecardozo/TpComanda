<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Producto
    {
        public const TIPO_VINO = "Vino";
        public const TIPO_TRAGO = "Trago";
        public const TIPO_CERVEZA = "Cerveza";
        public const TIPO_COMIDA = "Comida";
        public const TIPO_POSTRE = "Postre";

        public $id;
        public $nombre;
        public $tipo;
        public $precio;

        public function CrearProducto()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre,tipo,precio) values(:nombre,:tipo,:precio)");
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":tipo",$this->tipo,PDO::PARAM_STR);
            $consulta->bindValue(":precio",$this->precio);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerProductos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,tipo,precio FROM productos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
        }

        public static function TraerUnProducto($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,tipo,precio FROM productos WHERE id = '$id'");
            $consulta->execute();
            return $consulta->fetchObject("Producto");
        }

    }

?>