<?php

    include_once "./BaseDatos/accesoDatos.php";

    class Pedido
    {

        public $id;
        public $id_usuario;
        public $id_mesa;

        public function CrearPedido()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (id_usuario,id_mesa) values(:id_usuario,:id_mesa)");
            $consulta->bindValue(":id_usuario",$this->id_usuario,PDO::PARAM_INT);
            $consulta->bindValue(":id_mesa",$this->id_mesa,PDO::PARAM_INT);
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


    }

?>