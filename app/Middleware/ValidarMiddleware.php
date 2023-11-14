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

            $response = new Response();

            if(isset($prt['id_usuario']) && isset($prt['id_mesa']) && isset($prt['nombre_cliente']) && isset($prt['id_producto']) && isset($prt['cantidad']))
            {
                
                $ids_productos = explode(',',$prt['id_producto']);
                $cantidades = explode(',',$prt['cantidad']);
                
                if(count($ids_productos) === count($cantidades))
                {
                    $response = $handler->handle($request); 
                }
                else
                    $msj = "Error! la cantidad de ids_productos con las cantidades no coincide";

            }
            else
                $msj = "Error! no estan seteados todos los parametros";
            
            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function VerificarParametrosPedido($request, $handler)
        {
            $prt = $request->getParsedBody();
            $response = new Response();
            $flag = false;
            
            $arrayIdsProductos = explode(',',$prt['id_producto']);
            $cantidades = explode(',',$prt['cantidad']);

            if (Mesa::TraerUnaMesa($prt['id_mesa']))
            {
                if(Usuario::TraerUnUsuario($prt['id_usuario']))
                {
                    foreach ($arrayIdsProductos as $value) 
                    {
                        $flag = Producto::TraerUnProducto($value);
                        if($flag)
                        {
                            $msj = "Error no existe un producto con el id: ".$value;
                            break;
                        }
                    }

                }
                else
                    $msj = "Error no existe una usuario con ese id!";
            }
            else
                $msj = "Error no existe una mesa con ese id!";
            
            if($flag)
            {
                $request->IdsProductos = $arrayIdsProductos;
                $request->cantidades = $cantidades;
                $response = $handler->handle($request); 
            }

            if(isset($msj))
            {
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response->withHeader('Content-Type', 'application/json');
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

            return $response->withHeader('Content-Type', 'application/json');
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

            return $response->withHeader('Content-Type', 'application/json');
        }

    }


?>