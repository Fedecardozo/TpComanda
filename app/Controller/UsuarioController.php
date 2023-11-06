<?php

    require_once "./Objetos/Usuario.php";
    require_once "./Interfaces/Icrud.php";

    class UsuarioController extends Usuario implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $nombre = $parametros['nombre'];
            $apellido = $parametros['apellido'];
            $dni = $parametros['dni'];
            $puesto = $parametros['puesto'];
            $estado = $parametros['estado'];

            // Creamos el usuario
            $usr = new Usuario();
            $usr->nombre = $nombre;
            $usr->apellido = $apellido;
            $usr->dni = $dni;
            $usr->puesto = $puesto;
            $usr->estado = $estado;
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

    }

?>