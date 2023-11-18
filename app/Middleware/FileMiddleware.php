<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;

    class FileMiddleware
    {
        //Encuesta,mesa,pedido,producto,usuario
        public static function IssetNombre(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $metodo = $request->getMethod();
            if($metodo === 'GET')
            {
                $parametros = $request->getQueryParams();
            }
            else
            {
                $parametros = $request->getParsedBody();
            }
            
            if(isset($parametros['nombre']))
            {
                $nombre = strtolower($parametros['nombre']);
                $request = $request->withAttribute('nombre',$nombre);
                $response = $handler->handle($request);
            }
            else 
            {
                $msj = "No esta seteado el nombre del archivo";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            

            return $response;
        }

        public static function ValidarNombre(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $nombre = $request->getAttribute('nombre');
            
            if($nombre === 'encuestas' || $nombre === 'mesas' || $nombre === 'pedidos' || $nombre === 'productos' || $nombre === 'usuarios')
            {
                $destino = "./ArchivoCsv/$nombre.csv";
                $request = $request->withAttribute('destino',$destino);
                $response = $handler->handle($request);
            }   
            else
            {
                $msj = "Nombre del archivo invalido";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            

            return $response;
        }

    }


?>