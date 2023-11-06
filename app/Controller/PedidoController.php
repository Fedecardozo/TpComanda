<?php

    require_once "./Objetos/Pedido.php";
    require_once "./Interfaces/Icrud.php";

    class PedidoController extends Pedido implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $id_usuario = $parametros['id_usuario'];
            $id_mesa = $parametros['id_mesa'];

            // Creamos el Pedido
            $prd = new Pedido();
            $prd->id_usuario = $id_usuario;
            $prd->id_mesa = $id_mesa;

            $prd->crearPedido();

            $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Pedido::TraerPedidos();
            $payload = json_encode(array("listaPedido" => $lista));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

    }

?>