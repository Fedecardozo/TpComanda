<?php

use Illuminate\Support\Arr;

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

        public function __get($name)
        {
            switch ($name) 
            {
                case 'Codigo':
                    return $this->codigo ?? "No tiene"; break;
                case 'NombreCliente':
                    return $this->nombreCliente ?? "No tiene"; break;
            }
        }

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

        public static function TraerUnaMesa($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,codigo,estado,nombreCliente FROM mesas WHERE id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchObject("Mesa");
        }

        public static function ListarMesas()
        {
            $retorno = array();
        
            foreach (self::TraerMesas() as $value) 
            {
                if($value instanceof Mesa)
                {
                    $clonado = clone $value;
                    $clonado->nombreCliente = $clonado->NombreCliente;
                    $clonado->codigo = $value->Codigo;
                    array_push($retorno,$clonado);
                }
            }

            return $retorno;
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

        public static function ModificarMesa($id, $codigo, $estado,$nombreCliente)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE mesas SET codigo = :codigo, estado = :estado, nombreCliente = :nombreCliente WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':nombreCliente', $nombreCliente, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
        }

    }

?>