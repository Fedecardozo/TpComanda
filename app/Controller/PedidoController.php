<?php

    require_once "./Objetos/Pedido.php";
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Sector.php";
    require_once "./Interfaces/Icrud.php";

    class PedidoController extends Pedido implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $id_usuario = $parametros['id_usuario'];
            $id_mesa = $parametros['id_mesa'];
            $nombreCliente = $parametros['nombre_cliente'];
            $id_producto = $parametros['id_producto'];
            $cantidad = $parametros['cantidad'];

            $msj = "Error no existe una mesa con ese id!";

            if (Mesa::TraerUnaMesa($id_mesa))
            {
                $msj = "Error no existe una usuario con ese id!";

                if(Usuario::TraerUnUsuario($id_usuario))
                {
                    $msj = "Error no existe un producto con ese id!";
                    $producto = Producto::TraerUnProducto($id_producto);
                    if($producto)
                    {
                        //Traigo un sector relacionado con el producto
                        $sector = Sector::TraerUnSector($producto->SectorID);

                        //Creo el Pedido
                        $pedido = new Pedido();
                        $pedido->id_mesa = $id_mesa;
                        $pedido->id_usuario = $id_usuario;
                        $pedido->id_producto = $id_producto;
                        $pedido->codigo = Mesa::GenerarCodigoAlfanumerico();
                        $pedido->estado = Pedido::ESTADO_PREPARACION; 
                        $pedido->fechaInicio = date("Y-m-d H:i:s");
                        $pedido->fechaEntrega = $sector->CalcularTiempoEstimadoPedido();
                        $pedido->cantidad = $cantidad;
                        $msj = $sector->CalcularTiempoEstimadoPedido();
                        Mesa::ModificarMesa($id_mesa,Mesa::ESTADO_COMIENDO,$nombreCliente);
                        $msj = $pedido->crearPedido() ? "Pedido creado con exito" : "No se pudo crear el pedido";
                        
                    }
                    
                }
            }
            
            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Pedido::TraerPedidos();
            $payload = json_encode(array("listaPedido" => $lista));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];

            $pedido = Pedido::TraerUnpedido($id);
            $payload = json_encode(array("Pedido" => $pedido));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        //Mañana apenas me levante ok
        //Averiguar como calcular el tiempo de preparacion ok
        //Agregar foto averigurar como es eso
        //Averigurar como hacer para saber que el pedido este listo

    }

?>