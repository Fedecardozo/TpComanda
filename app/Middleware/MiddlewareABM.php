<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;

    class MiddlewareABM
    {

        //Usuario Id
        public static function IssetParametrosIdUsuario(Request $request, RequestHandler $handler)
        {
            $prt = $request->getParsedBody();

            $response = new Response();

            if(isset($prt['id']))
            {
                $request = $request->withAttribute('id_usuario',$prt['id']);
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "Error! no esta seteado el parametro Id";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }

            return $response;
        }

        //Usuario nombre dni puesto
        public static function IssetParametrosUsuario(Request $request, RequestHandler $handler)
        {
            $prt = $request->getParsedBody();

            $response = new Response();

            if(isset($prt['nombre']) && isset($prt['dni']) && isset($prt['puesto']))
            {
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "Error! no estan seteados todos los parametros (nombre,dni,puesto)";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        //Usuario traer 
        public static function IsUsuario(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $usuario = Usuario::TraerUnUsuario($request->getAttribute('id_usuario'));

            if($usuario instanceof Usuario)
            {
                $request = $request->withAttribute('usuario',$usuario);
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "El usuario no existe con ese id";
                $payload = json_encode(array("error" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        //Usuario verificar su estado sea activo (Baja logica)
        public static function UsuarioIsActivo(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $usuario = $request->getAttribute('usuario');

            if($usuario->estado === Usuario::ESTADO_ACTIVO)
            {
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "El usuario ya fue eliminado!";
                $payload = json_encode(array("error" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        //Pedido
        public static function IssetCodigoPedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $parametros = $request->getParsedBody();

            if(isset($parametros['codigo_pedido']))
            {
                $request = $request->withAttribute('codigo_pedido',$parametros['codigo_pedido']);
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "Le falto el codigo del pedido";
                $payload = json_encode(array("error" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        //Verificar pedido
        public static function IsPedido(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $codigo = $request->getAttribute('codigo_pedido');
            $pedido = Pedido::TraerUnPedido($codigo);

            if($pedido instanceof Pedido)
            {
                $request = $request->withAttribute('pedido',$pedido);
                $response = $handler->handle($request); 
            }
            else
            {
                $msj = "El pedido no existe";
                $payload = json_encode(array("error" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        //Verificar estado pedido
        public static function IsPedidoCancelado(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $pedido = $request->getAttribute('pedido');

            if($pedido->estado === Pedido::ESTADO_LISTO ||
               $pedido->estado === Pedido::ESTADO_PREPARACION)
            {
                $response = $handler->handle($request); 
                //Despues de que se cancelo el pedido, hay que cancelar los detalles
                Detalle::ModificarEstadoTodos($pedido->id,Pedido::ESTADO_CANCELADO);
            }
            else
            {
                $msj = "No se puede cancelar. El pedido ya fue ".$pedido->estado;
                $payload = json_encode(array("error" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

    }


?>