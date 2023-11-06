<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Usuario
    {

        public const PUESTO_BARTENDER = "Bartender";
        public const PUESTO_CERVECERO = "Cervecero";
        public const PUESTO_COCINERO = "Cocinero";
        public const PUESTO_MOZO = "Mozo";
        public const PUESTO_ADMIN = "Admin";

        public const ESTADO_ACTIVO = "Activo";
        public const ESTADO_INACTIVO = "Inactivo";

        public $id;
        public $nombre;
        public $apellido;
        public $dni;
        public $puesto;
        public $estado;

        public function CrearUsuario()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into usuarios (nombre,apellido,dni,puesto,estado) values(:nombre,apellido,:dni,:puesto,:estado)");
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":apellido",$this->apellido,PDO::PARAM_STR);
            $consulta->bindValue(":dni",$this->dni,PDO::PARAM_INT);
            $consulta->bindValue(":puesto",$this->puesto,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerUsuarios()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre,apellido,dni,puesto,estado FROM usuarios");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
        }


    }

?>