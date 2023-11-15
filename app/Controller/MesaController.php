<?php

    require_once "./Objetos/Mesa.php";
    require_once "./Interfaces/Icrud.php";

    class MesaController extends Mesa implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            // $parametros = $request->getParsedBody();

            // Creamos el Mesa
            $mesa = new Mesa();
            $mesa->estado = Mesa::ESTADO_CERRADA;
            $mesa->codigo = Mesa::GenerarCodigoAlfanumerico();

            $mesa->crearMesa();

            $payload = json_encode(array("mensaje" => "Mesa creado con exito"));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Mesa::ListarMesas();
            $payload = json_encode(array("listaMesa" => $lista));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];

            $mesa = Mesa::TraerUnaMesa($id);
            $payload = json_encode(array("Mesa" => $mesa));

            $response->getBody()->write($payload);
            return $response;
        }

        public function ModificarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $codigo = $parametros['codigo_mesa'];
            $estado = $parametros['estado'];

            $msj = Mesa::ModificarEstadoMesa($codigo, $estado) ? "Se cambio el estado exitosamente" : "No se pudo cambiar el estado" ;
            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response;
        }
    }

?>