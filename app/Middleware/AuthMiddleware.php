<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once "./utils/AutentificadorJWT.php";
require_once "./Objetos/Usuario.php";


class AuthMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        return self::VerificarToken($request,$handler);
    }

    public static function VerificarToken(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarLogin(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $dni = $parametros['dni'];

        $user= Usuario::TraerUnUsuarioPorNombreDni($dni,$nombre);
        
        if($user)
        {
            $datos = array('nombre' => $user->nombre, 'puesto' => $user->puesto);
            $request = $request->withAttribute('datos', $datos);
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array('error' => 'Nombre o dni incorrectos'));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}