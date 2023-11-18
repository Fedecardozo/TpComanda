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

        public function BorrarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];

            $usuario = Usuario::BorrarUnUsuario($id);
            $msj = $usuario ? "Se elimino con exito" : "No se pudo eliminar";
            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response;
        }

        public function ModificarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $nombre = $parametros['nombre'];
            $dni = $parametros['dni'];
            $puesto = $parametros['puesto'];
            $id = $parametros['id'];

            $usuario = Usuario::ModificarUnUsuario($nombre,$dni,$puesto,$id);
            $msj = $usuario ? "Se modifico con exito" : "El dni ya existe";
            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response;
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