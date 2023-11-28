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
        public $imagen;
        public $tiempoDemora;

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

        /*public static function TraerPedidos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT pedidos.id,
            pedidos.id_usuario,
            pedidos.id_mesa, 
            pedidos.codigo, 
            pedidos.estado, 
            pedidos.fechaInicio,
            pedidos.fechaEntrega,
            pedidos.destino as imagen, 
            MAX(detalles.duracion) AS 'tiempoDemora' 
            FROM pedidos, detalles;");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }*/

        public static function TraerPedidos()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT
            pedidos.id,
            pedidos.id_usuario,
            pedidos.id_mesa, 
            pedidos.codigo, 
            pedidos.estado, 
            pedidos.fechaInicio,
            pedidos.fechaEntrega,
            pedidos.destino as imagen, 
            IFNULL(MAX(detalles.duracion), 0) AS 'tiempoDemora' 
            FROM
            pedidos
            LEFT JOIN
            detalles ON pedidos.id = detalles.id_pedido
            GROUP BY
            pedidos.id,
            pedidos.id_usuario,
            pedidos.id_mesa, 
            pedidos.codigo, 
            pedidos.estado, 
            pedidos.fechaInicio,
            pedidos.fechaEntrega,
            pedidos.destino;");
                    $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        public static function TraerPedidosPorEstado($estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega,destino as imagen FROM pedidos WHERE estado = '$estado'");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }

        /*public static function TraerUnPedido($codigo)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega,destino as imagen FROM pedidos WHERE codigo = :codigo");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchObject("Pedido");
        }*/

        public static function TraerUnPedido($codigo)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT
            pedidos.id,
            pedidos.id_usuario,
            pedidos.id_mesa, 
            pedidos.codigo, 
            pedidos.estado, 
            pedidos.fechaInicio,
            pedidos.fechaEntrega,
            pedidos.destino as imagen, 
            IFNULL(MAX(detalles.duracion), 0) AS 'tiempoDemora' 
            FROM
            pedidos
            LEFT JOIN
            detalles ON pedidos.id = detalles.id_pedido 
            WHERE pedidos.codigo = '$codigo'
            GROUP BY
            pedidos.id,
            pedidos.id_usuario,
            pedidos.id_mesa, 
            pedidos.codigo, 
            pedidos.estado, 
            pedidos.fechaInicio,
            pedidos.fechaEntrega,
            pedidos.destino;");
            // $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchObject("Pedido");
        }
        
        public static function TraerUnPedidoPorEstado($estado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id_usuario,id_mesa, codigo, estado, fechaInicio,fechaEntrega,destino as imagen FROM pedidos WHERE estado = :estado");
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

        public static function CambiarFechaEstado($id,$fechaEntrega)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedidos SET estado = :estado, fechaEntrega = :fechaEntrega WHERE id = '$id'");
            $consulta->bindValue(':estado', self::ESTADO_ENTREGADO, PDO::PARAM_STR);
            $consulta->bindValue(':fechaEntrega', $fechaEntrega, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
        }

        public static function AddImagen($codigo,$imagen,$destino)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedidos SET imagen = :imagen, destino = :destino WHERE codigo = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':imagen', $imagen, PDO::PARAM_LOB);
            $consulta->bindValue(':destino', $destino, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
        }

        public function CalcularDemora()
        {
            //Ejemplo
            //tomo pedido 17:50
            //demora 10 minutos
            //Hora actual 17:57
            //Esto tiene que ser 3 minutos
            //(la hora actual - (la hora que el pedido + 10 minutos)) 

            $retorno = "indefinido";

            if($this->tiempoDemora > 0)
            {
                $retorno = "00:00";
                // Fecha inicio
                $fechaInicio = new DateTime($this->fechaInicio);
                // Obtener la fecha actual
                $fechaActual = new DateTime();
                // Sumar minutos
                $fechaSumada = clone $fechaInicio;
                $tiempo = "PT".$this->tiempoDemora."M";
                $fechaSumada->add(new DateInterval($tiempo));
    
                if($fechaActual < $fechaSumada)
                {
                    // Restar la fecha sumada a la fecha actual
                    $diferencia = $fechaActual->diff($fechaSumada);
                    $retorno = $diferencia->format('%I:%S');
                }
                 
            }
            // Devolver la diferencia
            return $retorno;

        }

        public static function ListarCalculandoDemora($arrayPedidos)
        {
            if(is_array($arrayPedidos))
            {
                foreach ($arrayPedidos as $value) 
                {
                    if($value instanceof Pedido)
                    {
                        if($value->estado === self::ESTADO_PREPARACION)
                            $value->tiempoDemora = $value->CalcularDemora();
                        else
                            $value->tiempoDemora = "00:00";
                    }
                }
            }
            return $arrayPedidos;
        }

    }

?>