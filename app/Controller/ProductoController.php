<?php

    require_once "./Objetos/Producto.php";
    require_once "./Interfaces/Icrud.php";

    class ProductoController extends Producto implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();

            $nombre = $parametros['nombre'];
            $tipo = $parametros['tipo'];
            $precio = $parametros['precio'];

            // Creamos el Producto
            $prd = new Producto();
            $prd->nombre = $nombre;
            $prd->tipo = $tipo;
            $prd->precio = $precio;
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