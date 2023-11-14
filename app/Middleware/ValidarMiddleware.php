<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Producto.php";

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
            return $response;
        }

    }


?>