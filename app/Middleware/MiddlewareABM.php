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

    }


?>