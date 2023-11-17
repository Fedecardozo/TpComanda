<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Producto.php";
    require_once "./Objetos/Detalle.php";
    require_once "./Objetos/Sector.php";
    require_once "./Objetos/Pedido.php";
    require_once "./Objetos/Cuenta.php";


    class ValidarMiddleware
    {
        // Pedidos
        public static function IssetParametrosPedido($request, $handler)
        {
            $prt = $request->getParsedBody();
            $pedido = $prt["pedido"];

            $response = new Response();

            if(isset($pedido['id_mesa']) && isset($pedido['nombreCliente']))
            {
                $request = $request->withAttribute('id_mesa',$pedido['id_mesa']);
                $request = $request->withAttribute('cliente',$pedido['nombreCliente']);
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "Error! no estan seteados todos los parametros (id_mesa,nombre_cliente)";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response;
        }

        public static function VerificarParametrosPedido($request, $handler)
        {
            $prt = $request->getParsedBody();
            $id_mesa = $request->getAttribute('id_mesa');
            $response = new Response();
            $productos = array();
            $cantidades = array();

            if (Mesa::TraerUnaMesaId($id_mesa))
            {
                if(isset($prt['productos']) && is_array($prt['productos']))
                {
                    foreach ($prt['productos'] as $value) 
                    {
                        $prd = Producto::TraerUnProducto($value['id_producto']);
                        if($prd)
                        {
                            array_push($productos,$prd);
                            array_push($cantidades,$value['cantidad']);
                        }
                        else
                        {
                            $msj = "Error no existe un producto con el id: ".$value['id_producto'];
                            break;
                        }
                    }
                }
            }
            else
                $msj = "Error no existe una mesa con ese id!";
            

            
            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            else
            {
                $request = $request->withAttribute('productos',$productos);
                $request = $request->withAttribute('cantidades',$cantidades);
                $response = $handler->handle($request); 
            }

            return $response;
        }

        //Usuario
        public static function IssetParametrosUsuario($request, $handler)
        {
            $prt = $request->getParsedBody();

            $response = new Response();

            if(isset($prt['nombre']) && isset($prt['dni']) && isset($prt['puesto']))
            {
                $response = $handler->handle($request); 
            }
            else
                $msj = "Error! no estan seteados todos los parametros (nombre,dni,puesto)";
            
            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response;
        }

        //Productos
         public static function IssetParametrosProducto($request, $handler)
        {
            $prt = $request->getParsedBody();

            $response = new Response();

            if(isset($prt['nombre']) && isset($prt['tipo']) && isset($prt['precio']))
            {
                $response = $handler->handle($request); 
            }
            else
                $msj = "Error! no estan seteados todos los parametros (nombre,tipo,precio)";
            
            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response;
        }

        //Global
        public static function ReturnContentJson(Request $request, RequestHandler $handler) 
        {
            $response = $handler->handle($request);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Pedidos - Detalles
        public static function ValidarUpdateDetalles(Request $request, RequestHandler $handler)
        {
            $parametros = $request->getParsedBody();
            $response = new Response();
            $esvalid = false;

            if(isset($parametros['id_detalle']))
            {
                $id_detalle = $parametros['id_detalle'];
                $usuario = $request->getAttribute('usuario');
                $esvalid = Detalle::TraerDetalle_Id_sector($id_detalle,$usuario->IdSector);
               
            }
            else
                $msj = "Falta el parametro id_detalle";

            if($esvalid instanceof Detalle)
            {
                $request = $request->withAttribute('estado_detalle',$esvalid->estado);
                $request = $request->withAttribute('id_pedido',$esvalid->id_pedido);
                $response = $handler->handle($request);
            }
            else
            {
                $msj = isset($msj) ? $msj : "El id no existe o no pertence a tu sector!";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload); 
            }
            
            return $response;
        }

        public static function ValidarDuracion(Request $request, RequestHandler $handler)
        {
            $parametros = $request->getParsedBody();
            $response = new Response();

            if(!isset($parametros["duracion"]))
            {
                $msj = "No esta la duracion";
            }
            else if(!is_numeric($parametros["duracion"]))
            {
                $msj = "No es una duracion numerica";
            }
            else if(Detalle::IssetDuracion($parametros["id_detalle"]))
            {
                $msj = "El pedido ya tiene asignado una duracion";
            }
            else
            {
                $response = $handler->handle($request);
            }

            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload); 
            }

            return $response;
        }

        public static function ValidarEstadoPedido(Request $request, RequestHandler $handler)
        {
            $parametros = $request->getParsedBody();
            $response = new Response();
            $isValid = isset($parametros["estado"]);
            $estado_detalle = $request->getAttribute('estado_detalle');
            $id_pedido = $request->getAttribute('id_pedido');

            if($isValid)
            {
                $estado = ucfirst(strtolower($parametros["estado"])); //Capital case
                switch ($estado) 
                {
                    case Pedido::ESTADO_CANCELADO: 
                        if($estado_detalle === Pedido::ESTADO_ENTREGADO || $estado_detalle === Pedido::ESTADO_CANCELADO) 
                        {
                            //Al setear el msj, no es necesario poner que isvalid es false
                            $msj = "No se puede cancelar un pedido que fue ".$estado_detalle;
                        }
                        break;
                    case Pedido::ESTADO_LISTO: 
                        $estado = Pedido::ESTADO_LISTO; 
                        if($estado_detalle === Pedido::ESTADO_ENTREGADO || $estado_detalle === Pedido::ESTADO_CANCELADO)
                        {
                            //Al setear el msj, no es necesario poner que isvalid es false
                            $msj = "Un pedido que cancelado o entregado no puede estar listo para servir";
                        }
                        break;
                    default: 
                        $msj = "Los unicos estados que tenes permitido son ".Pedido::ESTADO_CANCELADO." y ".Pedido::ESTADO_LISTO; 
                        $isValid = false; 
                    break;
                }
            }
            else
            {
                $msj = "Falta el parametro estado";
            }
            
            if(isset($msj))
            {
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            else if($isValid)
            {
                $request = $request->withAttribute('estado',$estado);
                $response = $handler->handle($request);//todo ok
                //Despues de que hizo lo necesario verifico que si el pedido completo ya esta listo para servir
                if(Detalle::VerificarPedidoCompleto($id_pedido,Pedido::ESTADO_LISTO))
                {
                    //Cambio estado pedido
                    Pedido::CambiarEstadoPedido($id_pedido,Pedido::ESTADO_LISTO);
                }

            }

            return $response;
        }

        //Mesas
        public static function IssetUpdateMesas(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();

            if(isset($parametros['codigo_mesa']) && isset($parametros['estado']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "No estan seteados todos los parametros codigo_mesa y estado";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function VerificarEstadosUpdateMesas(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();
            $estado = Mesa::VerificarEstado($parametros['estado']);

            if($estado)
            {
                $request = $request->withAttribute('estado',$estado);
                $response = $handler->handle($request);//ok
            }
            else
            {
                $msj = "No existe ese estado";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function AccionEstadosUpdateMesas(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();
            $mesa = Mesa::TraerUnaMesa($parametros['codigo_mesa']);
            $usuario = $request->getAttribute('usuario');
            $estado = $request->getAttribute('estado');

            if($mesa instanceof Mesa)
            {
                if(($usuario->puesto === Usuario::PUESTO_MOZO &&
                    ($mesa->estado === Mesa::ESTADO_COMIENDO && $estado != Mesa::ESTADO_PAGANDO )||($mesa->estado === Mesa::ESTADO_ESPERANDO && $estado != Mesa::ESTADO_COMIENDO)
                    || ($mesa->estado === Mesa::ESTADO_PAGANDO && $estado ===Mesa::ESTADO_PAGANDO)) 
                                        || 
                    ($usuario->puesto === Usuario::PUESTO_SOCIO && 
                     $estado != Mesa::ESTADO_CERRADA ||
                    ($mesa->estado === Mesa::ESTADO_CERRADA && $estado === Mesa::ESTADO_CERRADA)))
                {
                    $msj = "No se puede cambiar el estado de ".$mesa->estado." a ".$estado;
                }
                else
                {
                    $request = $request->withAttribute('mesa',$mesa);
                    $response = $handler->handle($request);//ok
                }
            }
            else
            {
                $msj = "No tiene autorizacion para modificar con ese estado";
            }

            if(isset($msj))
            {
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function ValidarUpdateMesas(Request $request, RequestHandler $handler)
        {
            $parametros = $request->getParsedBody();
            $response = new Response();
            $mesa = $request->getAttribute('mesa');
            $estado = $request->getAttribute('estado');
 
            if(isset($mesa->codigo_pedido))
            {
                $response = $handler->handle($request);//ok
                $pedido = Pedido::TraerUnPedido($mesa->codigo_pedido);
                if($pedido instanceof Pedido)
                {
                    if($estado === Mesa::ESTADO_COMIENDO)
                    {
                        if(!(Pedido::CambiarFechaEstado($pedido->id,date("Y-m-d H:i:s")) && Detalle::ModificarEstadoTodos($pedido->id,Pedido::ESTADO_ENTREGADO)))
                        {
                            $msj = "Error al modificar el estado del pedido";
                        }         
                    }
                    else if($estado === Mesa::ESTADO_PAGANDO)
                    {
                        $cuenta = Cuenta::TraerCuentas($pedido->id);
                        array_push($cuenta,["PrecioFinal" => Cuenta::GetCuentaFinal($cuenta)]);
                        $payload = json_encode(["DetallePedido" => $cuenta]);
                        $response->getBody()->write($payload); 
                    }
                    else if($estado === Mesa::ESTADO_CERRADA)
                    {
                        //Si el estado es cerrada y paso es por que es un socio
                        //Entonces borro los datos de la mesa
                        Mesa::ModificarMesa($mesa->id,Mesa::ESTADO_CERRADA,null,null);

                        //si el pedido no fue entregado. Cancelo el pedido y los detalles
                        if( $pedido->estado != Pedido::ESTADO_ENTREGADO &&
                            (!Pedido::CambiarEstadoPedido($pedido->id,Pedido::ESTADO_CANCELADO) ||
                            !Detalle::ModificarEstadoTodos($pedido->id,Pedido::ESTADO_CANCELADO)))
                        {
                            $msj = "Error al modificar el estado del pedido";
                        }
                    }
                }
                else
                    $msj = "El pedido no existe!";
            }
            else
                $msj = "La mesa no tiene ningun pedido";

            
            if(isset($msj))
            {
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }

            return $response;
        }

        //Pedido-Imagen
        public static function IssetUpdateFotoPedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();
            $files = $request->getUploadedFiles();   

            if(isset($parametros['codigo']) && isset($files["foto"]))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "No estan seteados todos los parametros codigo y el archivo foto";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function VerificarPedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();
            
            $pedido = Pedido::TraerUnPedido($parametros['codigo']);
            
            if($pedido instanceof Pedido)
            {
                $request = $request->withAttribute('pedido',$pedido);
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "El pedido no existe";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function VerificarPedidoImagen(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $pedido = $request->getAttribute('pedido');
            
            if($pedido->estado === Pedido::ESTADO_CANCELADO)
            {
                $msj = "No se le puede agregar una imagen a un pedido Cancelado";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            else 
            {
                $response = $handler->handle($request);
                if(isset($pedido->imagen))
                {
                    $msj = "Al pedido se le reemplazo la imagen";
                    $payload = json_encode(array("Warning" => $msj));
                    $response->getBody()->write($payload); 
                }
            }
     
            return $response;
        }
        
        //Cliente su pedido
        public static function IssetClientePedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getQueryParams();
            
            if(isset($parametros['codigo_pedido']) && isset($parametros["codigo_mesa"]))
            {
                $request = $request->withAttribute('codigo_pedido',$parametros['codigo_pedido']);
                $request = $request->withAttribute('codigo_mesa',$parametros['codigo_mesa']);
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "No estan seteados todos los parametros codigo_pedido o el codigo_mesa";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }

            return $response;
        }

        public static function ValidarClienteParams(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $codigo_pedido = $request->getAttribute('codigo_pedido');
            $codigo_mesa = $request->getAttribute('codigo_mesa');

            if(strlen($codigo_pedido)==5 && strlen($codigo_mesa)==5)
            {
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "codigo de pedido o codigo de mesa invalido. alfanumerico de 5 caracteres";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }
            return $response;
        }

        public static function VerificarMesa(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $codigo_mesa = $request->getAttribute('codigo_mesa');
            
            $mesa = Mesa::TraerUnaMesa($codigo_mesa);
            if($mesa instanceof Mesa)
            {
                $request = $request->withAttribute('mesa',$mesa);
                $response = $handler->handle($request);
            }
            else
            {
                $msj = "No existe la mesa";
                $payload = json_encode(array("Error" => $msj));
                $response->getBody()->write($payload); 
            }

            return $response;
        }

        public static function VerificarClientePedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $codigo_pedido = $request->getAttribute('codigo_pedido');
            $mesa = $request->getAttribute('mesa');
            
            $pedido = Pedido::TraerUnPedido($codigo_pedido);
            if($pedido instanceof Pedido && $pedido->id_mesa === $mesa->id)
            {
                if($pedido->estado === Pedido::ESTADO_PREPARACION && 
                    $mesa->estado === Mesa::ESTADO_ESPERANDO)
                {
                    $request = $request->withAttribute('pedido',$pedido);
                    $response = $handler->handle($request);
                }
                else
                    $msj = "Su pedido esta ".$pedido->estado;         
            }
            else
                $msj = "No existe el pedido";
            
            if(isset($msj))
            {
                $payload = json_encode(array("mesaje" => $msj));
                $response->getBody()->write($payload); 
            }

            return $response;
        }

    }




?>