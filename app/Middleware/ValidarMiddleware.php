<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Producto.php";
    require_once "./Objetos/Detalle.php";
    require_once "./Objetos/Sector.php";


    class ValidarMiddleware
    {

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

            if (Mesa::TraerUnaMesa($id_mesa))
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

        public static function ReturnContentJson(Request $request, RequestHandler $handler) 
        {
            $response = $handler->handle($request);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function ValidarUpdateDetalles(Request $request, RequestHandler $handler)
        {
            $parametros = $request->getParsedBody();
            $response = new Response();
            $esvalid = false;

            if(isset($parametros['id_detalle']))
            {
                $id_detalle = $parametros['id_detalle'];
                $usuario = $request->getAttribute('usuario');
                switch ($usuario->puesto) 
                {
                    case Usuario::PUESTO_ADMIN:
                        
                        break;
                    case Usuario::PUESTO_MOZO:
                        
                        break;
                    case Usuario::PUESTO_SOCIO:
                        
                        break;
                    default:
                        $esvalid = Detalle::TraerDetalle_Id_sector($id_detalle,$usuario->IdSector);
                        break;
                }
            }
            else
                $msj = "Falta el parametro id_detalle";

            if($esvalid instanceof Detalle)
            {
                $request = $request->withAttribute('estado_detalle',$esvalid->estado);
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

            if($isValid)
            {
                $estado = ucfirst(strtolower($parametros["estado"])); //Capital case
                switch ($estado) 
                {
                    case Pedido::ESTADO_CANCELADO: 
                        $estado = Pedido::ESTADO_CANCELADO; 
                        if($estado_detalle === Pedido::ESTADO_ENTREGADO || $estado_detalle === Pedido::ESTADO_CANCELADO) 
                        {
                            //Al setear el msj, no es necesario poner que isvalid es false
                            $msj = "No se puede cancelar un pedido que fue ".$estado_detalle;
                        }
                        break;
                    case Pedido::ESTADO_ENTREGADO: 
                        $estado = Pedido::ESTADO_ENTREGADO; 
                        if($estado_detalle === Pedido::ESTADO_ENTREGADO || $estado_detalle === Pedido::ESTADO_CANCELADO)
                        {
                            $msj = "No se puede entregar un pedido que ya fue ".$estado_detalle;
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
                    default: $msj = "Estado invalido"; $isValid = false; break;
                }
            }
            else
            {
                $msj = "Falta el parametro estado";
            }
            
            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload); 
            }
            else if($isValid)
            {
                $request = $request->withAttribute('estado',$estado);
                $response = $handler->handle($request);
            }

            return $response;
        }
    }


?>