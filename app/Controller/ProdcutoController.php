<?php

    require_once "./Objetos/Producto.php";
    require_once "./Interfaces/Icrud.php";

    class ProductoController extends Producto implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $id_usuario = $parametros['id_usuario'];
            $id_pedido = $parametros['id_pedido'];
            $nombre = $parametros['nombre'];
            $tipo = $parametros['tipo$tipo'];
            $precio = $parametros['precio$precio'];
            $stock = $parametros['stock$stock'];

            // Creamos el Producto
            $prd = new Producto();
            $prd->id_usuario = $id_usuario;
            $prd->id_pedido = $id_pedido;
            $prd->nombre = $nombre;
            $prd->tipo = $tipo;
            $prd->precio = $precio;
            $prd->stock = $stock;
            $prd->crearProducto();

            $payload = json_encode(array("mensaje" => "Producto creado con exito"));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Producto::TraerProductos();
            $payload = json_encode(array("listaProducto" => $lista));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

    }

?>