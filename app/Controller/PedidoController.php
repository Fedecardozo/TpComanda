<?php

    require_once "./Objetos/Pedido.php";
    require_once "./Objetos/Mesa.php";
    require_once "./Objetos/Usuario.php";
    require_once "./Objetos/Sector.php";
    require_once "./Objetos/Detalle.php";
    require_once "./Interfaces/Icrud.php";

    class PedidoController extends Pedido implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {
            //Obtener los datos del request
            $id_mesa = $request->getAttribute('id_mesa');
            $productos = $request->getAttribute('productos');
            $cantidades = $request->getAttribute('cantidades');
            $cliente = $request->getAttribute('cliente');
            $usuario = $request->getAttribute('usuario');

            //Creo el Pedido
            $pedido = new Pedido();
            $pedido->id_mesa = $id_mesa;
            $pedido->id_usuario = $usuario->id;
            $pedido->codigo = Mesa::GenerarCodigoAlfanumerico();
            $pedido->estado = Pedido::ESTADO_PREPARACION; 
            $pedido->fechaInicio = date("Y-m-d H:i:s");
            
            $id_pedido = $pedido->CrearPedido(); 
            $msj = "No se pudo crear el pedido";
            $flag = false;

            if($id_pedido)
            {
                $detalle = new Detalle();
                foreach ($productos as $key => $value) 
                {
                    $detalle->id_producto = $value->id;
                    $detalle->cantidad = $cantidades[$key];
                    $detalle->id_pedido = $id_pedido;
                    $detalle->id_sector = $value->SectorID;
    
                    Mesa::ModificarMesa($id_mesa,Mesa::ESTADO_ESPERANDO,$cliente);
                    $flag = $detalle->CrearDetalle();
                }

            }
            
            $msj = $flag ? "Pedido creado con exito" : "Hubo un error al cargar el detalle";

            $payload = json_encode(array("mensaje" => $msj));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Pedido::TraerPedidos();
            $payload = json_encode(array("listaPedido" => $lista));

            $response->getBody()->write($payload);
            return $response;
        }

        public function TraerUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];

            $pedido = Pedido::TraerUnpedido($id);
            $payload = json_encode(array("Pedido" => $pedido));

            $response->getBody()->write($payload);
            return $response;
        }

        public function AgregarUnaFoto($request, $response, $args)
        {
            $files = $request->getUploadedFiles();   
            $parametros = $request->getParsedBody(); 
            $codigo = $parametros["codigo"];

            $msj = "No adjunto la imagen";
            if(isset($files["foto"]))
            {
                $msj = "No se encontro el codigo ingresado!";
                if(Pedido::TraerUnPedido($codigo))
                {
                    $foto = $files["foto"];
                    $destino = "./ImgPedidos/".$codigo.".jpg";
                    $foto->moveTo($destino);
                    $msj = "Se guardo con exito la foto!";
                }
            }
            
            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);
            
            return  $response; 
        }

        //Login
        //Necesito un logger de usuarios
        //Con JWT y cada usuario va a tener accesso a cada informacion y accion que le corresponda
        //Se le va a registringir algunos sectores del programa, dependiendo del usuario que inicio sesion
        //Validarlo con middleware


        //Preguntar como generar el pedido
        //Si se puede generar con JWT (Creo que no)
        //Opcion 1, generar una peticion Pedir(id_usuario,id_producto,cantidad,codigo);
        //Opcion 2, pasar un array json con el id del producto y cantidad;

        //Averigurar como hacer para saber que el pedido este listo
        
    }

?>