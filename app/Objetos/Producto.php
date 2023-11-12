<?php

    include_once "./BaseDatos/accesoDatos.php";
    require_once "./Objetos/Sector.php";

    class Producto
    {
        public const TIPO_TRAGO_VINO = "Trago-Vino";
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

        public function __get($name)
        {
            switch ($name) 
            {
                case 'SectorID':
                    switch ($this->tipo) 
                    {
                        case self::TIPO_COMIDA:
                            return Sector::ID_COCINA;
                            break;
                        case self::TIPO_POSTRE:
                            return Sector::ID_CANDY_BAR;
                            break;
                        case self::TIPO_CERVEZA:
                            return Sector::ID_BARRA_CHOPERAS;
                            break;
                        case self::TIPO_TRAGO_VINO:
                            return Sector::ID_BARRA_DE_TRAGOS;
                            break;
                    }
                    break;
            }
        }

    }

?>