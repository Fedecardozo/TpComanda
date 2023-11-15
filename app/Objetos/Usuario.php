<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Usuario
    {

        public const PUESTO_BARTENDER = "Bartender";
        public const PUESTO_CERVECERO = "Cervecero";
        public const PUESTO_COCINERO = "Cocinero";
        public const PUESTO_COCINERO_CANDY = "CocineroCandy";
        public const PUESTO_MOZO = "Mozo";
        public const PUESTO_SOCIO = "Socio";
        public const PUESTO_ADMIN = "Admin";

        public const ESTADO_ACTIVO = "Activo";
        public const ESTADO_INACTIVO = "Inactivo";

        public $id;
        public $nombre;
        public $dni;
        public $puesto;
        public $estado;
        public $fechaAlta;
        public $fechaBaja;

        public function CrearUsuario()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into usuarios (nombre,fechaAlta,dni,puesto,estado) values(:nombre,:fechaAlta,:dni,:puesto,:estado)");
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":fechaAlta",$this->fechaAlta,PDO::PARAM_STR);
            $consulta->bindValue(":dni",$this->dni,PDO::PARAM_INT);
            $consulta->bindValue(":puesto",$this->puesto,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerUsuarios()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,fechaAlta,dni,puesto,estado,fechaBaja FROM usuarios");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
        }

        public static function TraerUnUsuario($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,fechaAlta,dni,puesto,estado,fechaBaja FROM usuarios WHERE id = '$id'");
            $consulta->execute();
            return $consulta->fetchObject("Usuario");
        }

        public static function TraerUnUsuarioPorNombreDni($dni,$nombre)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,fechaAlta,dni,puesto,estado,fechaBaja FROM usuarios WHERE dni = '$dni' AND nombre = '$nombre'");
            $consulta->execute();
            return $consulta->fetchObject("Usuario");
        }

        public function __get($name)
        {
            switch ($name) 
            {
                case 'IdSector':
                    switch ($this->puesto) 
                    {
                        case self::PUESTO_BARTENDER:
                            return Sector::ID_BARRA_DE_TRAGOS;
                            break;
                        case self::PUESTO_CERVECERO:
                            return Sector::ID_BARRA_CHOPERAS;
                            break;
                        case self::PUESTO_COCINERO:
                            return Sector::ID_COCINA;
                            break;
                        case self::PUESTO_COCINERO_CANDY:
                            return Sector::ID_CANDY_BAR;
                            break;
                    }
                    break;
            }
        }

    }

?>