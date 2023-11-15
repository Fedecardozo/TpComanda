<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Pedido
    {
        public const ESTADO_PREPARACION = "Preparacion";
        public const ESTADO_ENTREGADO = "Entregado";
        public const ESTADO_CANCELADO = "Cancelado";
        public const ESTADO_LISTO = "Listo para servir";

        public $id;
        public $id_usuario;
        public $id_mesa;
        public $codigo;
        public $estado; // preparacion, entregado, cancelado
        public $fechaInicio;
        public $fechaEntrega;

        public function CrearPedido()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega) values(:id_usuario,:id_mesa,:codigo,:estado,:fechaInicio,:fechaEntrega)");
            $consulta->bindValue(":id_usuario",$this->id_usuario,PDO::PARAM_INT);
            $consulta->bindValue(":id_mesa",$this->id_mesa,PDO::PARAM_INT);
            $consulta->bindValue(":codigo",$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->bindValue(":fechaInicio",$this->fechaInicio,PDO::PARAM_STR);
            $consulta->bindValue(":fechaEntrega",$this->fechaEntrega,PDO::PARAM_STR);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerPedidos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        public static function TraerPedidosPorEstado($estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega FROM pedidos WHERE estado = '$estado'");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        public static function TraerUnPedido($codigo)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega FROM pedidos WHERE codigo = :codigo");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchObject("Pedido");
        }
        
        public static function TraerUnPedidoPorEstado($estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega FROM pedidos WHERE estado = :estado");
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        public static function CambiarEstadoPedido($id,$estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedidos SET estado = '$estado' WHERE id = '$id'");
            $consulta->execute();
            return $consulta->rowCount();
        }

    }

?>