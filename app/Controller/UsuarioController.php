<?php

    require_once "./Objetos/Usuario.php";
    require_once "./Interfaces/Icrud.php";
    require_once "./utils/AutentificadorJWT.php";

    class UsuarioController extends Usuario implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $nombre = $parametros['nombre'];
            $dni = $parametros['dni'];
            $puesto = $parametros['puesto'];

            // Creamos el usuario
            $usr = new Usuario();
            $usr->nombre = $nombre;
            $usr->fechaAlta = date("Y-m-d H:i:s");
            $usr->dni = $dni;
            $usr->puesto = $puesto;
            $usr->estado = Usuario::ESTADO_ACTIVO;
            $usr->crearUsuario();

            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Usuario::TraerUsuarios();
            $payload = json_encode(array("listaUsuario" => $lista));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];

            $usuario = Usuario::TraerUnUsuario($id);
            $payload = json_encode(array("usuario" => $usuario));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function CrearToken($request, $response, $args)
        {
            $datos = $request->getAttribute('user');//obtengo los datos del middleware
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));

            $response->getBody()->write($payload);
            return $response;
            
        }
    }

?>