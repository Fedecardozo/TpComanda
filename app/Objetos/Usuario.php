<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Usuario
    {

        public const PUESTO_BARTENDER = "Bartender";
        public const PUESTO_CERVECERO = "Cervecero";
        public const PUESTO_COCINERO = "Cocinero";
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
            return $consulta->fetch(PDO::FETCH_CLASS, "Usuario");
        }

    }

?>