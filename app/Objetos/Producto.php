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

        public function CrearProductoConId()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into productos (id,nombre,tipo,precio) values(:id,:nombre,:tipo,:precio)");
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":tipo",$this->tipo,PDO::PARAM_STR);
            $consulta->bindValue(":precio",$this->precio);
            $consulta->bindValue(":id",$this->id,PDO::PARAM_INT);
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

        public static function VaciarProductos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("TRUNCATE productos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
        }

        public static function TraerUnProducto($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,tipo,precio FROM productos WHERE id = '$id';");
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

        public static function CargarCsv($destino)
        {
            // $destino = "./ArchivoCsv/productos.csv";
            $producto = new Producto();
            if(file_exists($destino) &&
             ($archivo = fopen($destino,"r")) !== FALSE)
            {
                self::VaciarProductos();//vacio la tabla
                while (($fila = fgetcsv($archivo,1000,',')) !== FALSE) 
                {
                    $producto->id = $fila[0];
                    $producto->nombre = $fila[1];
                    $producto->tipo = $fila[2];
                    $producto->precio = $fila[3];
                    $producto->CrearProductoConId();
                }
                fclose($archivo);
            }
        }

        public static function ConvertirCsv($destino)
        {
            // $destino = "./ArchivoCsv/productos.csv";
            $productos = self::TraerProductos();
           
            $archivo = fopen($destino,'w+');
            foreach ($productos as $value) 
            {
                fputcsv($archivo,[$value->id,$value->nombre,$value->tipo,$value->precio]);
            }
            fclose($archivo);

        }

    }

?>