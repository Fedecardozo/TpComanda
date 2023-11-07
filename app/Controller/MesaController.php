<?php

    require_once "./Objetos/Mesa.php";
    require_once "./Interfaces/Icrud.php";

    class MesaController extends Mesa implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $nombre = $parametros['nombreCliente'];

            // Creamos el Mesa
            $mesa = new Mesa();
            $mesa->codigo = $mesa->GenerarCodigoAlfanumerico();
            $mesa->estado = Mesa::ESTADO_CERRADA;
            $mesa->nombreCliente = $nombre;

            $mesa->crearMesa();

            $payload = json_encode(array("mensaje" => "Mesa creado con exito"));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Mesa::TraerMesas();
            $payload = json_encode(array("listaMesa" => $lista));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

    }

?>