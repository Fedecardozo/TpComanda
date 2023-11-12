<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Pedido
    {
        public const ESTADO_PREPARACION = "Preparacion";
        public const ESTADO_ENTREGADO = "Entregado";
        public const ESTADO_CANCELADO = "Cancelado";

        public $id;
        public $id_usuario;
        public $id_mesa;
        public $id_producto;
        public $codigo;
        public $estado; // preparacion, entregado, cancelado
        public $fechaInicio;
        public $fechaEntrega;
        public $cantidad;

        public function CrearPedido()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (id_usuario,id_mesa,id_producto, codigo, estado, fechaInicio,fechaEntrega,cantidad) values(:id_usuario,:id_mesa,:id_producto,:codigo,:estado,:fechaInicio,:fechaEntrega,:cantidad)");
            $consulta->bindValue(":id_usuario",$this->id_usuario,PDO::PARAM_INT);
            $consulta->bindValue(":id_mesa",$this->id_mesa,PDO::PARAM_INT);
            $consulta->bindValue(":id_producto",$this->id_producto,PDO::PARAM_INT);
            $consulta->bindValue(":codigo",$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(":estado",$this->estado,PDO::PARAM_STR);
            $consulta->bindValue(":fechaInicio",$this->fechaInicio,PDO::PARAM_STR);
            $consulta->bindValue(":fechaEntrega",$this->fechaEntrega,PDO::PARAM_STR);
            $consulta->bindValue(":cantidad",$this->cantidad,PDO::PARAM_INT);
            $consulta->execute();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }

        public static function TraerPedidos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa,id_producto, codigo, estado, fechaInicio,fechaEntrega,cantidad FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        public static function TraerUnPedido($codigo)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id_usuario,id_mesa,id_producto, codigo, estado, fechaInicio,fechaEntrega,cantidad FROM pedidos WHERE codigo = :codigo");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchObject("Pedido");
        }
        

    }

?>