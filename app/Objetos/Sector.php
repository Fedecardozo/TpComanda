<?php

    include_once "./BaseDatos/accesoDatos.php";
    require_once "./Objetos/Pedido.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Producto.php";

    class Sector
    {
        public const ID_COCINA = 1;
        public const ID_BARRA_DE_TRAGOS = 2;
        public const ID_BARRA_CHOPERAS = 3;
        public const ID_CANDY_BAR = 4;

        public $id;
        public $nombre_sector;

        public static function TraerUnSector($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id,nombre_sector FROM sector WHERE id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchObject("Sector");            
        }
        
        public function __get($name)
        {
            switch ($name) 
            {
                case 'PedidosPendientes':
                    //Obtener pedidos pendientes un array
                    $retorno = array();
                    $pedidos = Pedido::TraerUnPedidoPorEstado(Pedido::ESTADO_PREPARACION);

                    foreach ($pedidos as $value) 
                    {
                        $producto = Producto::TraerUnProducto($value->id_producto);
                        if($producto->tipo == $this->TipoProducto && $value->estado == Pedido::ESTADO_PREPARACION)
                        {
                            array_push($retorno,$value);
                        }   
                    }
                    return $retorno;
                    break;

                case 'Cantidad_pendientes':
                    return count($this->PedidosPendientes);
                    break;
                
                case 'Cantidad_Empleados':
                    //Buscar por sector la cantidad de empleados activo
                    $retorno = array();
                    $usuarios = Usuario::TraerUsuarios();
                    foreach ($usuarios as $value) 
                    {
                        if($value->puesto == $this->TipoEmpleado && $value->estado == Usuario::ESTADO_ACTIVO)
                        {
                            array_push($retorno,$value);
                        }
                    }
                    return count($retorno);
                    break;
                
                case 'TipoProducto':
                    switch ($this->id) 
                    {
                        case self::ID_COCINA:
                            return Producto::TIPO_COMIDA;
                            break;
                        case self::ID_CANDY_BAR:
                            return Producto::TIPO_POSTRE;
                            break;
                        case self::ID_BARRA_CHOPERAS:
                            return Producto::TIPO_CERVEZA;
                            break;
                        case self::ID_BARRA_DE_TRAGOS:
                            return Producto::TIPO_TRAGO_VINO;
                            break;
                    }
                    break;
                
                case 'TipoEmpleado':
                    switch ($this->id) 
                    {
                        case self::ID_COCINA:
                            return Usuario::PUESTO_COCINERO;
                            break;
                        case self::ID_CANDY_BAR:
                            return Usuario::PUESTO_COCINERO_CANDY;
                            break;
                        case self::ID_BARRA_CHOPERAS:
                            return Usuario::PUESTO_CERVECERO;
                            break;
                        case self::ID_BARRA_DE_TRAGOS:
                            return Usuario::PUESTO_BARTENDER;
                            break;
                    }
                    break;

                case 'TiempoPorSector':
                    switch ($this->id) 
                    {
                        case self::ID_COCINA:
                            return 15;
                            break;
                        case self::ID_CANDY_BAR:
                            return 7;
                            break;
                        case self::ID_BARRA_CHOPERAS:
                            return 5;
                            break;
                        case self::ID_BARRA_DE_TRAGOS:
                            return 10;
                            break;
                    }
                    break;
            }
    
        }
        
        public function CalcularTiempoEstimadoPedido()
        {
            //No es lo mismo que este un empleado a que esten 3 o más
            $promedioTiempo = $this->TiempoPorSector/$this->Cantidad_Empleados;

            //Obtengo la fecha actual
            $fechaActual =  DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

            //Creo el nuevo tiempo para el proximo pedido
            $newTiempo = (int)$promedioTiempo;

            //Array de pendientes
            $ultimoPendiente = $this->PedidosPendientes;
            if(!empty($ultimoPendiente)) // No esta vacio
            {
                //Ultimo pedido pendiente
                $ultimoPendiente = end($ultimoPendiente);

                //Como no esta vacio busco la fecha de entrega
                $fechaEntrega = $ultimoPendiente->fechaEntrega;

                //Formateo la fecha para que sea un objeto DateTime
                $fechaEntrega = DateTime::createFromFormat('Y-m-d H:i:s', $fechaEntrega);

                //Tiempo restante del ultimo pedido con la fecha actual
                $tiempoRestante = ($fechaEntrega->diff($fechaActual))->format('%i');
                
                //Agrego a la fecha actual el promedio estimado
                $fechaActual = $fechaEntrega;

                //Creo el nuevo tiempo para el proximo pedido
                $newTiempo += doubleval($tiempoRestante);
            }
            $strTime = '+'.$newTiempo.' minutes';
            //Retorno la fecha de entrega estimada del pedido
            return ($fechaActual->modify($strTime))->format('Y-m-d H:i:s');
        }

    }

?>