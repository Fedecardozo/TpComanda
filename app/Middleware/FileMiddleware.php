<?php

    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;

    class FileMiddleware
    {
        /*//Encuesta,mesa,pedido,producto,usuario
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
        }*/

        public static function ValidarArchivo(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $destino = "./ArchivoCsv/productos.csv";
            $request = $request->withAttribute('destino',$destino);
            $files = $request->getUploadedFiles();   
            
            if(isset($files['csv']))
            {
                $request = $request->withAttribute('archivo',$files['csv']);
                $response = $handler->handle($request);
            }   
            else
            {
                $msj = "error falta el archivo";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }

        public static function ValidarTipoArchivo(Request $request, RequestHandler $handler)
        {
            $response = new Response();
            $archivo = $request->getAttribute('archivo');
            $type = $archivo->getClientMediaType();
            $tipoCsv = explode('/',$type)[1];

            if($tipoCsv === 'csv')
            {
                $response = $handler->handle($request);
            }   
            else
            {
                $msj = "No es un archivo valido";
                $payload = json_encode(array("mensaje" => $msj));
                $response->getBody()->write($payload);  
            }
            
            return $response;
        }
    }


?>