<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Pedido
    {
        private const LENGTH_CODIGO = 5;
        public const ESTADO_PREPARACION = "Preparacion";
        public const ESTADO_ENTREGADO = "Entregado";
        public const ESTADO_CANCELADO = "Cancelado";

        public $id;
        public $id_usuario;
        public $id_mesa;
        public $codigo;
        public $estado; // preparacion, entregado, cancelado
        public $fechaInicio;
        public $fechaEntrega;

        public function __get($value)
        {
            switch ($value) 
            {
                case 'Codigo':
                    $cont = 0;
                    $codigo = false;
                    do
                    {
                        $codigo = false;
                        $codigo = self::GenerarCodigoAlfanumerico();
                        if(!self::VerificarCodigo($codigo))
                        {
                            break;
                        }
                        $cont++;
                    }while($cont<5);
                    return $codigo;
                    break;
            }
        }

        public function CrearPedido()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (id_usuario,id_mesa, codigo, estado, fechaInicio) values(:id_usuario,:id_mesa,:codigo,:estado,:fechaInicio)");
            $consulta->bindValue(":id_usuario",$this->id_usuario,PDO::PARAM_INT);
            $consulta->bindValue(":id_mesa",$this->id_mesa,PDO::PARAM_INT);
            $consulta->bindValue(":codigo",$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->bindValue(":fechaInicio",$this->fechaInicio,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerPedidos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_pedido FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
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

        private static function VerificarCodigo($codigo)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT codigo FROM pedidos WHERE codigo = :codigo");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetch();
        }

    }

?>