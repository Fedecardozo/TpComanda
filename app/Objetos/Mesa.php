<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Mesa
    {
        private const LENGTH_CODIGO = 5;

        public const ESTADO_ESPERANDO = "Cliente esperando pedido";
        public const ESTADO_COMIENDO = "Cliente comiendo";
        public const ESTADO_PAGANDO = "Cliente pagando";
        public const ESTADO_CERRADA = "Cerrada";

        public $id;
        public $codigo;
        public $estado;
        public $nombreCliente;

        // public function __get($value)
        // {
        //     switch ($value) 
        //     {
        //         case 'Codigo':
        //             $cont = 0;
        //             $codigo = false;
        //             do
        //             {
        //                 $codigo = false;
        //                 $codigo = self::GenerarCodigoAlfanumerico();
        //                 if(!self::VerificarCodigo($codigo))
        //                 {
        //                     break;
        //                 }
        //                 $cont++;
        //             }while($cont<5);
        //             return $codigo;
        //             break;
        //     }
        // }

        public function CrearMesa()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into mesas (nombreCliente,codigo,estado) values(:nombreCliente,:codigo,:estado)");
            $consulta->bindValue(":nombreCliente",$this->nombreCliente,PDO::PARAM_STR);
            $consulta->bindValue(":codigo",$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerMesas()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,codigo,estado,nombreCliente FROM mesas");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
        }

        public static function GenerarCodigoAlfanumerico() 
        {
            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codigo = '';
            $maxCaracteres = strlen($caracteres) - 1;

            for ($i = 0; $i < self::LENGTH_CODIGO; $i++)
            {
                $indice = mt_rand(0, $maxCaracteres);
                $codigo .= $caracteres[$indice];
            }

            return $codigo;
        }

        // private static function VerificarCodigo($codigo)
        // {
        //     $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        //     $consulta = $objetoAccesoDato->RetornarConsulta("SELECT codigo FROM mesas WHERE codigo = :codigo");
        //     $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
        //     $consulta->execute();
        //     return $consulta->fetch();
        // }

        public static function ObtenerCodigo($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT codigo FROM mesas WHERE id = '$id'");
            $consulta->execute();
            return $consulta->fetchColumn();
        }

    }

?>