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
            $datos = array('dni' => $user->dni, 'nombre'=> $user->nombre, 'puesto'=>$user->puesto);
            $request = $request->withAttribute('user', $datos);
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array('error' => 'Nombre o dni incorrectos'));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    private static function Verificar(Request $request, RequestHandler $handler, $call): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            
            AutentificadorJWT::verificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            $user= Usuario::TraerUnUsuarioPorNombreDni($data->dni,$data->nombre);
            if($call($data))
            {
                $request = $request->withAttribute('usuario', $user);
                $response = $handler->handle($request);
            }
            else
                throw new Exception("No es un usuario valido para realizar esta accion");     

            
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }

        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarMozo(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_MOZO;   
        });
    }

    public static function VerificarSocio(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_SOCIO;   
        });
    }

    public static function VerificarAdmin(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_ADMIN;   
        });
    }

    public static function VerificarBartender(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_BARTENDER;   
        });
    }

    public static function VerificarCervecero(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_CERVECERO;   
        });
    }

    public static function VerificarCandy(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_COCINERO_CANDY;   
        });
    }

    public static function VerificarCocinero(Request $request, RequestHandler $handler)
    {
        return self::Verificar($request,$handler,function($data){
            return $data->puesto === Usuario::PUESTO_COCINERO;   
        });
    }

    //mozo toma pedido ok
    //mozo saca foto ok
    //moza se fija los pedidos que estan listos para servir y cambia estado mesa -
    //moza cobra la cuenta -

    //cocinero candy - listar pedidos de candy pendientes -
    //cocinero - listar pedidos cocina pendientes -
    //bartender - listar pedidos tragos-vinos pendientes -
    //cervecero - cambiar el estado y agregarle tiempo de cervezar pendientes -
   
    //cocinero candy - cambiar el estado y agregarle tiempo de candy pendientes -
    //cocinero - cambiar el estado y agregarle tiempo cocina pendientes -
    //bartender - cambiar el estado y agregarle tiempo tragos-vinos pendientes -
    //cervecero - cambiar el estado y agregarle tiempo de cervezar pendientes -
 
    //cliente ingresa codigo mesa y codigo pedido y ve el tiempo de demora de su pedido -
    //cliente ingresa codigo mesa y codigo pedido junto con los datos de la encuesta -

    //socios listado pedidos y demora -
    //socios listado mesas y estados -
    //socios cerrar mesa -
    //socios mejores comentarios -
    //socios mesa mas usada -



}