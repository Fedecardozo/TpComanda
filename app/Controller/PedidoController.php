<?php

    require_once "./Objetos/Pedido.php";
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Sector.php";
    require_once "./Objetos/Detalle.php";
    require_once "./Interfaces/Icrud.php";

    class PedidoController extends Pedido implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            //Obtener los datos del request
            $id_mesa = $request->getAttribute('id_mesa');
            $productos = $request->getAttribute('productos');
            $cantidades = $request->getAttribute('cantidades');
            $cliente = $request->getAttribute('cliente');
            $usuario = $request->getAttribute('usuario');

            //Creo el Pedido
            $pedido = new Pedido();
            $pedido->id_mesa = $id_mesa;
            $pedido->id_usuario = $usuario->id;
            $pedido->codigo = Mesa::GenerarCodigoAlfanumerico();
            $pedido->estado = Pedido::ESTADO_PREPARACION; 
            $pedido->fechaInicio = date("Y-m-d H:i:s");
            
            $id_pedido = $pedido->CrearPedido(); 
            $msj = "No se pudo crear el pedido";
            $flag = false;

            if($id_pedido)
            {
                $detalle = new Detalle();
                foreach ($productos as $key => $value) 
                {
                    $detalle->id_producto = $value->id;
                    $detalle->cantidad = $cantidades[$key];
                    $detalle->id_pedido = $id_pedido;
                    $detalle->id_sector = $value->SectorID;
    
                    Mesa::ModificarMesa($id_mesa,Mesa::ESTADO_ESPERANDO,$pedido->codigo,$cliente);
                    $flag = $detalle->CrearDetalle();
                }

            }
            
            $msj = $flag ? "Pedido creado con exito" : "Hubo un error al cargar el detalle";

            $payload = json_encode(array("mensaje" => $msj, "codigo" => $pedido->codigo));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerTodos($request, $response, $args)
        {
            $listaPedidos = Pedido::TraerPedidos();
            $lista = Pedido::ListarCalculandoDemora($listaPedidos);

            $payload = json_encode(array("listaPedido" => $listaPedidos));
            $payload = json_encode(array("listaPedido" => $lista));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerUno($request, $response, $args)
        {
            $pedido = $request->getAttribute('pedido');
            
            $payload = json_encode($pedido);
            $response->getBody()->write($payload); 

            return $response;
        }

        public function BorrarUno($request, $response, $args)
        {
            $pedido = $request->getAttribute('pedido');

            $valid = Pedido::CambiarEstadoPedido($pedido->id,Pedido::ESTADO_CANCELADO);
            $msj = $valid ? "Se cancelo con exito" : "No se pudo cancelar";
            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response;
        }

        public function AgregarUnaFoto($request, $response, $args)
        {
            $files = $request->getUploadedFiles();   
            $pedido = $request->getAttribute('pedido');

            $foto = $files["foto"];
            $destino = "./ImgPedidos/".$pedido->codigo.".jpg";
            $foto->moveTo($destino);
            $imagen = file_get_contents($destino);

            $msj = Pedido::AddImagen($pedido->codigo,$imagen,$destino) ? "Se guardo con exito la foto!" : "Error al guardar la foto";
                  
            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);
            
            return  $response; 
        }

        public static function AsignarDuracion($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id_detalle = $parametros['id_detalle'];
            $duracion = $parametros['duracion'];
            $estado = Pedido::ESTADO_PREPARACION;

            $msj = Detalle::AddDuracion($id_detalle,$duracion,$estado) ? "Se agrego la duracion exitosamente!" : "Hubo un error al cargar la duracion!";                  

            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);

            return $response;
        }

        public static function CambiarEstadoDetalle($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id_detalle = $parametros['id_detalle'];
            $estado = $request->getAttribute('estado');

            $msj = Detalle::ModificarEstado($id_detalle,$estado) ? "Se cambio el estado exitosamente!" : "Hubo un error al cambiar el estado!";                  

            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);

            return $response;
        }

        public static function ListarPedidosPendientes($request, $response, $args)
        {
            $usuario = $request->getAttribute('usuario');
            $id_sector = $usuario->IdSector;

            $msj = Detalle::TraerDetallesPorEstado($id_sector,Pedido::ESTADO_PREPARACION);

            $msj = count($msj) ? $msj : array("mensaje"=>"No hay pendientes");                  

            $payload = json_encode($msj);
            $response->getBody()->write($payload);

            return $response;
        }

        public static function ListarDetallesListos($request, $response, $args)
        {
            $msj = Detalle::TraerDetallesSoloPorEstado(Pedido::ESTADO_LISTO);

            $msj = count($msj) ? $msj : array("mensaje"=>"No hay pedidos listos para servir");                  

            $payload = json_encode($msj);
            $response->getBody()->write($payload);

            return $response;
        }

        public static function ListarPedidosListos($request, $response, $args)
        {
            $msj = Pedido::TraerPedidosPorEstado(Pedido::ESTADO_LISTO);

            $msj = count($msj) ? $msj : array("mensaje"=>"No hay pedidos listos para servir");                  

            $payload = json_encode($msj);
            $response->getBody()->write($payload);

            return $response;
        }
        
    }

?>