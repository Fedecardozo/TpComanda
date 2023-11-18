<?php   

    require_once "./Objetos/Encuesta.php";

    class EncuestaController extends Encuesta // implements Icrud
    {
        public function CargarUno($request, $response, $args)
        {

            // Creamos el Mesa
            $encuesta = new Encuesta();
            $encuesta->mesa = $request->getAttribute('punt_mesa');
            $encuesta->restaurante = $request->getAttribute('punt_restaurante');
            $encuesta->mozo= $request->getAttribute('punt_mozo');
            $encuesta->cocinero= $request->getAttribute('punt_cocinero');
            $encuesta->estrellas= $request->getAttribute('estrellas');
            $encuesta->tipo = $request->getAttribute('tipo');
            $encuesta->texto= $request->getAttribute('texto');
            $encuesta->codigo_pedido= $request->getAttribute('codigo_pedido');
            $encuesta->codigo_mesa= $request->getAttribute('codigo_mesa');

            $msj = $encuesta->CrearEncuesta() ? "Encuesta creado con exito" : "No se pudo crear la encuesta ";

            $payload = json_encode(array("mensaje" => $msj));
            $response->getBody()->write($payload);

            return $response;
        }

        public function TraerMejoresComentarios($request, $response, $args)
        {
            $lista = Encuesta::TraerEncuestas(Encuesta::TIPO_BUENA,3,5);
            $payload = json_encode(array("MejoresComentarios" => $lista));

            $response->getBody()->write($payload);
            return $response;
        }

    }

?>